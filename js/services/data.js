angular.module('MakerMRP')

    //////////////////////////////////////////////////////////////////////
    // Data service (data model)
    //
    .factory('DataService', function () {
        this.products = [];
        this.parts = [];
        this.boms = [];
        this.subtypes = [];

        this.getTypeName = function(id) {
          console.log("ID=", id, "Types: ", this.types[id]);
          return (id in this.types) ? this.types[id].name : "?";
        };

        this.getSubtypeName = function(id) {
          return (id in this.subtypes) ? this.subtypes[id].name : "?";
        };

        return this;
    });
