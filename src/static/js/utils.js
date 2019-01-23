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
