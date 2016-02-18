
var app = angular.module('mmrpApp', []);

app.controller('ProductController', function($scope, $http){
  $scope.bom = null;
  $http.get('api/product').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.products = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('PartController', function($scope, $http) {
  $http.get('api/part').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.parts = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('TypeController', function($scope, $http) {
  $http.get('api/type').then(function(resp) {
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.types = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

app.controller('SubtypeController', function($scope, $http) {
  $scope.new = {id: 0, name: ""};
  $scope.subtypes = [];

  $http.get('api/subtype').then(function(resp) {
    // For JSON responses, resp.data contains the result
    //$scope.subtypes = Object.keys(resp.data).map(function(k) {
    //  return resp.data[k];
    //});
    $scope.subtypes = resp.data;
    console.log('Success', $scope.subtypes);
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });

  $scope.add = function(){
    console.log('add(): ', $scope.newName);
    $http.post('api/subtype', $scope.new ).then(function(resp) {
      console.log('Success', resp.data);
      $scope.subtypes[resp.data['id']] = resp.data;
      $scope.new = {};
    });
  };

  $scope.delete = function(id){
    console.log('delete(): ', $scope.subtypes[id], id);
    delete $scope.subtypes[id];
    $http.delete('api/subtype/'+id).then(function(resp) {
      console.log('Success', resp.data);
    });
  }

});


app.factory('dataFactory', ['$http', function($http) {

   var urlBase = '/api/';
   var productUrl = '/product';
   var dataFactory = {};

   dataFactory.getProducts = function () {
       return $http.get(urlBase + productUrl);
   };

   dataFactory.getProducts = function (id) {
       return $http.get(urlBase + productUrl+ '/' + id);
   };

   dataFactory.insertProduct = function (cust) {
       return $http.post(urlBase + productUrl, cust);
   };

   dataFactory.updateProduct = function (cust) {
       return $http.put(urlBase + productUrl + '/' + cust.ID, cust)
   };

   dataFactory.deleteProduct = function (id) {
       return $http.delete(urlBase + productUrl + '/' + id);
   };

   return dataFactory;
}]);
