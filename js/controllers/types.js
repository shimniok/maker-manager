angular.module('MakerMRP')
  //////////////////////////////////////////////////////////////////////
  // TYPE Controller
  //
  .controller('TypeController', function (DataService, TypesResource, $scope) {
    $scope.data = DataService;
    $scope.new = new TypesResource();

    /**
     * Add the new Type to the data model
     * Depends on $scope.new
     */

    $scope.add = function() {
      console.log("TypeController.add()");
      $scope.new.$save(function(type) {
        DataService.types.push(type);
        $scope.new = new TypesResource();
        console.log("TypeController.add()", type);
      });
    };

    /*
    $scope.del = function(type) {
      if (typeof(type) === 'TypesResource') {
        type.$remove();
      }
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
