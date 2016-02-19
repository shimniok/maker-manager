angular.module('MakerMRP', ['ngRoute'])
.config(function($routeProvider){
  $routeProvider
    /*
    .when(base, {
      templateUrl: 'templates/products/index.html'
    })
    .when(base+'/products', {
      templateUrl: 'templates/products/index.hml'
    })
    .when(base+'/subtypes', {
      templateUrl: 'templates/subtypes/index.hml'
    })
    */
    .when('/types', {
      templateUrl: 'templates/types/index.html',
      controller: 'TypeController',
      controllerAs: "typeCtrl"
    })
    .otherwise({ redirectTo: '/types'})
});
