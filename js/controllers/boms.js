angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// PART Controller
//
.controller('BomController', function(DataService, $scope, $stateParams){
  $scope.data = DataService;
  $scope.id = $stateParams.id;
  $scope.bom = [];

  /*
  $http.get('api/bom/'+$stateParams.id).then(function(resp){
    console.log('products.bom(id): api/bom Success', resp);
    $scope.bom = resp.data.data;

    $scope.product = null;
    $scope.parts = null;
    $scope.types = null;
    $scope.subtypes = null;

    $http.get('api/products/'+$stateParams.id).then(function(resp){
      console.log('products.bom(id): api/products Success', resp);
      $scope.product = resp.data;
    }, function(err){
      console.error('ERR', err);
    });

    $http.get('api/parts').then(function(resp){
      console.log('products.bom(id): api/parts Success', resp);
      $scope.parts = resp.data.data;
    }, function(err){
      console.error('ERR', err);
    });

    $http.get('api/types').then(function(resp){
      console.log('products.bom(id): api/types Success', resp);
      $scope.types = resp.data.data;
    }, function(err){
      console.error('ERR', err);
    });

    $http.get('api/subtypes').then(function(resp) {
      console.log('products.bom(id): api/subtypes Success', resp);
      $scope.subtypes = resp.data.data;
    }, function(err){
      console.error('ERR', err);
    });

  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });
  */
});
