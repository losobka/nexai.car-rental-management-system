'use strict';

angular.module('core.car').factory('CarService', ['$resource',
    function ($resource) {
        return $resource('http://localhost:8000/cars/:id', {}, {
            query: {
                method: 'GET',
                params: {},
                isArray: true
            },
            update: {
                method: 'PATCH'
            }
        });
    }
]);
