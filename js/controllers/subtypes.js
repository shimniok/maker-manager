angular.module('MakerMRP')
  //////////////////////////////////////////////////////////////////////
  // SUBYTPE Controller
  //
  .controller('SubtypeController', function (DataService, SubtypesResource, $scope) {
    $scope.data = DataService;
    $scope.new = new SubtypesResource();

    $scope.add = function () {
      console.log('SubtypeController.add(): ', $scope.new);
      $scope.new.$save(function (subtype) {
        DataService.subtypes.push(subtype);
        $scope.new = new SubtypesResource();
        console.log("SubtypeController.add()", subtype);
      });
    };
    
    /*
     $scope.delete = function(id){
     console.log('delete(): ', $scope.subtypes[id], id);
     $http.delete('api/subtype/'+id).then(function(resp){
     console.log('Success', resp.data);
     delete $scope.subtypes[id];
     });
     };
     */

  });
