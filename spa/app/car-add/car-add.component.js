'use strict';

angular.module('carAdd').component('carAdd', {
    templateUrl: 'car-add/car-add.template.html',
    controller: ['$routeParams', 'CarService', '$scope', '$window', '$http',
        function CarAddController($routeParams, CarService, $scope, $window, $http) {
            $scope.brands = $http
                .get('/brands')
                .then(function (response) {
                    const brands = response.data.map(function (carBrandDto) {
                        return carBrandDto.name;
                    });

                    $scope.brands = brands;

                    return brends;
                })
            ;

            $scope.violations = {};
            $scope.car = {
                vin: '',
                registration: '',
                isRented: false
            };
            $scope.rental = {
                customerEmail: '',
                startDate: '',
                endDate: null,
                billingAddress: {
                    street: '',
                    postalCode: '',
                    city: '',
                }
            };

            $scope.addCar = function(car) {
                $scope.violations = {};
                $scope.car = car;

                CarService.save(car)
                    .$promise
                    .then(function (response) {
                        if ($scope.car.isRented) {
                            $scope.rental = $http
                                .post('/cars/' + response.id + '/rentals', { ...$scope.rental, ...{ car: 'http://localhost:8000/cars/' + response.id }}, { headers: { 'Content-type': 'application/json' }})
                                .then(function (response) {
                                    $window.location.href = '#!/cars';
                                })
                                .catch(function (response) {
                                    if (422 === response.status) {
                                        response.data.violations.forEach(function (violation) {
                                            if (! $scope.violations[violation.propertyPath])
                                                $scope.violations[violation.propertyPath] = [];

                                            $scope.violations[violation.propertyPath].push(violation.message)
                                        });
                                    }
                                })
                            ;
                        }

                        $window.location.href = '#!/cars';
                    })
                    .catch(function (response) {
                        if (422 === response.status) {
                            response.data.violations.forEach(function (violation) {
                                if (! $scope.violations[violation.propertyPath])
                                    $scope.violations[violation.propertyPath] = [];

                                $scope.violations[violation.propertyPath].push(violation.message)
                            });
                        }
                    })
                ;
            }
        }
    ]
});
