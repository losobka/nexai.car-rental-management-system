'use strict';

angular.module('carEdit').component('carEdit', {
    templateUrl: 'car-edit/car-edit.template.html',
    controller: ['$routeParams', 'CarService', '$scope', '$window', '$http',
        function CarEditController($routeParams, CarService, $scope, $window, $http) {
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

            $scope.car = CarService.get({id: $routeParams.id}, function (car) {
            });

            $scope.errorMessages = [];

            $scope.editCar = function(id) {
                CarService.update({id: id}, $scope.car)
                    .$promise
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
            };
        }
    ]
});
