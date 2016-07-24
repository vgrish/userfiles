Ext.override(MODx.panel.Resource, {

    userFilesOriginals: {
        getFields: MODx.panel.Resource.prototype.getFields
    },

    getFields: function(config) {
        var fields = this.userFilesOriginals.getFields.call(this, config);

        var isHide = false;
        var templates = MODx.config.userfiles_working_templates || '';
        if (templates.split(',').indexOf(''+config.record.template+'') < 0) {
            isHide = true;
        }

        if (!userfiles.config.resource) {
            isHide = true;
        }

        fields.filter(function(row) {
            if (row.id == 'modx-resource-tabs' && !isHide) {
                row.items.push({
                    xtype: 'userfiles-panel-resource-file',
                    title: _('userfiles')
                });
            }
        });

        return fields;
    }

});

Ext.ComponentMgr.onAvailable('modx-resource-tabs', function() {

    var tabs = this;
    tabs.on('beforerender', function() {

        var is = tabs.items.items.filter(function(row) {
            if (row.id == 'userfiles-panel-resource') {
                return true;
            } else {
                return false;
            }
        });

        var isHide = false;

        var $resource = Ext.getCmp("modx-page-update-resource");
        var templates = MODx.config.userfiles_working_templates || '';
        if (templates.split(',').indexOf(''+$resource.record.template+'') < 0) {
            isHide = true;
        }

        if (!userfiles.config.resource) {
            isHide = true;
        }

        if (is.length == 0 && !isHide) {
            tabs.add({
                xtype: 'userfiles-panel-resource-file',
                title: _('userfiles')
            });
        }
    });
});
