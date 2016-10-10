(function () {
    'use strict';

    angular
        .module('rebates', [])
        .controller('RebatesCtrl', RebatesCtrl);

    RebatesCtrl.$inject = ['$scope', '$state', '$yikeUtils'];

    /* @ngInject */
    function RebatesCtrl($scope, $state, $yikeUtils) {
        $scope.status = $state.params.status;
        $scope.data = {
            flag: false
        };
        $scope.user = ME;
        $scope.rebates = rebates;
        init();

        ////////////////

        function init() {
            getRebates();
        }

        function getRebates() {
            yike.query('home', '', 'rebates', {status: $scope.status})
                .then(function(data) {
                    if (data.result) {
                        $scope.data.list = data.result.users;
                        $scope.data.flag = false;
                    } else {
                        $scope.data.flag = true;
                        $yikeUtils.toast('暂无数据');
                    }
                    $scope.$digest();
                })
        }

        function rebates(id) {
            yike.query('home', '', 'send', {id: id})
                .then(function(data) {
                    if (data.status == '1') {
                        $yikeUtils.toast(data.result);
                        $state.reload();
                    }
                    $scope.$digest();
                })
        }
    }
})();