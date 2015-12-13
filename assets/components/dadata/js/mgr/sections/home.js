dadata.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'dadata-panel-home', renderTo: 'dadata-panel-home-div'
		}]
	});
	dadata.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(dadata.page.Home, MODx.Component);
Ext.reg('dadata-page-home', dadata.page.Home);