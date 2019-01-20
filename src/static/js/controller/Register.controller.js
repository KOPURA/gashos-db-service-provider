'use strict';
sap.ui.controller('gashos.dbaas.provider.controller.Register', {

    backToLogin: function() {
        window.app.back();
    },

    registerUser: function() {
        var that = this;
        var oView = this.getView();
        var oUnameField = oView.byId('Username');
        var oPwdField   = oView.byId('Password');
        var oPwdConfirmField = oView.byId('ConfirmPassword');
        oView.reset(true);

        if (!this.passwordsMatch(oPwdField, oPwdConfirmField))
            return;

        AJAXUtils.doPOST({
            url: "api/api.php?action=register",
            data: {
                "Username": oUnameField.getValue(),
                "Password": oPwdField.getValue(),
            },
            success: function(oResponse) {
                sap.m.MessageToast.show("Registration successful!", {
                    duration: 100,
                    onClose: function() {
                        that.backToLogin();
                    }
                });
            },
            error: function(iStatusCode, oResponse) {
                var oErrors = oResponse['errors'];
                ValidationUtils.showErrors(oView, ['Username', 'Password'], oErrors);
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