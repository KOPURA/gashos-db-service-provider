'use strict';
sap.ui.controller('gashos.dbaas.provider.controller.Register', {

    backToLogin: function() {
        window.app.back();
    },

    registerUser: function() {
        var oView = this.getView();
        var oUnameField = oView.byId('uname');
        var oPwdField   = oView.byId('pwd');
        var oPwdConfirmField = oView.byId('confirm_pwd');

        if (!this.passwordsMatch(oPwdField, oPwdConfirmField))
            return;

        AJAXUtils.doPOST({
            url: "api/api.php?action=register",
            data: {
                "Username": oUnameField.getValue(),
                "Password": oPwdField.getValue(),
            },
            success: function(oResponse) {
                console.log(oResponse);
            },
            error: function() {
                console.log(arguments);
            },
        });
    },

    passwordsMatch: function(oPwdField, oPwdConfirmField) {
        var sPassowrd = oPwdField.getValue();
        var sPasswordConfirm = oPwdConfirmField.getValue();
        if (sPassowrd !== sPasswordConfirm) {
            oPwdConfirmField.setValueStateText("Passwords do not match");
            oPwdConfirmField.setValueState(sap.ui.core.ValueState.Error);
            return false;
        }
        return true;
    },

});