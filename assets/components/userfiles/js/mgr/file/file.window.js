userfiles.window.ImageEdit = function(config) {
    config = config || {};
    config.cropperSelector = config.cropperSelector || '.image-upload-wrapper > img';

    Ext.applyIf(config, {
        title: _('update'),
        modal: Ext.isIE ? false : true,
        width: 800,
        layout: 'auto',
        closeAction: 'close',
        shadow: true,
        resizable: true,
        collapsible: true,
        maximizable: false,
        autoHeight: false,
        autoScroll: true,
        allowDrop: true,

        cls: 'userfiles-cropper-window',
        items: this.getFields(config),
        listeners: this.getListeners(config),
        bbar: this.getBottomBar(config)
    });

    userfiles.window.ImageEdit.superclass.constructor.call(this, config);
    this.config = config;
    this.on('show',this.init,this);
};
Ext.extend(userfiles.window.ImageEdit, Ext.Window, {
    imageData: '',
    cropperProfile: {name: ''},

    changeCropperProfile: function(profile){
        var ratio;

        if (profile.ratio != "") {
            ratio = profile.ratio;
            ratio.replace(/[^-:x()\d/*+.]/g, '');
            ratio = eval(ratio) || NaN;
        } else {
            if (profile.width && profile.height) {
                var width = parseInt(profile.width);
                var height = parseInt(profile.height);
                if (width > 0 && height > 0) {
                    ratio = width / height;
                } else {
                    ratio = NaN;
                }
            } else {
                ratio = NaN;
            }
        }

        this.cropperProfile = profile;
        this.$cropperEl.cropper('setAspectRatio', ratio);
    },


    getFields: function(config) {

        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            layout: 'column',
            border: false,
            defaults: {
                layout: 'form',
                labelAlign: 'top',
                labelSeparator: '',
                anchor: '100%',
                border: false
            },
            items: [{
                columnWidth: 1,
                defaults: {
                    msgTarget: 'under',
                    anchor: '100%'
                },
                cls: 'userfiles-cropper',
                items: [{
                    html: '<div class="image-upload-wrapper"><img src="' + config.record.dyn_url + '"></div>'
                }]
            }]
        }];
    },

    getBottomBar: function (config) {
        var component = [
            'move', 'crop', 'zoom_plus', 'zoom_minus', 'rotate_left', 'rotate_right', 'scalex', 'scaley',
            'clear', 'left', 'profile', 'type', 'cancel', 'save'
        ];
        var bbar = [];

        var add = {
            move: {
                param: 'move',
                action: 'setDragMode',
                handler: this.setCropperAction,
                scope: this
            },
            crop: {
                param: 'crop',
                action: 'setDragMode',
                handler: this.setCropperAction,
                scope: this
            },
            zoom_plus: {
                param: 0.1,
                action: 'zoom',
                handler: this.setCropperAction,
                scope: this
            },
            zoom_minus: {
                param: -0.1,
                action: 'zoom',
                handler: this.setCropperAction,
                scope: this
            },
            rotate_left: {
                param: -90,
                action: 'rotate',
                handler: this.setCropperAction,
                scope: this
            },
            rotate_right: {
                param: 90,
                action: 'rotate',
                handler: this.setCropperAction,
                scope: this
            },
            scalex: {
                param: -1,
                action: 'scaleX',
                handler: this.setCropperAction,
                scope: this
            },
            scaley: {
                param: -1,
                action: 'scaleY',
                handler: this.setCropperAction,
                scope: this
            },
            clear: {
                param: null,
                action: 'clear',
                handler: this.setCropperAction,
                scope: this
            },
            type: {
                xtype: 'userfiles-combo-mime-type',
                cls: 'userfiles-mime-type',
                width: 78,
                listeners: {
                    select: {
                        fn: function(combo, value){
                            if (this.$cropperEl) {
                                this.$cropperEl.type = value.data.type;
                            }
                        },
                        scope: this
                    },
                },
                value: config.record.type
            },
            profile: {
                xtype: 'userfiles-combo-cropper-profile',
                cls: 'userfiles-cropper-profile',
                width: 150,
                listeners: {
                    select: {
                        fn: function(combo, value){
                            this.changeCropperProfile(value.data);
                        },
                        scope: this
                    },
                }
            },
            left: '->',
            cancel: {
                handler: this.close,
                scope: this
            },
            save: {
                handler: this.save,
                scope: this
            }
        };

        component.filter(function(field) {
            if (add[field]) {
                Ext.applyIf(add[field], {
                    text: _('userfiles_button_' + field),
                    listeners: {
                        render: function(b) {
                            var t = _('userfiles_tooltip_' + field);
                            if (t) {
                                Ext.QuickTips.register({
                                    target: b,
                                    text: t,
                                });
                            }
                        }
                    }
                });
                bbar.push(add[field]);
            }
        });

        return bbar;
    },

    getListeners: function (config) {
        var listeners = {
            show: {
                fn: function() {
                    var uf$ = jQuery.noConflict();
                    this.$cropperEl = uf$('#' + this.id + ' ' + config.cropperSelector);
                    var cropperOptions = {};
                    cropperOptions.crop = function(data) {
                        this.imageData = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width,
                            '"rotate":' + data.rotate + '}'
                        ].join();
                    }.bind(this);
                    this.$cropperEl.cropper(cropperOptions);
                    this.$cropperEl.type = config.record.type;
                },
                scope: this
            }
        };

        return Ext.applyIf(config.listeners, listeners);
    },

    setCropperAction: function(btn) {
        var param = this.$cropperEl.data(btn.action);

        switch (true) {
            case btn.action == 'scaleX' && param == btn.param && param > 0:
                btn.param = -1;
                break;
            case btn.action == 'scaleX' && param == btn.param && param < 0:
                btn.param = 1;
                break;
            case btn.action == 'scaleY' && param == btn.param && param > 0:
                btn.param = -1;
                break;
            case btn.action == 'scaleY' && param == btn.param && param < 0:
                btn.param = 1;
                break;
        }

        this.$cropperEl.data(btn.action, btn.param).attr('data-' + btn.action, btn.param);
        this.$cropperEl.cropper(btn.action, btn.param);
    },



    close: function() {
        this.$cropperEl.cropper("destroy");
        userfiles.window.ImageEdit.superclass.close.call(this);
    },

    save: function(btn) {
        var config = this.config;
        var data = this.$cropperEl.cropper('getData');
        var type = this.$cropperEl.type || 'png';

        this.$cropperEl.cropper('getCroppedCanvas', data).toBlob(function (file) {
            file = new Blob([file], { type: 'image/'+type});

            var formData = new FormData();
            formData.append('action', 'mgr/file/image/update');
            formData.append('crop', true);
            formData.append('data', JSON.stringify(data));
            formData.append('type', type);
            formData.append('id', config.record.id);
            formData.append('file', file, 'file.'+type);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', userfiles.config.connector_url);
            xhr.setRequestHeader('Powered-By', 'MODx');
            xhr.setRequestHeader('modAuth', Ext.Ajax.defaultHeaders.modAuth);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                    if (this.progress) {
                        this.progress.hide();
                    }
                    this.fireEvent('success', {});
                    userfiles.window.ImageEdit.superclass.close.call(this);
                }
            }.bind(this);

            xhr.upload.onprogress = function (event) {
                if (event.lengthComputable) {
                    this.progress.updateProgress(event.loaded / event.total);
                }
            }.bind(this);

            xhr.send(formData);
            this.progress = Ext.MessageBox.progress(_('please_wait'));

        }.bind(this));

    }

});
Ext.reg('userfiles-window-image-edit', userfiles.window.ImageEdit);



userfiles.window.FileLink = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        title: _('userfiles_link'),
        width: 800,
        closeAction: 'close',
        resizable: false,
        collapsible: false,
        maximizable: false,
        autoHeight: true,

        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    userfiles.window.FileLink.superclass.constructor.call(this,config);
};
Ext.extend(userfiles.window.FileLink,MODx.Window, {

    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: this.close,
            scope: this
        }];
    },

    getButtons: function (config) {
        return [{
            text: _('close'),
            scope: this,
            handler: function () {
                this.close();
            }
        }];
    },

    getFields: function(config) {
        return [{
            xtype: 'textarea',
            name: 'link',
            fieldLabel: _('userfiles_link'),
            allowBlank: false,
            anchor: '100%'
        }];
    }

});
Ext.reg('userfiles-window-file-link',userfiles.window.FileLink);



userfiles.window.FileUpdate = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('update'),
        width: 550,
        layout: 'auto',
        autoHeight: true,
        maximizable: false,

        url: userfiles.config.connector_url,
        action: 'mgr/file/update',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    userfiles.window.FileUpdate.superclass.constructor.call(this, config);

};
Ext.extend(userfiles.window.FileUpdate, MODx.Window, {

    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: this.submit,
            scope: this
        }];
    },

    getButtons: function (config) {
        return [{
            text: _('save'),
            scope: this,
            handler: function () {
                this.submit();
            }
        }];
    },

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'textfield',
            fieldLabel: _('userfiles_name'),
            name: 'name',
            anchor: '99%',
            allowBlank: false
        }, {
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .49,
                        border: false,
                        layout: 'form',
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: this.getRightFields(config)
                    }]
                }]
            }]
        }, {
            xtype: 'textarea',
            fieldLabel: _('userfiles_description'),
            name: 'description',
            anchor: '99%',
            height: 50,
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('userfiles_active'),
            name: 'active',
            checked: config.record.active
        }];
    },

    getLeftFields: function(config) {
        return [{
            xtype: 'userfiles-combo-class',
            fieldLabel: _('userfiles_class'),
            width: 190,
            custm: true,
            clear: true,
            addall: false,
            name: 'class',
            value: config.record.class,
            anchor: '99%',
            listeners: {
                afterrender: {
                    fn: function(r) {
                        this._handleChangeClass(0);
                    },
                    scope: this
                },
                select: {
                    fn: function (r) {
                        this._handleChangeClass(1);
                    },
                    scope: this
                }
            }
        }];
    },

    getRightFields: function(config) {
        return [{
            xtype: 'userfiles-combo-parent',
            fieldLabel: _('userfiles_parent'),
            width: 190,
            custm: true,
            clear: true,
            addall: false,
            anchor: '99%',
            name: 'parent',
            value: config.record.parent
        }];
    },

    _handleChangeClass: function (change) {
        var f = this.fp.getForm();
        var _class = f.findField('class');
        var _parent = f.findField('parent');

        _parent.baseParams.class = _class.getValue();

        if (!!_parent.pageTb) {
            _parent.pageTb.show();
        }
        if ((1 == change)) {
            _parent.setValue();
        }
        _parent.store.load();
    }

});
Ext.reg('userfiles-window-file-update', userfiles.window.FileUpdate);

