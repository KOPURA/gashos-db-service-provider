(function(){
    var mRegistry = {};
    sap.ui.core.mvc.JSView.extend('gashos.dbaas.provider.app.AppPage', {
        renderer: 'sap.ui.core.mvc.JSViewRenderer',
        constructor: function(sViewId, sClassName, mSettings) {
            jQuery.sap.require({
                modName: sClassName,
                type: 'view',
            });
            jQuery.extend(this, (mRegistry[sClassName] || {}));

            mSettings = mSettings || {}
            mSettings.viewName = sClassName;
            sap.ui.core.mvc.JSView.prototype.constructor.apply(this, [sViewId, mSettings]);
        },

        showErrors: function(oErrors) {
            var aIds = this.getParamKeys();
            $.each(aIds, function(iIdx, sId) {
                var aRelevantErrors = oErrors[sId] || [];
                if (!aRelevantErrors.length)
                    return;

                var sErrorString = aRelevantErrors.join("\n");
                var oControl = this.byId(sId);
                oControl.setValueState(sap.ui.core.ValueState.Error);
                oControl.setValueStateText(sErrorString);
            }.bind(this));

            var aGeneralErrors = ValidationUtils.fetchGeneralErrors(oErrors);
            if (aGeneralErrors.length) {
                var sGeneralErrorsString = aGeneralErrors.join("\n");
                sap.m.MessageToast.show(sGeneralErrorsString);
            }
        },

        reset: function(bKeepValues) {
            this.setModel(new sap.ui.model.json.JSONModel({}));

            var aIds = this.getParamKeys();
            $.each(aIds, function(iIdx, sId) {
                var oControl = this.byId(sId);
                oControl.setValueState(sap.ui.core.ValueState.None);
                oControl.setValueStateText("");
                if (!bKeepValues) {
                    oControl.setValue("");
                }
            }.bind(this));
        },

        getParamKeys: function() {
            return [];
        },

        getBusyText: function() {
            return 'Please wait...';
        },

        onNav: function() {
            this.reset();
        },
    });
    gashos.dbaas.provider.app.page = function(sClassName, oImplementation) {
        mRegistry[sClassName] = mRegistry[sClassName] || {};
        jQuery.extend(mRegistry[sClassName], oImplementation);
    };
})();