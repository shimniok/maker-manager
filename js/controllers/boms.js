angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// PART Controller
//
.controller('BomController', function(DataService, $scope, $stateParams){
  $scope.data = DataService;
  // Update the current product
  DataService.setCurrentProduct($stateParams.id);
  $scope.product = DataService.currentProduct;
  console.log("BomController: product", $scope.product);
});
