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

if (!JSON.parseWithLogging) {
    JSON.parseWithLogging = function(sJSONString) {
        try {
            return JSON.parse(sJSONString);
        } catch(e) {
            var sError = "Invalid JSON string: " + sJSONString;
            console.error(sError);
            return {
                errors: [ sError ],
            };
        }
    }
}

window.ValidationUtils = {
    fetchGeneralErrors: function(oErrors) {
        return Object.keys(oErrors).map(x => oErrors[x]).filter(x => typeof x === 'string');
    }
}

window.AJAXUtils = {
    doGET: function(oInput) {
        this._request('GET', oInput);
    },

    doPOST: function(oInput) {
        this._request('POST', oInput);
    },

    doDELETE: function(oInput) {
        this._request('DELETE', oInput);
    },

    _request: function(sMethod, oInput) {
        var fnSuccess = (typeof oInput.success === 'function') ? oInput.success : function() {}
        var fnError   = (typeof oInput.error === 'function')   ? oInput.error : function() {}
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
                var oJSON = JSON.parseWithLogging(sData);
                fnSuccess(oJSON);
            },
            error: function(oJXR, sTextStatus, sError) {
                var iStatusCode = oJXR.status;
                var sResponse = oJXR.responseText;
                var oJSONResponse = JSON.parseWithLogging(sResponse);
                fnError(iStatusCode, oJSONResponse);
            },
            complete: fnFinally
        });
    }
};

window.FormatterUtils = {
    getIconByStatus: function(sStatus) {
        if (sStatus === 'Running') {
            return 'sap-icon://message-success';
        } else if (sStatus === 'Initializing') {
            return 'sap-icon://pending';
        } else if (sStatus === 'Error') {
            return 'sap-icon://status-error';
        } else if (sStatus === 'Stopping') {
            return 'sap-icon://stop';
        } else {
            return 'sap-icon://question-mark';
        }
    },

    getIconColorByStatus: function(sStatus) {
        if (sStatus === 'Running') {
            return sap.ui.core.IconColor.Positive;
        } else if (sStatus === 'Initializing') {
            return sap.ui.core.IconColor.Critical;
        } else if (sStatus === 'Error') {
            return sap.ui.core.IconColor.Negative;
        } else if (sStatus === 'Stopping') {
            return sap.ui.core.IconColor.Critical;
        } else {
            return sap.ui.core.IconColor.Neutral;
        }
    },

    getTooltipByStatus: function(sStatus, sError) {
        if (sStatus === 'Running') {
            return 'Instance running';
        } else if (sStatus === 'Initializing') {
            return 'Instance is being created';
        } else if (sStatus === 'Error') {
            return 'Instance couldn\' be created: ' + sError;
        } else if (sStatus === 'Stopping') {
            return 'Instance is being terminated';
        } else {
            return '';
        }

    },

    canDeleteInstance: function(sStatus) {
        return sStatus === 'Running';
    }
}
