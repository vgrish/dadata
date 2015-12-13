var dadata = function (config) {
	config = config || {};
	dadata.superclass.constructor.call(this, config);
};
Ext.extend(dadata, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('dadata', dadata);

dadata = new dadata();