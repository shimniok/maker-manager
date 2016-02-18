
var app = angular.module('mmrpApp', []);

//////////////////////////////////////////////////////////////////////
// PRODUCT Controller
//
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

//////////////////////////////////////////////////////////////////////
// PART Controller
//
app.controller('PartController', function($scope, $http){
  $http.get('api/part').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.parts = resp.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
});

//////////////////////////////////////////////////////////////////////
// TYPE Controller
//
app.controller('TypeController', function($scope, $http){
  $scope.newType = {id: 0, name: ""};
  $scope.types = [];

  $http.get('api/type').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.types = resp.data;
    console.log('Success', $scope.types);
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });

  $scope.add = function(){
    console.log('add(): ', $scope.newType);
    $http.post('api/type', $scope.newType ).then(function(resp){
      console.log('Success', resp.data);
      $scope.types[resp.data['id']] = resp.data;
      $scope.newType = {};
    });
  };

  $scope.delete = function(id){
    console.log('delete(): ', $scope.types[id], id);
    $http.delete('api/type/'+id).then(function(resp){
      console.log('Success', resp.data);
      delete $scope.types[id];
    });
  };

});

//////////////////////////////////////////////////////////////////////
// SUBYTPE Controller
//
app.controller('SubtypeController', function($scope, $http) {
  $scope.newSubtype = {id: 0, name: ""};
  $scope.subtypes = [];

  $http.get('api/subtype').then(function(resp) {
    // For JSON responses, resp.data contains the result
    $scope.subtypes = resp.data;
    console.log('Success', $scope.subtypes);
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });

  $scope.add = function(){
    console.log('add(): ', $scope.newSubtype);
    $http.post('api/subtype', $scope.newSubtype ).then(function(resp){
      console.log('Success', resp.data);
      $scope.subtypes[resp.data['id']] = resp.data;
      $scope.newSubtype = {};
    });
  };

  $scope.delete = function(id){
    console.log('delete(): ', $scope.subtypes[id], id);
    $http.delete('api/subtype/'+id).then(function(resp){
      console.log('Success', resp.data);
      delete $scope.subtypes[id];
    });
  };

});
