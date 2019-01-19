(function() {
    var oCurrentBusyDialog = undefined;
    gashos.dbaas.provider.getIndicator = function() {
        return {
            setBusy: function(bIsBusy, sBusyText) {
                if (bIsBusy) {
                    oCurrentBusyDialog = new sap.m.BusyDialog({
                        text: sBusyText,
                    });
                    oCurrentBusyDialog.open();
                } else if (typeof oCurrentBusyDialog !== 'undefined') {
                    oCurrentBusyDialog.close();
                    oCurrentBusyDialog.destroy();
                    oCurrentBusyDialog = undefined;
                }
            },
            showError: function(sErrorText) {
                var oDialog = new sap.m.Dialog({
                    title: 'Error',
                    type: sap.m.DialogType.Message,
                    state: sap.ui.core.ValueState.Error,
                    content: new sap.m.Text({
                        text: sErrorText,
                    }),
                    beginButton: new sap.m.Button({
                        text: 'OK',
                        press: function(oEvent) {
                            oDialog.close();
                        }
                    }),
                    afterClose: function(oEvent) {
                        oEvent.getSource().destroy();
                    }
                });
                oDialog.open();
            },
        }
    };
})();