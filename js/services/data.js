angular.module('MakerMRP')
.factory('data', ['$http', function($http){
	var data = {};

	data.products = [];
	data.parts = [];
	data.boms = [];
	data.types = [];
	data.subtypes = [];

  $http.get('api/parts').then(function(resp) {
    data.parts = resp.data.data;
    console.log('data: api/parts Success', data.parts);
  }, function(err) {
    console.error('ERR', err);
    // err.status will contain the status code
  });

  $http.get('api/products').then(function(resp) {
    data.products = resp.data.data;
    console.log('data: api/products Success', data.products);
  }, function(err) {
    console.error('data: api/products ERR', err);
    // err.status will contain the status code
  });

	/*
  $http.get('api/boms').then(function(resp) {
    this.boms = resp.data.data;
    console.log('data: api/boms Success', boms);
  }, function(err) {
    console.error('data: api/boms ERR', err);
    // err.status will contain the status code
  });
	*/

  $http.get('api/types').then(function(resp) {
    data.types = resp.data.data;
    console.log('data: api/types Success', data.types);
  }, function(err) {
    console.error('data: api/types ERR', err);
    // err.status will contain the status code
  });

  $http.get('api/subtypes').then(function(resp) {
    data.subtypes = resp.data.data;
    console.log('data: api/subtypes Success', data.subtypes);
  }, function(err) {
    console.error('data: api/subtypes ERR', err);
    // err.status will contain the status code
  });

	return data;
}]);
