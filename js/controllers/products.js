angular.module('MakerMRP')
  //////////////////////////////////////////////////////////////////////
  // PRODUCT Controller
  //
  .controller('ProductController', function(DataService, ProductsResource, $scope){
    $scope.data = DataService;
    $scope.new = new ProductsResource();
    
    $scope.add = function() {
      console.log("ProductController.add()");
      $scope.new.$save(function(product) {
        DataService.products.push(product);
        $scope.new = new ProductsResource();
        console.log("ProductController.add()", product);
      });
    };
  });
