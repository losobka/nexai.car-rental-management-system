'use strict';

angular
    .module('carList')
    .component('carList', {
        templateUrl: 'car-list/car-list.template.html',
        controller: ['$scope', '$window', 'CarService',
            function CarListController($scope, $window, CarService) {
                $scope.loading = true;
                $scope.loadingError = false;

                $scope.cars = CarService
                    .query()
                    .$promise
                    .then(function (result) {
                        $scope.loadingError = false;
                        $scope.loading = false;
                        $scope.cars = result;
                    })
                    .catch(function (error) {
                        $scope.loadingError = true;
                        $scope.loading = false;
                    });


                $scope.edit = function (id) {
                    $window.location.href = '#!/cars/' + id;
                };

                $scope.remove = function (id) {
                    id = id;

                    var shouldRemove = $window.confirm('Are you sure you want to delete this car?');

                    if (true === shouldRemove) {
                        CarService.delete({id: id}).$promise.then(function (result) {
                            $window.location.reload();
                        })
                    }
                };

                $scope.reload = function () {
                    $window.location.reload();
                }
            }
        ]
    })
    .directive('address', function () {
        return {
            restrict: 'E',
            scope: {
                street: '@',
                postalCode: '@',
                city: '@'
            },
            template: `{{street}}, {{postalCode}} {{city}}`
        };
    })
;
