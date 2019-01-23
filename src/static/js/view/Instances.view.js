gashos.dbaas.provider.app.page("gashos.dbaas.provider.view.Instances", {
    
    getControllerName : function () {
        return "gashos.dbaas.provider.controller.Instances";
    },
    
    createContent : function(oController) {
        var oPage = new sap.m.Page({
            title: "Database Instances",
            content: [

            ],
            headerContent: this._createHeader(oController),
            footer: this._createFooter(oController),
        });

        return [ oPage ];
    },

    onNav: function() {
        var oController = this.getController();
        oController.startMonitoring();
        // Start the monitoring of the instances;
        // Also, stop it on logout !!!!
    },

    _createHeader: function(oController) {
        return new sap.m.Button({
            text: "Logout",
            icon: "sap-icon://log",
            type: sap.m.ButtonType.Ghost,
            press: [oController.logoutUser, oController],
        });
    },

    _createFooter: function(oController) {
        return new sap.m.Toolbar({
            content: [
                new sap.m.Button({
                    text: "Create new instance",
                    type: sap.m.ButtonType.Emphasized,
                    icon: "sap-icon://it-host",
                    press: [oController.createDBInstance, oController],
                }),
            ],
        });
    },
});
