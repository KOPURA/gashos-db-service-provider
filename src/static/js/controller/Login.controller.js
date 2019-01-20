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

        AJAXUtils.doPOST({
            url: "api/api.php?action=login",
            data: {
                "Username": oUnameField.getValue(),
                "Password": oPwdField.getValue(),
            },
            success: function(oResponse) {
                sap.m.MessageToast.show("Login successful!");
            },
            error: function(iStatusCode, oResponse) {
                var oErrors = oResponse['errors'];
                ValidationUtils.showErrors(oView, ['Username', 'Password'], oErrors);
            },
        });
    },
});