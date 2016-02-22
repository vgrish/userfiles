userfiles.panel.ResourceFiles = function(config) {
    config = config || {};

    if (!config.class) {
        config.class = 'modResource';
    }
    if (!config.update) {
        config.update = false;
    }

    Ext.apply(config, {
        id: 'userfiles-panel-resource',
        forceLayout: true,
        deferredRender: false,
        autoHeight: true,
        border: false,
        bodyCssClass: 'main-wrapper_',
        items: this.getMainItems(config)
    });
    userfiles.panel.ResourceFiles.superclass.constructor.call(this, config);
};
Ext.extend(userfiles.panel.ResourceFiles, MODx.Panel, {

    getMainItems: function (config) {
        return [{
			xtype       : 'panel',
			layout      : 'fit',
			items :[{
				xtype: 'userfiles-panel-files',
				parent: userfiles.config.resource.id,
				class: config.class
			}]
		}];
    },

});

Ext.reg('userfiles-panel-resource-file', userfiles.panel.ResourceFiles);

