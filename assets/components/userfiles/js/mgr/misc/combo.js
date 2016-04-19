Ext.namespace('userfiles.combo');


userfiles.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear
    });
    userfiles.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(userfiles.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger ' + this.searchBtnCls
            }, {
                tag: 'div',
                cls: 'x-form-trigger ' + this.clearBtnCls
            }]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    }

});
Ext.reg('userfiles-field-search', userfiles.combo.Search);


userfiles.combo.Source = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-source-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-source-clear'
            });
        }
        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        ctCls: 'x-field-file-source',
        name: config.name || 'source',
        hiddenName: config.hiddenName || 'source',
        displayField: 'name',
        valueField: 'id',
        editable: false,
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        anchor: '99%',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    userfiles.combo.Source.superclass.constructor.call(this, config);
};
Ext.extend(userfiles.combo.Source, MODx.combo.MediaSource);
Ext.reg('userfiles-combo-source', userfiles.combo.Source);



userfiles.combo.Type = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-type-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-type-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'type',
        hiddenName: config.name || 'type',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        hideMode: 'offsets',
        url: userfiles.config.connector_url,
        baseParams: {
            action: 'mgr/misc/type/getlist',
            combo: true,
            addall: config.addall || 0,
            novalue: config.novalue || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-userfiles-type',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    userfiles.combo.Type.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.combo.Type, MODx.combo.ComboBox);
Ext.reg('userfiles-combo-type', userfiles.combo.Type);


userfiles.combo.List = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-list-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-list-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'list',
        hiddenName: config.name || 'list',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        hideMode: 'offsets',
        url: userfiles.config.connector_url,
        baseParams: {
            action: 'mgr/misc/list/getlist',
            combo: true,
            addall: config.addall || 0,
            novalue: config.novalue || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-userfiles-list',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    userfiles.combo.List.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.combo.List, MODx.combo.ComboBox);
Ext.reg('userfiles-combo-list', userfiles.combo.List);




userfiles.combo.List = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-list-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-list-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'list',
        hiddenName: config.name || 'list',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        hideMode: 'offsets',
        url: userfiles.config.connector_url,
        baseParams: {
            action: 'mgr/misc/list/getlist',
            combo: true,
            addall: config.addall || 0,
            novalue: config.novalue || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-userfiles-list',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    userfiles.combo.List.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.combo.List, MODx.combo.ComboBox);
Ext.reg('userfiles-combo-list', userfiles.combo.List);


userfiles.combo.Class = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-class-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-class-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'class',
        hiddenName: config.name || 'class',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        hideMode: 'offsets',
        url: userfiles.config.connector_url,
        baseParams: {
            action: 'mgr/misc/class/getlist',
            combo: true,
            addall: config.addall || 0,
            novalue: config.novalue || 0,
            class: config.class || ''
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b></span>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-userfiles-class',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    userfiles.combo.Class.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.combo.Class, MODx.combo.ComboBox);
Ext.reg('userfiles-combo-class', userfiles.combo.Class);


userfiles.combo.Parent = function(config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear?62:31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-parent-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-userfiles-parent-clear'
            });
        }

        config.initTrigger = function() {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function(t, all, index) {
                t.hide = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function() {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'parent',
        hiddenName: config.name || 'parent',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['name', 'id'],
        pageSize: 10,
        emptyText: _('userfiles_combo_select'),
        hideMode: 'offsets',
        url: userfiles.config.connector_url,
        baseParams: {
            action: 'mgr/misc/parent/getlist',
            combo: true,
            addall: config.addall || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{name}</b>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-userfiles-parent',
        clearValue: function() {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function(index) {
            return this.triggers[index];
        },

        onTrigger1Click: function() {
            this.onTriggerClick();
        },

        onTrigger2Click: function() {
            this.clearValue();
        }
    });
    userfiles.combo.Parent.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.combo.Parent, MODx.combo.ComboBox);
Ext.reg('userfiles-combo-parent', userfiles.combo.Parent);


userfiles.combo.MimeType = function(config) {
    config = config || {};

    if (!config.data) {
        config.data = JSON.parse(MODx.config['userfiles_image_mime_type'] || '[{"type":"png","mime":"image/png"}]');
    }

    Ext.applyIf(config,{
        name: config.name || 'mime',
        displayField: 'type',
        valueField: 'mime',
        editable: false,
        mode: 'local',
        store: new Ext.data.JsonStore({
            data: config.data,
            fields: ['type', 'mime']
        }),
        value: config.data[0] ? config.data[0].type : ''
    });

    userfiles.combo.MimeType.superclass.constructor.call(this,config);
};
Ext.extend(userfiles.combo.MimeType,MODx.combo.ComboBox);
Ext.reg('userfiles-combo-mime-type',userfiles.combo.MimeType);
