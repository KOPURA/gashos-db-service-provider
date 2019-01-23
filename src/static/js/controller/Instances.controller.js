'use strict';
sap.ui.controller('gashos.dbaas.provider.controller.Instances', {

    logoutUser: function() {
        var oView = this.getView();
        gashos.dbaas.provider.getIndicator().setBusy(true, oView.getBusyText());

        AJAXUtils.doDELETE({
            url: 'api/user/',
            success: function() {
                window.app.backToTop();
            },
            error: function(iStatus, oResponse) {
                var aErrors = ValidationUtils.fetchGeneralErrors(oResponse['errors']) || [];
                var sErrorString = aErrors.length ? aErrors.join("\n") : "Internal server error";
                gashos.dbaas.provider.getIndicator().showError("Logout failed: \n\n" + sErrorString);
            },
            finally: function() {
                jQuery.sap.delayedCall(500, this, function() {
                    gashos.dbaas.provider.getIndicator().setBusy(false);
                });
            }
        });
    },

    createDBInstance: function() {
        var oDialog = this._createDBSettingsDialog();
        oDialog.open();
    },

    _createDBSettingsDialog: function() {
        var aValidDBTypes = ['mysql'];
        var oDialog = new sap.m.Dialog({
            content: this._createDialogContent(),
            beginButton: new sap.m.Button({
                text: "Create",
                type: sap.m.ButtonType.Accept,
                press: function(oEvent) {
                    console.log("Creating....");
                },
            }),
            endButton: new sap.m.Button({
                text: "Cancel",
                type: sap.m.ButtonType.Reject,
                press: function(oEvent) {
                    oEvent.getSource().getParent().close();
                }
            }),
            afterClose: function(oEvent) {
                oEvent.getSource().destroy();
            }
        });
        return oDialog;
    },

    _createDialogContent: function() {
        
    },

});