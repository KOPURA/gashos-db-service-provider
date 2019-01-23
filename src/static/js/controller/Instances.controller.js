'use strict';
sap.ui.controller('gashos.dbaas.provider.controller.Instances', {

    logoutUser: function() {
        var that = this;
        var oView = this.getView();
        gashos.dbaas.provider.getIndicator().setBusy(true, oView.getBusyText());

        AJAXUtils.doDELETE({
            url: 'api/user/',
            success: function() {
                if (that.intervalId) {
                    clearInterval(that.intervalId);
                    that.intervalId = null;
                }
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
        var that = this;
        var oDialog = new sap.m.Dialog({
            content: this._createDialogContent(),
            title: "Choose database instance settings",
            icon: "sap-icon://action-settings",
            contentWidth: "640px",
            contentHeight: "200px",
            beginButton: new sap.m.Button({
                text: "Create",
                type: sap.m.ButtonType.Accept,
                press: function(oEvent) {
                    var oDialog = oEvent.getSource().getParent();
                    that._createInstance(oDialog);
                },
            }),
            endButton: new sap.m.Button({
                text: "Cancel",
                type: sap.m.ButtonType.Reject,
                press: function(oEvent) {
                    var oDialog = oEvent.getSource().getParent();
                    oDialog.close();
                }
            }),
            afterClose: function(oEvent) {
                oEvent.getSource().destroy();
            }
        });
        return oDialog;
    },

    _createDialogContent: function() {
        var aFormElements = this._createDialogFormContent();
        var oForm = new sap.ui.layout.form.SimpleForm({
            editable: true,
            layout: sap.ui.layout.form.SimpleFormLayout.ResponsiveGridLayout,
            content: aFormElements,
        });
        return [ oForm ];
    },

    _createDialogFormContent: function() {
        var aValidDBTypes = [{key: 'mysql', text: "MySQL Database"}];
        var aValidTypeSelections = aValidDBTypes.map(x => new sap.ui.core.Item(x));
        return [
            new sap.m.Label({text: "Database Type"}), new sap.m.Select("DBType", {items: aValidTypeSelections}),
        ];
    },

    _createInstance: function(oDialog) {
        var oView = this.getView();
        var oTypeSelect = sap.ui.getCore().byId("DBType");
        var sDBType = oTypeSelect.getSelectedItem().getKey();
        AJAXUtils.doPOST({
            url: "api/instances/",
            data: {
                DBType: sDBType,
            },
            success: function() {
                oDialog.close();
            },
            error: function(iStatusCode, oResponse) {
                var aGeneralErrors = ValidationUtils.fetchGeneralErrors(oResponse['errors']);
                var sError = "Failed to create new instance: \n\n" + aGeneralErrors.join("\n");
                gashos.dbaas.provider.getIndicator().showError(sError);
            },
        });
    },

    startMonitoring: function() {
        var oView = this.getView();
        var oInterval = setInterval(function() {
            AJAXUtils.doGET({
                url: "api/instances/",
                success: function(oResponse) {
                    var oModel = new sap.ui.model.json.JSONModel(oResponse);
                    oView.setModel(oModel);
                },
            });

        }, 2000);
        this.intervalId = oInterval;
    },

});