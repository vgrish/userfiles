
Ext.override(MODx.panel.Resource, {

    originals: {
        getFields: MODx.panel.Resource.prototype.getFields,
        beforeSubmit: MODx.panel.Resource.prototype.beforeSubmit
    },

    getFields: function(config) {
        var fields = this.originals.getFields.call(this, config);

        var isHide = false;
        var templates = MODx.config.userfiles_working_templates || '';
        if (templates.split(',').indexOf(''+config.record.template+'') < 0) {
            isHide = true;
        }

        if (!userfiles.config.resource) {
            isHide = true;
        }

        var tabs = fields.filter(function(row) {
            if (row.id == 'modx-resource-tabs' && !isHide) {
                row.items.push({
                    xtype: 'userfiles-panel-resource-file',
                    title: _('userfiles')
                });
            } else {
                return false;
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
