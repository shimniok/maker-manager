angular.module('MakerMRP')
//////////////////////////////////////////////////////////////////////
// TYPE Controller
//
.controller('TypeController', function($http){
  var controller = this;
  this.newType = {id: 0, name: ""};
  this.types = [];
  this.editing = null;
  this.errors = null;

  $http.get('api/type').then(function(response){
    console.log('Success', response);
    // For JSON responses, response.data contains the result
    controller.types = response.data;
    console.log('Success', controller.types);
  }, function(err) {
    console.error('Error', err.status);
    controller.errors = err.status;
    // err.status will contain the status code
  });

  this.add = function(){
    console.log('add(): ', controller.newType);
    this.editing = null;
    $http.post('api/type', controller.newType ).then(function(response){
      console.log('Success', response.data);
      controller.types[response.data['id']] = response.data;
      controller.newType = {};
    });
  };

  this.delete = function(id){
    console.log('delete(): ', controller.types[id], id);
    $http.delete('api/type/'+id).then(function(response){
      console.log('Success', response.data);
      delete controller.types[id];
    });
  };

  this.save = function(type){
    console.log('save(): ', type);
    $http.put('api/type'+type.id, type ).then(function(response){
      console.log('Success', response.data);
      controller.types[type.id] = type;
      controller.editing = null;
    });
  };

});
