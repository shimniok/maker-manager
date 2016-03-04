angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// SUBYTPE Controller
//
.controller('SubtypeController', function(DataService, $scope) {
  $scope.data = DataService;
  $scope.newSubtype = {id: 0, name: ""};

  /*
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
  */

});
