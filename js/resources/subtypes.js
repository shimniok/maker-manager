angular.module('MakerMRP')

  //////////////////////////////////////////////////////////////////////
  // SUBTYPES Resource
  //
  .factory('SubtypesResource', function($resource){
    return $resource('api/subtypes/:id');
  });