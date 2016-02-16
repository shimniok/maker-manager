
var app = angular.module('mmrpApp', []);

app.controller('ProductController', function($scope, $http){
  $scope.bom = null;
  $http.get('api/product/list').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.products = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('PartController', function($scope, $http) {
  $http.get('api/part/list').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.parts = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('TypeController', function($scope, $http) {
  $http.get('api/type/list').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.types = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('SubtypeController', function($scope, $http) {
  $http.get('api/subtype/list').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.subtypes = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});
