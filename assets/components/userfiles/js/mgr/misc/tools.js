userfiles.tools.getMenu = function (actions, grid, selected) {
	var menu = [];
	var cls, icon, title, action = '';

	var has_delete = false;
	for (var i in actions) {
		if (!actions.hasOwnProperty(i)) {
			continue;
		}

		var a = actions[i];
		if (!a['menu']) {
			if (a == '-') {
				menu.push('-');
			}
			continue;
		} else if (menu.length > 0 && (/^sep/i.test(a['action']))) {
			menu.push('-');
			continue;
		}

		if (selected.length > 1) {
			if (!a['multiple']) {
				continue;
			} else if (typeof(a['multiple']) == 'string') {
				a['title'] = a['multiple'];
			}
		}

		cls = a['cls'] ? a['cls'] : '';
		icon = a['icon'] ? a['icon'] : '';
		title = a['title'] ? a['title'] : a['title'];
		action = a['action'] ? grid[a['action']] : '';

		menu.push({
			handler: action,
			text: String.format(
				'<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
				cls, icon, title
			),
			scope: grid
		});
	}

	return menu;
};

userfiles.tools.renderActions = function (value, props, row) {
	var res = [];
	var cls, icon, title, action, item = '';
	for (var i in row.data.actions) {
		if (!row.data.actions.hasOwnProperty(i)) {
			continue;
		}
		var a = row.data.actions[i];
		if (!a['button']) {
			continue;
		}

		cls = a['cls'] ? a['cls'] : '';
		icon = a['icon'] ? a['icon'] : '';
		action = a['action'] ? a['action'] : '';
		title = a['title'] ? a['title'] : '';

		item = String.format(
			'<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
			cls, icon, action, title
		);

		res.push(item);
	}

	return String.format(
		'<ul class="userfiles-row-actions">{0}</ul>',
		res.join('')
	);
};


userfiles.tools.Hash = {
	get: function () {
		var vars = {},
			hash, splitter, hashes;
		if (!this.oldbrowser()) {
			var pos = window.location.href.indexOf('?');
			hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
			splitter = '&';
		} else {
			hashes = decodeURIComponent(window.location.hash.substr(1));
			splitter = '/';
		}

		if (hashes.length == 0) {
			return vars;
		} else {
			hashes = hashes.split(splitter);
		}

		for (var i in hashes) {
			if (hashes.hasOwnProperty(i)) {
				hash = hashes[i].split('=');
				if (typeof hash[1] == 'undefined') {
					vars['anchor'] = hash[0];
				} else {
					vars[hash[0]] = hash[1];
				}
			}
		}
		return vars;
	},

	set: function (vars) {
		var hash = '';
		for (var i in vars) {
			if (vars.hasOwnProperty(i)) {
				hash += '&' + i + '=' + vars[i];
			}
		}

		if (!this.oldbrowser()) {
			if (hash.length != 0) {
				hash = '?' + hash.substr(1);
			}
			window.history.pushState(hash, '', document.location.pathname + hash);
		} else {
			window.location.hash = hash.substr(1);
		}
	},

	add: function (key, val) {
		var hash = this.get();
		hash[key] = val;
		this.set(hash);
	},

	remove: function (key) {
		var hash = this.get();
		delete hash[key];
		this.set(hash);
	},

	clear: function () {
		this.set({});
	},

	oldbrowser: function () {
		return !(window.history && history.pushState);
	}
};


userfiles.tools.renderReplace = function (value, replace, color) {
	if (!value) {
		replace = MODx.lang.userfiles_no_value || MODx.lang.userfiles_no;
	}
	if (!replace) {
		replace = value;
	}
	if (!color) {
		color = '777';
	}
	return String.format('<span class="userfiles-render-color" style="color: #{1}">{0}</span>', replace, color);
};


userfiles.tools.userLink = function (value, id) {
	if (!value) {
		return '';
	}
	else if (!id) {
		return value;
	}
	var action = MODx.action ? MODx.action['security/user/update'] : 'security/user/update';
	var url = 'index.php?a=' + action + '&id=' + id;

	return String.format('<a href="{0}" target="_blank" class="user-link green">{1}</a>', url, value);
};


userfiles.tools.resourceLink = function (value, id, urlOnly) {
	if (!value) {
		return '';
	}
	else if (!id) {
		return value;
	}
	var action = MODx.action ? MODx.action['resource/update'] : 'resource/update';
	var url = 'index.php?a=' + action + '&id=' + id;

	if (!!urlOnly) {
		return url;
	}
	return String.format('<a href="{0}" target="_blank" class="resource-link green">{1}</a>', url, value);
};


userfiles.tools.handleChecked = function (checkbox) {
	var workCount = checkbox.workCount;
	if (!!!workCount) {
		workCount = 1;
	}
	var hideLabel = checkbox.hideLabel;
	if (!!!hideLabel) {
		hideLabel = false;
	}

	var checked = checkbox.getValue();
	var nextField = checkbox.nextSibling();

	for (var i = 0; i < workCount; i++) {
		if (checked) {
			nextField.show().enable();
		}
		else {
			nextField.hide().disable();
		}
		nextField.hideLabel = hideLabel;
		nextField = nextField.nextSibling();
	}
	return true;
};


userfiles.tools.msg = function (title, format, status) {
	var msgId = 'msg-div';
	var msgCt = Ext.get(msgId);

	function createBox(t, s, status) {
		if (!status) {
			status = 'info';
		}
		return ['<div class="msg msg-', status, '">',
			'<h4 class="msg-title">', t, '</h4>',
			'<div class="msg-text">', s, '</div>',
			'</div>'].join('');
	};

	if (!msgCt) {
		msgCt = Ext.DomHelper.insertFirst(document.body, {id: msgId}, true);
	}

	var m = Ext.DomHelper.append(msgCt, createBox(title, format, status), true);
	m.hide();
	m.slideIn('t').ghost("t", {delay: 2000, remove: true});
};


userfiles.tools.getListDefault = function (config) {

	var template = 0;
	if (userfiles.config && userfiles.config.resource && userfiles.config.resource.template) {
		template = userfiles.config.resource.template;
	}

	return MODx.config['userfiles_list_template_' + template] || MODx.config.userfiles_list_default || 'default';
};


userfiles.tools.getParentClass = function (config) {
	var parentClass = [];
	if (userfiles.config && userfiles.config.resource && userfiles.config.resource.class_key) {
		parentClass.push('modResource');
		parentClass.push(userfiles.config.resource.class_key);
	}
	else if (userfiles.config && userfiles.config.user) {
		parentClass.push('modUser');
	}
	else if (userfiles.config) {

	}

	return parentClass.join(',');
};

