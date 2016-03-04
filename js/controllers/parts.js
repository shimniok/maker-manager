angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// PART Controller
//
.controller('PartController', function(DataService, $scope){
  $scope.data = DataService;
});
