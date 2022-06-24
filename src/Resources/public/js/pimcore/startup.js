pimcore.registerNS("pimcore.plugin.NeustaPimcoreUMLGenerationBundle");

pimcore.plugin.NeustaPimcoreUMLGenerationBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.NeustaPimcoreUMLGenerationBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("NeustaPimcoreUMLGenerationBundle ready!");
    }
});

var NeustaPimcoreUMLGenerationBundlePlugin = new pimcore.plugin.NeustaPimcoreUMLGenerationBundle();
