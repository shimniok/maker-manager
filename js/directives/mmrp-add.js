angular.module('MakerMRP')
.directive('mmrpAdd', function(){
  return {
    restrict: 'E',
    templateUrl: 'templates/mmrp_add.html',
    scope: {
      form: '='
    }
  };
});
