<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Barchart - Watchlist Symbols</title>

    <!-- Load Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sortorder:after {
            content: '\25b2';   // BLACK UP-POINTING TRIANGLE
        }
        .sortorder.reverse:after {
            content: '\25bc';   // BLACK DOWN-POINTING TRIANGLE
        }
    </style>
</head>
<body>

<div class="container" ng-app="myApp" ng-controller="quoteCtrl">
    <div>
        <form>
            <div class="form-group">
                <input class="form-control" type="text" name="symbol" placeholder="Enter a symbol" ng-model="symbol" required/>
                <button class="btn"  ng-click="addSymbol()">Add Symbol</button>
            </div>
        </form>
    </div>
    <!-- Table-to-load-the-data Part -->
    <table class="table table-bordered table-striped" ng-if="quotes">
        <thead>
        <tr>
            <th>
            </th>
            <th>
                <a href="#" ng-click="sortBy('symbol')">
                    Symbol
                    <span class="sortorder" ng-show="propertyName === 'symbol'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('name')">
                    Symbol Name
                    <span class="sortorder" ng-show="propertyName === 'name'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('last')">
                    Last Price
                    <span class="sortorder" ng-show="propertyName === 'last'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('change')">
                    Change
                    <span class="sortorder" ng-show="propertyName === 'change'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('pctchange')">
                    %Change
                    <span class="sortorder" ng-show="propertyName === 'pctchange'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('volume')">
                    Volume
                    <span class="sortorder" ng-show="propertyName === 'volume'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th>
                <a href="#" ng-click="sortBy('tradetime')">
                    Time
                    <span class="sortorder" ng-show="propertyName === 'tradetime'" ng-class="{reverse: reverse}"></span>
                </a>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="quote in quotes | orderBy:propertyName:reverse">
            <td>{{ $index + 1 }}</td>
            <td>{{ quote.symbol }}</td>
            <td>{{ quote.name }}</td>
            <td>{{ quote.last }}</td>
            <td>{{ quote.change }}</td>
            <td>{{ quote.pctchange }}</td>
            <td>{{ quote.volume | number }}</td>
            <td>{{ quote.tradetime }}</td>
            <td>
                <button class="btn btn-danger btn-xs btn-delete" ng-click="confirmDelete(quote.symbol)">Delete</button>
            </td>
        </tr>
        </tbody>
    </table>
    <div ng-if="!quotes">
        There are no symbols in your watchlist, please add one
    </div>
    <!-- End of Table-to-load-the-data Part -->

</div>

<!-- Load Javascript Libraries (AngularJS, JQuery, Bootstrap) -->
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<!-- AngularJS Application Script Part -->
<script>
    var app = angular.module('myApp', []);
    app.controller('quoteCtrl', function($scope, $http) {
        $scope.propertyName = 'symbol';
        $scope.reverse = false;

        /* The -R- part */
        $http.get("/watchlist")
            .success(function(response) {
                $scope.quotes = response.quotes;
            });

        $scope.addSymbol = function(){
            if( $scope.symbol == "undefined" || $scope.symbol == null){
                return;
            }
            console.log($scope.quotes);
            if($scope.isSymbolExist($scope.symbol)){
                console.log($scope.symbol);
                alert("This symbol has already been added to the watchlist");
                return;
            }
            $http({
                method  : 'POST',
                url     : 'watchlist',
                data    : {'symbol': $scope.symbol},  // pass in data as strings
                headers : { 'Content-Type': 'application/json' }  // set the headers so angular passing info as form data (not request payload)
            }).
            success(function(response){
                if(typeof $scope.quotes == "undefined"){
                    $scope.quotes = response.quote;
                }else{
                    $scope.quotes.push(response.quote[0]);
                }

            }).
            error(function(response){
                // console.log(response);
                var msg = '';
                for (var property in response) {
                    if (response.hasOwnProperty(property)) {
                        msg += response[property] + "\n";
                    }
                }
                alert('Incomplete Form:\n' + msg);
            });
        };

        $scope.confirmDelete = function(symbol){
            var isOkDelete = confirm('Is it ok to delete this?');
            if(isOkDelete){
                $http.delete('/watchlist/' + symbol, {symbol:symbol}).
                success(function(data){
                    location.reload();
                }).
                error(function(data) {
                    console.log(data);
                    alert('Unable to delete');
                });
            } else {
                return false;
            }
        };

        $scope.sortBy = function(propertyName) {
            $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
            $scope.propertyName = propertyName;
        };

        $scope.isSymbolExist = function(symbol){
            for(var i in $scope.quotes){
                if($scope.quotes[i].symbol === symbol){
                    return true;
                }
            }
            return false;
        }
    });
</script>
<!-- End of AngularJS Application Script Part -->

</body>
</html>