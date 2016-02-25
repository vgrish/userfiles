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
