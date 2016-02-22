Ext.override(MODx.panel.Resource, {

    originals: {
        getFields: MODx.panel.Resource.prototype.getFields,
        beforeSubmit: MODx.panel.Resource.prototype.beforeSubmit
    },

    getFields: function(config) {
        var fields = this.originals.getFields.call(this, config);

        //var record = {
        //    resource: userfiles.config.resource || null,
        //    content: userfiles.config.content || null
        //};

        //console.log(record);

        var tabs = fields.filter(function(row) {
            if (row.id == 'modx-resource-tabs') {
                row.items.push({
                    //xtype: record.content ? 'userfiles-panel-content-update' : 'userfiles-panel-content-create',
                    xtype: 'userfiles-panel-resource-file',
                    title: _('userfiles'),

                    // userfiles-panel-resource-user-file
                    //record: record,
                    //update: record.content ? true : false
                });
            } else {
                return false;
            }
        });

        return fields;
    }

});
