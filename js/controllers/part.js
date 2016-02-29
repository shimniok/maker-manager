angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// PART Controller
//
.controller('PartController', function($scope, $http){

  $http.get('api/parts').then(function(resp){
    console.log('Success', resp);
    // For JSON responses, resp.data contains the result
    $scope.parts = resp.data.data;
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
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

});
