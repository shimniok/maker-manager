angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// PRODUCT Controller
//
.controller('ProductController', function(data, $scope){
  $scope.data = data;

  /*
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
  */

});
