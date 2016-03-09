angular.module('MakerMRP')

  //////////////////////////////////////////////////////////////////////
  // Data service (data model)
  //
  .factory('DataService', function ($q, $filter, ProductsResource, PartsResource, TypesResource, SubtypesResource, BomsResource) {
    var me = this;
    // Preload data
    this.parts = PartsResource.query();
    this.types = TypesResource.query();
    this.subtypes = SubtypesResource.query();
    this.boms = BomsResource.query();
    this.products = ProductsResource.query();
    this.currentProductId = null;
    this.currentProduct = this.products.$promise.then(function(result) {
      console.log("currentProduct: then()", me.currentProduct);
      me.updateCurrentProduct();
    });
    this.currentBom = this.currentProduct.then(function(result) {
      me.boms.$promise.then(function(result){
        me.parts.$promise.then(function(result){
          me.updateCurrentBom();
          console.log("currentBom: then()", me.currentBom);
        });
      });
    });

    this.getTypeName = function(id) {
      return (id in this.types) ? this.types[id].name : "?";
    };

    this.getSubtypeName = function(id) {
      return (id in this.subtypes) ? this.subtypes[id].name : "?";
    };

    /**
     * Get a specified part
     *
     * @param id of the part being requested
     * @returns part object
     */
    this.getPart = function(id) {
      return $filter('filter')(this.parts, {id: id})[0];
    };

    /**
     * Get a specified product
     *
     * @param id is the id of the product to get
     * @return product object
     */
    this.getProduct = function(id){
      return $filter('filter')(this.products, {id: id})[0];
    }

    /**
     * Gets a list of boms associated with the specified product id
     * @param id of the product for which we retrieve the boms
     * @returns {*}
     */
    this.getBom = function(id) {
      return $filter('filter')(this.boms, {products_id: id});
    }

    this.updateCurrentBom = function() {
      if (me.currentProductId != null) {
        var bom = $filter('filter')(this.boms, { products_id: this.currentProductId });
        me.currentBom = [];
        for (var i=0; i < bom.length; i++) {
          bom[i].part = me.getPart(bom[i].parts_id);
          me.currentBom.push(bom[i]);
        }
      }
    }

    this.updateCurrentProduct = function() {
      if (me.currentProductId != null) {
        console.log("updateCurrentProduct() id:", me.currentProductId);
        me.currentProduct = $filter('filter')(this.products, {id: me.currentProductId})[0];
        console.log("updateCurrentProduct() currentProduct: ", me.currentProduct);
      }
    }

    this.setCurrentProduct = function(id) {
      this.currentProductId = id;
      this.updateCurrentProduct();
      this.updateCurrentBom();
    }

    return this;
  });
