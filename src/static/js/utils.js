if (!Object.keys) {
    Object.keys = function(oObject) {
        var aKeys = [];
        $.each(oObject, function(key, value) {
            aKeys.push(key);
        });
        return aKeys;
    };
}

if (!Object.ensureExists) {
    Object.ensureExists = function(sPath, sDelimiter) {
        var aComponents = sPath.split(sDelimiter);
        var oCurrentObject = window;
        $.each(aComponents, function(iIdx, sComponent) {
            oCurrentObject[sComponent] = oCurrentObject[sComponent] || {};
            oCurrentObject = oCurrentObject[sComponent];
        });
    }
}

window.ValidationUtils = {
    resetErrors: function(oView, aIds, bKeepValues) {
        $.each(aIds, function(iIdx, sId) {
            var oControl = oView.byId(sId);
            oControl.setValueState(sap.ui.core.ValueState.None);
            oControl.setValueStateText("");
            if (!bKeepValues) {
                oControl.setValue("");
            }
        });
    },

    showErrors: function(oView, aIds, oErrors) {
        $.each(aIds, function(iIdx, sId) {
            var aRelevantErrors = oErrors[sId] || [];
            if (!aRelevantErrors.length)
                return;

            var sErrorString = aRelevantErrors.join("\n");
            var oControl = oView.byId(sId);
            oControl.setValueState(sap.ui.core.ValueState.Error);
            oControl.setValueStateText(sErrorString);
        });
        var aGeneralErrors  = Object.keys(oErrors).map(x => oErrors[x]).filter(x => typeof x === 'string');
        if (aGeneralErrors.length) {
            var sGeneralErrorsString = aGeneralErrors.join("\n");
            sap.m.MessageToast.show(sGeneralErrorsString);
        }
    },
}

window.AJAXUtils = {
    doGET: function(oInput) {
        this._request('GET', oInput);
    },

    doPOST: function(oInput) {
        this._request('POST', oInput);
    },

    _request: function(sMethod, oInput) {
        var fnSuccess = (typeof oInput.success === 'function') ? oInput.success : function() {}
        var fnError   = (typeof oInput.error === 'function') ? oInput.error : function() {}
        var fnFinally = (typeof oInput.finally === 'function') ? oInput.finally : function() {}
        var bIsAsync  = oInput.async || true;
        var sURL      = oInput.url;
        var sData     = JSON.stringify(oInput.data || {});
        if (typeof sURL === 'undefined') {
            throw new Error('URL cannot be undefined');
        }

        $.ajax({
            url: sURL,
            async: bIsAsync,
            type: sMethod,
            cache: false,
            dataType: 'text',
            contentType: 'application/json; charset=UTF-8',
            data: sData,
            success: function(sData) {
                var oJSON = JSON.parse(sData);
                fnSuccess(oJSON);
            },
            error: function(oJXR, sTextStatus, sError) {
                var iStatusCode = oJXR.status;
                var sResponse = oJXR.responseText;
                var oJSONResponse = JSON.parse(sResponse);
                fnError(iStatusCode, oJSONResponse);
            },
            complete: fnFinally
        });
    }
};
