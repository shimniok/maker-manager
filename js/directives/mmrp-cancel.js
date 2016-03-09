angular.module('MakerMRP')
.directive('mmrpCancel', function(){
  return {
    restrict: 'E',
    templateUrl: 'templates/cancel.html',
    scope: {
      form: '='
    }
  };
});
