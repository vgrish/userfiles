Ext.ComponentMgr.onAvailable('modx-user-tabs', function() {
    this.on('beforerender', function() {

        this.add({
            title: _('userfiles'),
            xtype: 'userfiles-panel-user-file'
        });
    });
});