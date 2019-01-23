'use strict';
sap.ui.controller('gashos.dbaas.provider.controller.Login', {

    toRegister: function() {
        window.app.to("RegisterView");
    },

    loginUser: function() {
        var that = this;
        var oView = this.getView();
        var oUnameField = oView.byId('Username');
        var oPwdField   = oView.byId('Password');
        oView.reset(true);

        gashos.dbaas.provider.getIndicator().setBusy(true, oView.getBusyText());
        AJAXUtils.doPOST({
            url: "api/user/",
            data: {
                "Username": oUnameField.getValue(),
                "Password": oPwdField.getValue(),
            },
            success: function(oResponse) {
                jQuery.sap.delayedCall(500, this, function() {
                    window.app.to("InstancesView");
                    gashos.dbaas.provider.getIndicator().setBusy(false);
                });
            },
            error: function(iStatusCode, oResponse) {
                jQuery.sap.delayedCall(500, this, function() {
                    oView.showErrors(oResponse['errors']);
                    gashos.dbaas.provider.getIndicator().setBusy(false);
                });
            },
        });
    },
});