
Ext.override(MODx.panel.Resource, {

    originals: {
        getFields: MODx.panel.Resource.prototype.getFields,
        beforeSubmit: MODx.panel.Resource.prototype.beforeSubmit
    },

    getFields: function(config) {
        var fields = this.originals.getFields.call(this, config);

        var tabs = fields.filter(function(row) {
            if (row.id == 'modx-resource-tabs') {
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

        if (is.length == 0) {
            tabs.add({
                xtype: 'userfiles-panel-resource-file',
                title: _('userfiles')
            });
        }
    });
});