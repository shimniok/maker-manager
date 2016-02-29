angular.module('MakerMRP')
.directive('mmrpEditable', function(){
  return {
    restrict: 'E',
    templateUrl: 'templates/editable.html',
    scope: {
      id: '@',
      data: '@',
      save: '=',
      remove: '='
    },
    controller: function($scope){
      $scope.selected = null;
      $scope.editing = function(id){
        return $scope.selected === id;
      }
      $scope.edit = function(id){
        $scope.selected = id;
      }
      $scope.cancel = function(){
        $scope.selected = null;
      }
    }
  };
});
