angular.module('MakerMRP', ['ngRoute', 'xeditable'])
.config(function($routeProvider){
  $routeProvider
    .when('/products', {
      templateUrl: 'templates/products.html',
      controller: 'ProductController'
    })
    .when('/parts', {
      templateUrl: 'templates/parts.html'
    })
    .when('/types', {
      templateUrl: 'templates/types.html',
    })
    .when('/subtypes', {
      templateUrl: 'templates/subtypes.html'
    })
    .otherwise({ redirectTo: '/products'})
})
.config(['$httpProvider', function($httpProvider) {
  //initialize get if not there
  if (!$httpProvider.defaults.headers.get) {
      $httpProvider.defaults.headers.get = {};
  }
  //disable IE ajax request caching
  $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
  // extra
  $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
  $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
}])
.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});
