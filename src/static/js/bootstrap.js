'use strict';

function requireResources() {
    jQuery.sap.registerModulePath('gashos.dbaas.provider.root', '/js');
    jQuery.sap.registerModulePath('gashos.dbaas.provider.app', '/js/app');
    jQuery.sap.registerModulePath('gashos.dbaas.provider.view', '/js/view');

    jQuery.sap.require('gashos.dbaas.provider.root.utils');
}

function createDashboard() {
    var oApp = new sap.m.App({
        initialPage: "LoginView",
        navigate: function(oEvtControl) {
            var oView = oEvtControl.getParameters().to;
            if (typeof oView.reset === 'function')
                oView.reset();
        },
    });
    oApp.addPage(sap.ui.jsview("LoginView", "gashos.dbaas.provider.view.Login"));
    oApp.addPage(sap.ui.jsview("RegisterView", "gashos.dbaas.provider.view.Register"));

    new sap.m.Shell({
            app: oApp,
            title: "Database Service Provider",
    }).placeAt("content");

    window.app = oApp;
}

function main() {
    window.gashos = window.gashos || {};
    window.gashos.dbaas = window.gashos.dbaas || {};
    window.gashos.dbaas.provider = window.gashos.dbaas.provider || {};

    requireResources();
    createDashboard();
}

