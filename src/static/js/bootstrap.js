'use strict';

function requireResources() {
    jQuery.sap.registerModulePath('gashos.dbaas.provider.root', '/js');
    jQuery.sap.registerModulePath('gashos.dbaas.provider.app', '/js/app');
    jQuery.sap.registerModulePath('gashos.dbaas.provider.view', '/js/view');

    jQuery.sap.require('gashos.dbaas.provider.root.utils');
    jQuery.sap.require('gashos.dbaas.provider.app.Indicator');
    jQuery.sap.require('gashos.dbaas.provider.app.Application');
    jQuery.sap.require('gashos.dbaas.provider.app.AppPage');
}

function createAppliaction() {
    var oApp = new gashos.dbaas.provider.app.Application({
        initialPage: "LoginView",
        visible: false,
        navigate: function(oEvtControl) {
            var oView = oEvtControl.getParameters().to;
            oView.onNav();
        },
    });
    oApp.addPage(new gashos.dbaas.provider.app.AppPage("LoginView"    , "gashos.dbaas.provider.view.Login"));
    oApp.addPage(new gashos.dbaas.provider.app.AppPage("RegisterView" , "gashos.dbaas.provider.view.Register"));
    oApp.addPage(new gashos.dbaas.provider.app.AppPage("InstancesView", "gashos.dbaas.provider.view.Instances"));

    new sap.m.Shell({
            app: oApp,
            title: "Database Service Provider",
    }).placeAt("content");

    oApp.startApplication();
    window.app = oApp;
}

function main() {
    window.gashos = window.gashos || {};
    window.gashos.dbaas = window.gashos.dbaas || {};
    window.gashos.dbaas.provider = window.gashos.dbaas.provider || {};

    requireResources();
    createAppliaction();
}

