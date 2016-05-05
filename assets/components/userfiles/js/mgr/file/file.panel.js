userfiles.panel.Files = function(config) {
    config = config || {};

	this.pageSize =  parseInt(config.pageSize|| MODx.config.default_per_page);

	this.view = MODx.load({
		xtype: 'userfiles-view-files',
		parent: config.parent || 0,
		class: config.class || '',
		list: config.list || '',
		type: config.type || '',
		pageSize: this.pageSize,
		emptyText:_('userfiles_emptymsg')
	});

    Ext.applyIf(config,{
        id: 'userfiles-files',
        border: false,
        items: this.view,
        tbar: this.getTopBar(config),
        bbar: this.getBottomBar(config),
    });
    userfiles.panel.Files.superclass.constructor.call(this,config);

	this.view.on('render', function(v) {

		this.DragZone = new Ext.dd.DragZone(v.getEl(), {
			getDragData : function(e) {
				var target = e.getTarget(v.itemSelector);
				if (!target) {
					return false;
				}
				if(e.ctrlKey) {
					return false;
				}
				else if (!v.isSelected(target)) {
					v.onClick(e);
				}

				var selNodes = v.getSelectedNodes();
				if (selNodes.length > 1) {
					return false;
				}

				return {
					nodes: selNodes,
					ddel: target,
					single: true
				};
			}
		});
		this.DropZone = new Ext.dd.DropZone(v.getEl(), {
			getTargetFromEvent: function(e) {
				return e.getTarget(v.itemSelector);
			}

			,onNodeEnter : function(target, dd, e, data) {
				Ext.fly(target).addClass('x-view-selected');
			}

			,onNodeOut : function(target, dd, e, data) {
				Ext.fly(target).removeClass('x-view-selected');
			}

			,onNodeOver : function(target, dd, e, data) {
				return Ext.dd.DropZone.prototype.dropAllowed && (target != data.nodes[0]);
			}

			,onNodeDrop : function(target, dd, e, data) {
				var targetNode = v.getRecord(target);
				var sourceNode = v.getRecord(data.nodes[0]);
				if (sourceNode == targetNode) {
					return false;
				}
				var targetElement = Ext.get(target);
				var sourceElement = Ext.get(data.nodes[0]);
				sourceElement.insertBefore(targetElement);

				v.fireEvent('sort', {
					target: targetNode,
					source: sourceNode,
					event: e,
					dd: dd
				});
				return true;
			}
		});
	});
	this.on('afterrender', function() {
		this.initialize();
	});

};
Ext.extend(userfiles.panel.Files,MODx.Panel, {
	dropzone: null,

	initialize: function() {
		if (this.initialized) {
			return;
		}

		var config = {
			url: userfiles.config.connector_url,
			params: {
				action:'mgr/file/upload',
				ctx: 'mgr',
				HTTP_MODAUTH: MODx.siteId
			},
			maxFilesize: 999999999,
			createImageThumbnails: false,
			clickable: '.userfiles-dropzonejs-upload-btn'
		};

		if (document.getElementById(this.config.id)) {
			dropzone = new Dropzone('#'+this.config.id, config);
			dropzone.view = this.view;
			dropzone.self = this;
		}

		var DropzoneEvents = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile",
			"addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple",
			"uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled",
			"canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"];
		Ext.each(DropzoneEvents, function (event) {
			if (this['_'+event]) {
				dropzone.on(event, this['_'+event]);
			}
		},this);
		this.initialized = true;
	},

	_addedfile:function(file) {
		dropzone.options.params['parent'] = this.view.getStore().baseParams['parent'] || 0;
		dropzone.options.params['class'] = this.view.getStore().baseParams['class'] || '';
		dropzone.options.params['list'] = this.view.getStore().baseParams['list'] || '';
		dropzone.options.params['source'] = this.view.getStore().baseParams['source'] || userfiles.config.source || 1;
		dropzone.options.params['context'] = this.view.getStore().baseParams['context'] || userfiles.config.context || 'web';

		this.errors = [];
		file.previewElement = null;
	},

	_processing: function(file) {
		this.progress = Ext.MessageBox.progress(_('please_wait'));
	},

	_uploadprogress: function(file, progress, bytesSent) {
		if (this.progress) {
			this.progress.updateText(file.name);
			this.progress.updateProgress(progress / 100);
		}
	},

	_complete: function(file) {

	},

	_queuecomplete: function() {
		if (this.progress) {
			this.progress.hide();
		}
		if (this.errors.length > 0) {
			MODx.msg.alert(_('userfiles_err'), this.errors.join('<br>'));
		}
		this.view.getStore().reload();
	},

	_error: function(file, message) {

	},

	_success: function(file, response) {
		if (response.success == false && response.message != '') {
			this.errors.push(file.name + ': ' + response.message);
		}
	},

	reload: function() {
		this.view.getStore().reload();
	},

	getTopBar: function(config) {
		var tbar1 = [];
		var tbar2 = [];

		var component1 = ['button', 'left', 'list', 'type', 'source','spacer'];
		var component2 = ['left','class','parent','search','spacer'];

		switch (true) {
			case !!userfiles.config.resource:
				/*component1.remove('button');*/
				component2 = [];
				break;
			case !!userfiles.config.user:
				component2 = [];
				break;
			default:
				break;
		}

		var add = {
			button: {
				xtype: 'button',
				cls: 'userfiles-dropzonejs-upload-btn',
				text: '<i class="icon icon-upload"></i> ' + _('userfiles_action_load')
			},
			left: '->',
			source: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-combo-source',
					custm: true,
					clear: true,
					fieldLabel: _('userfiles_source'),
					name: 'source',
					width: 190,
					anchor: '99%',
					allowBlank: false,
					value: userfiles.config.source || 1,
					listeners: {
						select: {
							fn: this._filterBySource,
							scope: this
						},
						afterrender: {
							fn: this._filterBySource,
							scope: this
						}
					}
				}]
			},
			list: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-combo-list',
					width: 190,
					custm: true,
					clear: true,
					value: 'default',
					listeners: {
						select: {
							fn: this._filterByCombo,
							scope: this
						},
						afterrender: {
							fn: this._filterByCombo,
							scope: this
						}
					}
				}]

			},
			type: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-combo-type',
					width: 190,
					custm: true,
					clear: true,
					addall: true,
					value: 0,
					anchor: '99%',
					listeners: {
						select: {
							fn: this._filterByCombo,
							scope: this
						},
						afterrender: {
							fn: this._filterByCombo,
							scope: this
						}
					}
				}]

			},
			class: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-combo-class',
					width: 190,
					custm: true,
					clear: true,
					addall: false,
					value: config.class,
					anchor: '99%',
					listeners: {
						select: {
							fn: this._filterByClass,
							scope: this
						},
						afterrender: {
							fn: this._filterByClass,
							scope: this
						}
					}
				}]
			},
			parent: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-combo-parent',
					width: 190,
					custm: true,
					clear: true,
					addall: true,
					//name: 'parent',
					value: 0,
					listeners: {
						select: {
							fn: this._filterByCombo,
							scope: this
						},
						afterrender: {
							fn: this._filterByCombo,
							scope: this
						}
					}
				}]
			},
			search: {
				xtype: 'panel',
				width: 190,
				layout: 'fit',
				bodyStyle: 'padding-left: 10px;',
				items: [{
					xtype: 'userfiles-field-search',
					width: 190,
					listeners: {
						search: {
							fn: function (field) {
								this._doSearch(field);
							},
							scope: this
						},
						clear: {
							fn: function (field) {
								field.setValue('');
								this._clearSearch();
							},
							scope: this
						}
					}
				}]
			},
			spacer: {
				xtype: 'panel',
				width: 10,
				layout: 'fit',
				items: [{
					xtype: 'spacer',
					/*style: 'width:1px;'*/
				}]
			}
		};

		component1.filter(function(item) {
			if (add[item]) {
				tbar1.push(add[item]);
			}
		});

		component2.filter(function(item) {
			if (add[item]) {
				tbar2.push(add[item]);
			}
		});

		var items = [];
		if (tbar1.length > 0) {
			items.push(new Ext.Toolbar(tbar1));
		}
		if (tbar2.length > 0) {
			items.push(new Ext.Toolbar(tbar2));
		}

		return new Ext.Panel({items:items});
    },

	_filterBySource: function (cb) {
		if (cb.value == '' || cb.value == 0) {
			cb.value = userfiles.config.source || 1;
			cb.setValue(cb.value);
		}
		this.view.getStore().baseParams[cb.name] = cb.value;
		this.getBottomToolbar().changePage(1);
	},

	_filterByCombo: function (cb) {
		this.view.getStore().baseParams[cb.name] = cb.value;
		this.getBottomToolbar().changePage(1);
	},

	_doSearch: function (cb) {
		this.view.getStore().baseParams.query = cb.getValue();
		this.getBottomToolbar().changePage(1);
		userfiles.tools.Hash.add('query', cb.getValue());
	},

	_clearSearch: function () {
		this.view.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
		userfiles.tools.Hash.remove('query');
	},


	_filterByClass: function (cb) {
		var parent = this.getTopToolbar().findByType('combo').find( function(c){ return ( c.hiddenName == 'parent') } );
		if (!!parent) {
			parent.baseParams.class = cb.value;
			parent.setValue();
			parent.store.load();

			if(!!parent.pageTb) {
				parent.pageTb.show();
			}

			this.view.getStore().baseParams[cb.name] = cb.value;
			this.getBottomToolbar().changePage(1);
		}
	},

    getBottomBar: function(config) {
        return new Ext.PagingToolbar({
			pageSize: this.pageSize,
			store: this.view.store,
			displayInfo: true,
			autoLoad: true,
			items: ['-',
				_('per_page') + ':',
				{
					xtype: 'textfield',
					value: this.pageSize,
					width: 50,
					listeners: {
						change: {fn:function(tf,nv,ov) {
							if (Ext.isEmpty(nv)) {return;}
							nv = parseInt(nv);
							this.getBottomToolbar().pageSize = nv;

							userfiles.tools.Hash.add('uf_start',0);
							userfiles.tools.Hash.add('uf_limit',nv);

							this.view.getStore().load({params:{start:0, limit: nv}});
						}, scope:this},
						render: {fn: function(cmp) {
							new Ext.KeyMap(cmp.getEl(), {
								key: Ext.EventObject.ENTER,
								fn: function() {this.fireEvent('change',this.getValue());this.blur();return true;},
								scope: cmp
							});
						}, scope:this}
					}
				}
			],
			listeners: {
				beforechange:{fn: function(tf, ov) {
					userfiles.tools.Hash.add('uf_start',ov.start);
					userfiles.tools.Hash.add('uf_limit',ov.limit);
				}, scope:this}
			}
		});
    }

});
Ext.reg('userfiles-panel-files',userfiles.panel.Files);


