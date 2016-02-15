
var app = angular.module('mmrpApp', []);

app.controller('PartController', function($scope, $http) {
  $http.get('part.php?mode=list').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.parts = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('TypeController', function($scope, $http) {
  $http.get('type.php?mode=list').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.types = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});
