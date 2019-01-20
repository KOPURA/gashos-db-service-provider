sap.ui.jsview("gashos.dbaas.provider.view.Register", {
    
    getControllerName : function () {
        return "gashos.dbaas.provider.controller.Register";
    },
    
    createContent : function(oController) {
        var oForm = this.createRegisterForm(oController);
        var oPage = new sap.m.Page({
            title: "Register",
            content: [ oForm ],
            showNavButton: true,
            navButtonPress: [oController.backToLogin, oController],
        });

        return [ oPage ];
    },

    createRegisterForm: function(oController) {
        var aFormElements = this.createRegisterFormElements(oController);
        var oForm = new sap.ui.layout.form.SimpleForm({
            editable: true,
            title: "Enter your data",
            layout: sap.ui.layout.form.SimpleFormLayout.ResponsiveGridLayout,
            content: aFormElements,
        });

        return oForm;
    },

    createRegisterFormElements: function(oController) {
        this.unameInput = new sap.m.Input(this.createId('Username'), {width: "50%", placeholder: "Type username..."});
        this.pwdInput = new sap.m.Input(this.createId('Password'), {width: "50%", placeholder: "Type password...", type: sap.m.InputType.Password})
        this.pwdConfirmationInput = new sap.m.Input(this.createId('ConfirmPassword'), {width: "50%", placeholder: "Type password again...", type: sap.m.InputType.Password})
        return [
           new sap.m.Label({text: "Username",}), this.unameInput,
           new sap.m.Label({text: "Password",}), this.pwdInput,
           new sap.m.Label({text: "Confirm Password",}), this.pwdConfirmationInput,
           new sap.m.Label({}), new sap.m.Button({
                width: "50%",
                text: "Register",
                press:  [oController.registerUser, oController]}),
        ];
    },

    reset: function(bKeepValues) {
        var aIDs = ['Username', 'Password', 'ConfirmPassword'];
        ValidationUtils.resetErrors(this, aIDs, bKeepValues);
    }
});
