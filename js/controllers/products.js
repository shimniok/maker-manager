angular.module('MakerMRP')
  //////////////////////////////////////////////////////////////////////
  // PRODUCT Controller
  //
  .controller('ProductController', function(DataService, $scope){
    $scope.data = DataService;
  });
