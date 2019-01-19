sap.ui.jsview("gashos.dbaas.provider.view.Login", {
    
    getControllerName : function () {
        return "gashos.dbaas.provider.controller.Login";
    },
    
    createContent : function(oController) {
        var oForm = this.createLoginForm(oController);
        var oFooter = this.createFooter(oController);
        var oPage = new sap.m.Page({
            title: "Login",
            content: [ oForm ],
            footer:  [ oFooter ],
        });

        return [ oPage ];
    },

    createLoginForm: function(oController) {
        var aFormElements = this.createLoginFormElements(oController);
        var oForm = new sap.ui.layout.form.SimpleForm({
            editable: true,
            title: "Enter credentials",
            layout: sap.ui.layout.form.SimpleFormLayout.ResponsiveGridLayout,
            content: aFormElements,
        });

        return oForm;
    },

    createLoginFormElements: function(oController) {
        return [
           new sap.m.Label({text: "Username",}), new sap.m.Input({width: "50%", placeholder: "Type username..."}),
           new sap.m.Label({text: "Password",}), new sap.m.Input({width: "50%", placeholder: "Type password...", type: sap.m.InputType.Password}),
           new sap.m.Label({}), new sap.m.Button({width: "50%", text: "Login"}),
        ];
    },

    createFooter: function(oController) {
        return new sap.m.Toolbar({
            content: [
                new sap.m.ToolbarSpacer(),
                new sap.m.Button({
                    text: "Create account",
                    type: sap.m.ButtonType.Emphasized,
                    press: [oController.toRegister, oController],
                }),
            ]
        });
    },
});
