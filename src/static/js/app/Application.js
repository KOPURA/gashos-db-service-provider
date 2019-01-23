(function() {
    sap.m.App.extend('gashos.dbaas.provider.app.Application', {
        renderer: 'sap.m.NavContainerRenderer',
        startApplication: function() {        
            var that = this;

            gashos.dbaas.provider.getIndicator().setBusy(true, "Loading application...");

            AJAXUtils.doGET({
                url: "api/instances/",
                success: function(oResponse) {
                    that.to("InstancesView");
                },
                error: function(iStatusCode, oResponse) {
                    if (iStatusCode === 401) {
                        that.to("LoginView");
                    } else {
                        var sError = "Failed to fetch model.\n\n";
                        sError += ValidationUtils.fetchGeneralErrors(oResponse["errors"]) || "Internal server error";
                        gashos.dbaas.provider.getIndicator().showError(sError);
                    }
                },
                finally: function() {
                    jQuery.sap.delayedCall(500, that, function() {
                        gashos.dbaas.provider.getIndicator().setBusy(false);
                        that.setVisible(true);
                    });
                }
            });
        },
    });
})();