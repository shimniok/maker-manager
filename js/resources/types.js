angular.module('MakerMRP')

    //////////////////////////////////////////////////////////////////////
    // TYPE Resource
    //
    .factory('TypesResource', function($resource){
        return $resource('api/types/:id');
    });