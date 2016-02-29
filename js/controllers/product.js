angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// PRODUCT Controller
//
.controller('ProductController', function($scope, $http){
  $scope.bom = null;
  $scope.parts = null;
  $scope.types = null;
  $scope.subtypes = null;
  $scope.selected = null; // product selected for bom display
  $scope.edited = null;   // product in process of being edited
  $scope.shown = -1;

  $http.get('api/products').then(function(resp){
    console.log('Success', resp);
    $scope.products = resp.data.data;
  }, function(err){
    console.error('ERR', err);
  });

  $http.get('api/types').then(function(resp){
    console.log('Success', resp);
    $scope.types = resp.data.data;
  }, function(err){
    console.error('ERR', err);
  });

  $http.get('api/subtypes').then(function(resp){
    console.log('Success', resp);
    $scope.subtypes = resp.data.data;
  }, function(err){
    console.error('ERR', err);
  });

  $http.get('api/parts').then(function(resp){
    console.log('Success', resp);
    $scope.parts = resp.data.data;
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
