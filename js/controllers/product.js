angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// PRODUCT Controller
//
.controller('ProductController', function($scope, $http, $location){
  $scope.products = null;
  $scope.parts = null;
  $scope.types = null;
  $scope.subtypes = null;

  $http.get('api/products').then(function(resp){
    console.log('products: Success', resp);
    $scope.products = resp.data.data;
  }, function(err){
    console.error('ERR', err);
  });

  $scope.select = function(id){
    if (id !== ''){
      $http.get('api/bom/'+id).then(function(resp){
        console.log('Success', resp);
        var bom = resp.data.data;
        $scope.bom = [];
        var i=0;
        for (part in bom){
          $scope.bom[ i ] = $scope.parts[ bom[part].parts_id ];
          i++;
        }
        $scope.selected = $scope.products[id];
      }, function(err){
        console.error('ERR', err);
      });
    }
  };

});
