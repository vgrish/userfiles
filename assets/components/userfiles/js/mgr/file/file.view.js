userfiles.view.Files = function (config) {
	config = config || {};
	this._initTemplates();

	Ext.applyIf(config, {
		url: userfiles.config.connector_url,
		fields: [
			'id', 'parent', 'name', 'description', 'url', 'createdon', 'createdby', 'file', 'thumbnail', 'thumbnails',
			'size', 'format_size', 'source', 'source_name', 'type', 'mime', 'rank', 'active', 'properties', 'class',
			'cls', 'actions', 'dyn_thumbnail', 'dyn_url'
		],
		id: 'userfiles-view-files',
		cls: 'userfiles-files',
		baseParams: {
			action: 'mgr/file/getlist',
			parent: config.parent || 0,
			class: config.class || '',
			list: config.list || '',
			type: config.type || '',
			limit: config.pageSize || MODx.config.default_per_page
		},
		enableDD: true,
		multiSelect: true,
		tpl: this.templates.thumb,
		itemSelector: 'div.modx-browser-thumb-wrap',
		trackOver: true,
		listeners: {
			afterrender: function (grid) {
				var params = userfiles.tools.Hash.get();
				if (!!params['query']) {
					this.getStore().baseParams['query'] = params['query'];
					this.store.reload();
				}
			}
		},
		prepareData: this.formatData.createDelegate(this)
	});
	userfiles.view.Files.superclass.constructor.call(this, config);

	this.addEvents('sort', 'select');
	this.on('sort', this.onSort, this);
	this.on('dblclick', this.onDblClick, this);

	var self = this;
	this.getStore().on('beforeload', function () {
		var widget = self.getEl();
		if (widget) {
			widget.mask(_('loading'), 'x-mask-loading');
		}
	});
	this.getStore().on('load', function () {
		var widget = self.getEl();
		if (widget) {
			widget.unmask();
		}
	});

};
Ext.extend(userfiles.view.Files, MODx.DataView, {

	templates: {},
	windows: {},

	onSort: function (o) {
		var el = this.getEl();
		el.mask(_('loading'), 'x-mask-loading');
		MODx.Ajax.request({
			url: userfiles.config.connector_url,
			params: {
				action: 'mgr/file/sort',
				resource_id: this.config.resource_id,
				source: o.source.id,
				target: o.target.id
			},
			listeners: {
				success: {
					fn: function (r) {
						el.unmask();
						this.store.reload();

						/* product */
						userfiles.tools.updateProductThumb(r.object);
					},
					scope: this
				},
				failure: {
					fn: function () {
						el.unmask();
					},
					scope: this
				}
			}
		});
	},

	onDblClick: function (e) {
		var node = this.getSelectedNodes()[0];
		if (!node) {
			return;
		}

		this.cm.activeNode = node;
		this.fileUpdate(node, e);
	},

	setAction: function (method, field, value) {
		var ids = this._getSelectedIds();
		if (!ids.length && (field !== 'false')) {
			return false;
		}
		this.getEl().mask(_('loading'), 'x-mask-loading');
		MODx.Ajax.request({
			url: userfiles.config.connector_url,
			params: {
				action: 'mgr/file/multiple',
				method: method,
				field_name: field,
				field_value: value,
				ids: Ext.util.JSON.encode(ids)
			},
			listeners: {
				success: {
					fn: function (r) {
						this.store.reload();

						/* product */
						userfiles.tools.updateProductThumb(r.object);
					},
					scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					},
					scope: this
				},
			}
		})
	},

	fileShow: function (btn, e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}
		window.open(data.dyn_url);
	},

	fileEdit: function (btn, e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data || !data.dyn_url) {
			return;
		}

		var formData = new FormData();

		var xhr = new XMLHttpRequest();
		xhr.open('GET', data.dyn_url);
		xhr.overrideMimeType(data.mime + '; charset=x-user-defined');

		/* xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		 xhr.setRequestHeader('Content-Length', data.size);
		 xhr.setRequestHeader('Content-Type', data.mime);*/

		xhr.onload = function () {
			if (xhr.status === 200) {

				if (this.progress) {
					this.progress.hide();
				}

				MODx.load({
					xtype: 'userfiles-window-image-edit',
					record: data,
					listeners: {
						success: {
							fn: function (r) {
								var hash = userfiles.tools.Hash.get();
								this.store.load({
									params: {
										start: parseInt(hash['uf_start'] || 0),
										limit: parseInt(hash['uf_limit'] || MODx.config.default_per_page)
									}
								});

								/* product */
								userfiles.tools.updateProductThumb(r.object);
							},
							scope: this
						},
						failure: {
							fn: function (response) {
								MODx.msg.alert(_('error'), response.message);
							},
							scope: this
						}
					}
				}).show();
			}
		}.bind(this);

		xhr.onprogress = function (event) {
			if (event.lengthComputable) {
				this.progress.updateProgress(event.loaded / event.total);
			}
		}.bind(this);

		xhr.send(formData);
		this.progress = Ext.MessageBox.progress(_('please_wait'));

	},

	fileUpdate: function (btn, e) {
		var node = this.cm.activeNode;
		var data = this.lookup[node.id];
		if (!data) {
			return;
		}

		MODx.load({
			xtype: 'userfiles-window-file-update',
			record: data,
			listeners: {
				success: {
					fn: function (r) {

						var hash = userfiles.tools.Hash.get();
						this.store.load({
							params: {
								start: parseInt(hash['uf_start'] || 0),
								limit: parseInt(hash['uf_limit'] || MODx.config.default_per_page)
							}
						});

						/* product */
						userfiles.tools.updateProductThumb(r.a.result.object);

					},
					scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					},
					scope: this
				}
			}
		}).show();
	},

	fileRemove: function () {
		var ids = this._getSelectedIds();
		Ext.MessageBox.confirm(
			_('userfiles_action_remove'),
			_('userfiles_confirm_remove'),
			function (val) {
				if (val == 'yes') {
					this.setAction('remove');
				}
			},
			this
		);
	},

	fileTurnOn: function () {
		this.setAction('setproperty', 'active', 1);
	},

	fileTurnOff: function () {
		this.setAction('setproperty', 'active', 0);
	},

	thumbnailCreate: function () {
		this.setAction('thumbnail/create', 'false', 0);
	},

	formatData: function (data) {
		data.shortName = Ext.util.Format.ellipsis(data.name, 20);
		data.qtip = String.format('<img src={0}>', data.dyn_url);
		data.qtitle = String.format('{0} : {1} : {2}: {3}', data.name, data.class, data.parent, data.format_size);

		this.lookup['userfiles-resource-' + data.id] = data;
		return data;
	},

	_initTemplates: function () {
		this.templates.thumb = new Ext.XTemplate(
			'<tpl for=".">',
			'<div class="modx-browser-thumb-wrap modx-pb-thumb-wrap userfiles-thumb-wrap {cls}" id="userfiles-resource-{id}">',
			'<tpl if="dyn_thumbnail">',
			'<div class="modx-browser-thumb1 modx-pb-thumb userfiles-thumb">',
			'<img src="{dyn_thumbnail}" ext:qtip="{qtip}" ext:qtitle="{qtitle}" ext:qclass="userfiles-qtip"/>',
			'</div>',
			'</tpl>',
			'<tpl if="!thumbnail">',
			'<div class="modx-browser-thumb1 modx-pb-thumb userfiles-thumb">',
			'<span class="icon userfiles-icon userfiles-icon-{type} icon-3x" ext:qtip="{qtitle}" ></span>',
			'</div>',
			'</tpl>',
			'<small>{shortName}</small>',
			'</div>',
			'</tpl>'
		);

		this.templates.thumb.compile();
	},

	_showContextMenu: function (v, i, n, e) {
		e.preventDefault();
		var data = this.lookup[n.id];
		var m = this.cm;
		m.removeAll();

		var menu = userfiles.tools.getMenu(data.actions, this, this._getSelectedIds());
		menu.filter(function (item) {
			m.add(item);
		});

		var ids = this._getSelectedIds();
		var source = this.getStore().baseParams['source'] || userfiles.config.source || MODx.config.default_media_source;

		m.add('-', {
			text: _('userfiles_header_link') + _('userfiles_links'),
			menu: {
				items: []
			},
			data: data,
			listeners: {
				render: {
					fn: function () {
						var m = this.menu;
						m.removeAll();

						MODx.Ajax.request({
							url: userfiles.config.connector_url,
							params: {
								action: 'mgr/misc/link/get',
								source: source,
								ids: Ext.util.JSON.encode(ids)
							},
							listeners: {
								success: {
									fn: function (r) {

										if (!r.object || !r.object.links) {
											return;
										}

										for (var i in r.object.links) {

											var data = {
												text: String.format('<b><small>{0}</small><b>', i),
												link: r.object.links[i].join('')
											};

											m.add({
												cls: 'userfiles-menu-item-link',
												text: data.text,
												link: data.link,
												listeners: {
													click: {
														fn: function () {

															try {

																var divId = 'userfiles-div-copy';
																var div = Ext.get(divId);
																if (!div) {
																	div = Ext.DomHelper.insertFirst(document.body, {
																		id: divId
																	}, true);
																}

																var textareaId = 'userfiles-textarea-copy';
																Ext.DomHelper.append(div, ['<textarea id="', textareaId, '">', this.link, '</textarea>'].join(''), true);

																var cut = document.querySelector('#' + textareaId);
																cut.select();
																var сutIsEnabled = document.queryCommandEnabled('cut');
																var successful = document.execCommand('cut');

																div.hide().ghost("t", {
																	delay: 2000,
																	remove: true
																});
																userfiles.tools.msg(_('userfiles_link'), _('userfiles_action_copy_boofer'));

															} catch (err) {
																console.error(err);

																MODx.load({
																	xtype: 'userfiles-window-file-link',
																	record: data,
																	listeners: {}
																}).show();
															}

														}
													}
												}
											});
										}

									},
									scope: this
								}
							}
						});

					}

				}
			}
		});

		m.show(n, 'tl-c?');
		m.activeNode = n;

		Ext.each(Ext.query('.x-tip'), function (t) {
			var o = Ext.get(t.id);
			if (o) o.hide();
		});

	},

	_getSelectedIds: function () {
		var ids = [];
		var selected = this.getSelectedRecords();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	}

});
Ext.reg('userfiles-view-files', userfiles.view.Files);
