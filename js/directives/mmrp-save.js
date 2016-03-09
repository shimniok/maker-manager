angular.module('MakerMRP')
.directive('mmrpSave', function(){
  return {
    restrict: 'E',
    templateUrl: 'templates/save.html',
    scope: {
      form: '='
    }
  };
});
