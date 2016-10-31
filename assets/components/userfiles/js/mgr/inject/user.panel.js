userfiles.panel.UserFiles = function(config) {
    config = config || {};

	if (!config.class) {
		config.class = userfiles.tools.getParentClass();
	}
	if (!config.update) {
		config.update = false;
	}

    Ext.apply(config, {
        id: 'userfiles-panel-user',
        forceLayout: true,
        deferredRender: false,
        autoHeight: true,
        border: false,
        bodyCssClass: 'main-wrapper_',
        items: this.getMainItems(config)
    });
    userfiles.panel.UserFiles.superclass.constructor.call(this, config);
};
Ext.extend(userfiles.panel.UserFiles, MODx.Panel, {

    getMainItems: function (config) {
        return [{
			xtype       : 'panel',
			layout      : 'fit',
			items :[{
				xtype: 'userfiles-panel-files',
				parent: userfiles.config.user.id,
				class: config.class
			}]
		}];
    },

});

Ext.reg('userfiles-panel-user-file', userfiles.panel.UserFiles);
