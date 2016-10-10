(function () {
    'use strict';

    angular
        .module('order.controller', [])
        .controller('OrderCtrl', OrderCtrl);

    OrderCtrl.$inject = ['$scope', '$rootScope'];

    /* @ngInject */
    function OrderCtrl($scope, $rootScope) {
        $scope.data = {};
        $rootScope.hideTabs = false;
        init();

        ////////////////

        function init() {
        }
    }
})();