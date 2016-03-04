angular.module('MakerMRP')

  //////////////////////////////////////////////////////////////////////
  // PRODUCTS Resource
  //
  .factory('ProductsResource', function($resource){
    return $resource('api/products/:id');
  })

  //////////////////////////////////////////////////////////////////////
  // PARTS Resource
  //
  .factory('PartsResource', function($resource){
    return $resource('api/parts/:id');
  })

  //////////////////////////////////////////////////////////////////////
  // BOMS Resource
  //
  .factory('BomsResource', function($resource){
    return $resource('api/boms/:id');
  })

  //////////////////////////////////////////////////////////////////////
  // SUBTYPES Resource
  //
  .factory('TypesResource', function($resource){
    return $resource('api/types/:id');
  })

  //////////////////////////////////////////////////////////////////////
  // SUBTYPES Resource
  //
  .factory('SubtypesResource', function($resource){
    return $resource('api/subtypes/:id');
  });