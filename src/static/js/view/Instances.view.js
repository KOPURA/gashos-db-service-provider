gashos.dbaas.provider.app.page("gashos.dbaas.provider.view.Instances", {
    
    getControllerName : function () {
        return "gashos.dbaas.provider.controller.Instances";
    },
    
    createContent : function(oController) {
        var oTable = this._createInstancesTable();
        var oPage = new sap.m.Page({
            title: "Database Instances",
            content: [oTable],
            headerContent: this._createHeader(oController),
            footer: this._createFooter(oController),
        });

        return [ oPage ];
    },

    onNav: function() {
        gashos.dbaas.provider.app.AppPage.prototype.onNav.apply(this);
        this.getController().startMonitoring();
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

    _createInstancesTable: function() {
        var aColumns = [
            new sap.m.Column({
                width: '22%',
                hAlign: sap.ui.core.TextAlign.Begin,
                vAlign: sap.ui.core.VerticalAlign.Middle,
                header: new sap.m.Text({
                    text: 'Instance ID',
                }),
            }),
            new sap.m.Column({
                header: new sap.m.Text({
                    text: 'Status',
                }),
                width: '10%',
                hAlign: sap.ui.core.TextAlign.Center,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
            new sap.m.Column({
                header: new sap.m.Text({
                    text: 'Hostname',
                }),
                width: '32%',
                hAlign: sap.ui.core.TextAlign.Begin,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
            new sap.m.Column({
                header: new sap.m.Text({
                    text: 'Database name',
                }),
                width: '10%',
                hAlign: sap.ui.core.TextAlign.Center,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
            new sap.m.Column({
                header: new sap.m.Text({
                    text: 'Database User',
                }),
                width: '10%',
                hAlign: sap.ui.core.TextAlign.Center,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
            new sap.m.Column({
                header: new sap.m.Text({
                    text: 'Create Time',
                }),
                width: '8%',
                hAlign: sap.ui.core.TextAlign.Center,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
            new sap.m.Column({
                width: '8%',
                hAlign: sap.ui.core.TextAlign.Center,
                vAlign: sap.ui.core.VerticalAlign.Middle,
            }),
        ];
        var oTable = new sap.m.Table({
            noDataText: "There are no instances",
            columns: aColumns,
            items: {
                path: "/instances",
                factory: this._createItems.bind(this),
            }
        });
        return oTable;
    },

    _createItems: function(sID, oContext) {
        return new sap.m.ColumnListItem({
            cells: this._createCells(oContext.getObject()),
        });
    },

    _createCells: function(oRow) {
        var oController = this.getController();
        var sStatus = oRow.STATUS;
        return [
            new sap.m.Text({text: oRow.INSTANCE_ID}),
            new sap.ui.core.Icon({
                src: FormatterUtils.getIconByStatus(sStatus),
                color: FormatterUtils.getIconColorByStatus(sStatus),
                tooltip: FormatterUtils.getTooltipByStatus(sStatus, oRow.ERROR),
            }),
            new sap.m.Text({text: oRow.PUBLIC_DNS}),
            new sap.m.Text({text: oRow.DB_NAME}),
            new sap.m.Text({text: oRow.DB_USER}),
            new sap.m.Text({text: oRow.CREATE_TIME}),
            new sap.m.Button({
                text: "Delete",
                press: [oController.deleteInstance, oController],
            })
        ];
    },
});
