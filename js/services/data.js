angular.module('MakerMRP')

  //////////////////////////////////////////////////////////////////////
  // Data service (data model)
  //
  .factory('DataService', function ($q, $filter, ProductsResource, PartsResource, TypesResource, SubtypesResource, BomsResource) {
    var me = this;

    // Pre-load data to ensure speedy-quick response. The resources return a promise that eventually gets resolved.
    // Unfortunately for us, some other properties depend on these. See currentProduct, CurrentBom below.
    //
    this.parts = PartsResource.query();
    this.types = TypesResource.query();
    this.subtypes = SubtypesResource.query();
    this.boms = BomsResource.query();
    this.products = ProductsResource.query();
    this.currentProductId = null;

    // Here we're setting the currentProduct to the promise from products so that
    // when resolved, the currentProduct is updated. This ensures that upon browser
    // refresh, the current product is eventually resolved.
    //
    this.currentProduct = this.products.$promise.then(function(result) {
      console.log("currentProduct: then()", me.currentProduct);
      me.updateCurrentProduct();
    });

    // Here we're setting currentBom to a promise, chained to its dependencies,
    // currentProduct, boms, and parts. Once those resolve, we update the current
    // bom. This ensures that upon browser refresh, the current Bom is eventually resolved.
    //
    this.currentBom = this.currentProduct.then(function(result) {
      me.boms.$promise.then(function(result){
        me.parts.$promise.then(function(result){
          me.updateCurrentBom();
          console.log("currentBom: then()", me.currentBom);
        });
      });
    });

    /**
     * Get the name of the specified type
     *
     * @param id is the id of the type
     * @returns {string} the name of the specified type
     */
    this.getTypeName = function(id) {
      return (id in this.types) ? this.types[id].name : "?";
    };

    /**
     * * Get the name of the specified subtype
     *
     * @param id is the id of the subtype
     * @returns {string} the name of the specified subtype
     */
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

    /**
     * Updates the current bom list based on the currentProductId
     */
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

    /**
     * Updates the current product object based on currentProductId
     */
    this.updateCurrentProduct = function() {
      if (me.currentProductId != null) {
        console.log("updateCurrentProduct() id:", me.currentProductId);
        me.currentProduct = $filter('filter')(this.products, {id: me.currentProductId})[0];
        console.log("updateCurrentProduct() currentProduct: ", me.currentProduct);
      }
    }

    /**
     * Called by a controller to set the currentProductId and subsequently make calls to
     * update current Product and Bom.
     *
     * @param id is the specified product to assign to currentProduct and to use for currentBom
     */
    this.setCurrentProduct = function(id) {
      this.currentProductId = id;
      this.updateCurrentProduct();
      this.updateCurrentBom();
    }

    return this;
  });
