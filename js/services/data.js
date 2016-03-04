angular.module('MakerMRP')

    //////////////////////////////////////////////////////////////////////
    // Data service (data model)
    //
    .factory('DataService', function () {
        var data = {};

        data.products = [];
        data.parts = [];
        data.boms = [];
        data.subtypes = [];

        /*
        $http.get('api/parts').then(function (resp) {
            data.parts = resp.data.data;
            console.log('data: api/parts Success', data.parts);
        }, function (err) {
            console.error('ERR', err);
            // err.status will contain the status code
        });

        $http.get('api/products').then(function (resp) {
            data.products = resp.data.data;
            console.log('data: api/products Success', data.products);

            $http.get('api/boms').then(function (resp) {
                data.boms = resp.data.data;

                console.log("resp.data:", resp.data);

                console.log("resp.data is a(n):", typeof resp.data);
                console.log("resp.data.data is a(n):", typeof resp.data.data);
                console.log('data: api/boms Success', data.boms);
                console.log("DataService.boms is a(n):", typeof data.boms);

                // Now do our fancy data merge thingy
                for (i in data.boms) {
                    for (p in data.products) {
                        data.products[p].bom = data.boms[i];
                    }
                }
                console.log('data: api/boms: join bom, products: ', data.products);

            }, function (err) {
                console.error('data: api/boms ERR', err);
                // err.status will contain the status code
            });

        }, function (err) {
            console.error('data: api/products ERR', err);
            // err.status will contain the status code
        });

        $http.get('api/types').then(function (resp) {
            data.types = resp.data.data;
            console.log('data: api/types Success', data.types);
        }, function (err) {
            console.error('data: api/types ERR', err);
            // err.status will contain the status code
        });

        $http.get('api/subtypes').then(function (resp) {
            data.subtypes = resp.data.data;
            console.log('data: api/subtypes Success', data.subtypes);
        }, function (err) {
            console.error('data: api/subtypes ERR', err);
            // err.status will contain the status code
        });
        */

        return data;
    });
