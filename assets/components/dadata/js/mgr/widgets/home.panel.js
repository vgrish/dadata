dadata.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'dadata-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('dadata') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('dadata_items'),
				layout: 'anchor',
				items: [{
					html: _('dadata_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'dadata-grid-items',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	dadata.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(dadata.panel.Home, MODx.Panel);
Ext.reg('dadata-panel-home', dadata.panel.Home);
