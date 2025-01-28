'use strict';

angular.module('carRentalApp').config(['$routeProvider',
    function config($routeProvider) {
        $routeProvider.when('/cars', {
            template: '<car-list></car-list>'
        }).when('/cars/add', {
            template: '<car-add></car-add>'
        }).when('/cars/:id', {
            template: '<car-edit></car-edit>'
        }).otherwise('/cars');
    }
]);
