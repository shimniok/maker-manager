angular.module('MakerMRP')
.directive('mmrpEdit', function(){
  return {
    restrict: 'E',
    templateUrl: 'templates/edit.html',
    scope: {
      form: '='
    }
  };
});
