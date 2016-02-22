var userfiles = function(config) {
	config = config || {};
	userfiles.superclass.constructor.call(this, config);
};
Ext.extend(userfiles, Ext.Component, {
	page: {},
	window: {},
	grid: {},
	tree: {},
	panel: {},
	combo: {},
	config: {},
	view: {},
	tools: {}
});
Ext.reg('userfiles', userfiles);

userfiles = new userfiles();
