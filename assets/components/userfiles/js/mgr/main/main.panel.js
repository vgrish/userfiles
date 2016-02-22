userfiles.panel.Main = function (config) {
    if (!config.class) {
        config.class = 'modResource';
    }
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        cls: 'userfiles-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            xtype: 'modx-tabs',
            defaults: {
                border: false,
                autoHeight: true
            },
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('userfiles_files'),
                layout: 'anchor',
                items: [{
                    html: _('userfiles_files_intro'),
                    cls: 'panel-desc'
                }, {
                    xtype: 'userfiles-panel-files',
                    class: config.class,
                    cls: 'main-wrapper'
                }]
            }]
        }]
    });
    userfiles.panel.Main.superclass.constructor.call(this, config);
};
Ext.extend(userfiles.panel.Main, MODx.Panel);
Ext.reg('userfiles-panel-main', userfiles.panel.Main);
