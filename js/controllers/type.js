angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// TYPE Controller
//
.controller('TypeController', function(data, $scope){
  $scope.data = data;
  $scope.new = [];

  /*
  $http.get('api/types').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, response.data contains the result
    $scope.types = resp.data.data;
    console.log('Success', $scope.types);
  }, function(err) {
    console.error('Error', err.status);
    // err.status will contain the status code
  });

  $scope.add = function(){
    console.log('add(): ', JSON.stringify($scope.new));
    $http.post('api/types', JSON.stringify($scope.new)).then(function(resp){
      console.log('Success', resp.data);
      var id = resp.data.id;
      $scope.types[id] = { name: resp.data.name, id: resp.data.id };
      $scope.new = {};
    });
  };

  $scope.remove = function(id){
    console.log('delete(): ', id, $scope.types[id]);
    $http.delete('api/types/'+id).then(function(resp){
      console.log('Success', resp.data);
      delete $scope.types[id];
    });
  };

  $scope.save = function(type){
    console.log('save(): ', type);
    $http.put('api/types/'+type.id, type ).then(function(resp){
      console.log('Success', resp.data);
      $scope.types[type.id] = type;
      $scope.editing = null;
    });
  };
  */

});
