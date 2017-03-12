AppRoot.controller("extensionController", ['$scope', '$rootScope', 'MainEvent', function($scope, $rootScope, MainEvent) {
    $scope.enableExtension = false;
    INKXEAPP.EXTENSION = (function() {
        var onReady = function() {
                var sid = getQueryStringVal("sid");
                if (sid) {
                    $scope.enableExtension = false;
                } else {
                    $scope.enableExtension = true;
                }
            },
            getQueryStringVal = function(param) {
                var _queryStringParam = getQueryStrings();
                if (_queryStringParam != null) {
                    if (_queryStringParam[param] != undefined) {
                        return _queryStringParam[param];
                    } else {
                        return false;
                    }
                }
                return false;
            },
            getQueryStrings = function() {
                var _queryStringParam = null,
                    _requestUrl = window.parent.location.search.toString();
                if (_requestUrl != '') {
                    _requestUrl = _requestUrl.substring(1);
                    _queryStringParam = new Array();
                    var _keyValuePairs = _requestUrl.split('&');
                    for (var i = 0; i < _keyValuePairs.length; i++) {
                        var keyValue = _keyValuePairs[i].split('=');
                        _queryStringParam[keyValue[0]] = keyValue[1];
                    }
                }
                return _queryStringParam;
            },
            saleThisDesign = function() {
                //Here is your custom codes
                $scope.$emit(MainEvent.SAVE_DESIGN_STATE_SINGLETON);
                $rootScope.$on(MainEvent.ON_SAVE_DESIGN_STATE_SINGLETON, function(event, pData) {
                    if (pData.status == "success") {
                        console.log("refid  = " + pData.refid);
                        console.log("configured product id  = " + pData.confId);
                        if (confirm("Do you want to sale this design?") == true) {
                            window.location.href = "http://alpha.cfcdi.org/rawr-shops/udprod/vendor/products/?productid=2391";
                        }
                    }
                });
            },
            customFunction2 = function() {
                //Here is your custom codes
            };
        return {
            saleThisDesign: saleThisDesign,
            onReady: onReady,
            customFunction2: customFunction2
        };
    })();
}]);