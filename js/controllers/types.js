angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// TYPE Controller
//
.controller('TypeController', function(DataService, $scope){
    $scope.data = DataService;
    $scope.new = [];

  /*
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
