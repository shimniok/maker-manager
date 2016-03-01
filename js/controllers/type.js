angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// TYPE Controller
//
.controller('TypeController', function($http, $scope){
  $scope.types = [];
  $scope.new = [];

  $http.get('api/types').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, response.data contains the result
    $scope.types = resp.data.data;
    console.log('Success', $scope.types);
  }, function(err) {
    console.error('Error', err.status);
    $scope.errors = err.status;
    // err.status will contain the status code
  });

  $scope.add = function(){
    console.log('add(): ', $scope.new);
    $http.post('api/types', $scope.new).then(function(resp){
      console.log('Success', resp.data);
      $scope.types[resp.data['id']] = resp.data;
      controller.new = {};
    });
  };

  $scope.delete = function(id){
    console.log('delete(): ', controller.types[id], id);
    $http.delete('api/types/'+id).then(function(resp){
      console.log('Success', resp.data);
      delete controller.types[id];
    });
  };

  $scope.save = function(type){
    console.log('save(): ', type);
    $http.put('api/types/'+type.id, type ).then(function(resp){
      console.log('Success', resp.data);
      controller.types[type.id] = type;
      controller.editing = null;
    });
  };

});
