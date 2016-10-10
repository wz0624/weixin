(function () {
    'use strict';

    angular
        .module('level', [])
        .controller('LevelCtrl', LevelCtrl);

    LevelCtrl.$inject = ['$scope', '$state', '$yikeUtils'];

    /* @ngInject */
    function LevelCtrl($scope, $state, $yikeUtils) {
        $scope.id = $state.params.id;
        $scope.data = {
            flag: false
        };
        $scope.user = ME;
        init();

        ////////////////

        function init() {
            getChildren();
        }

        function getChildren() {
            yike.query('home', '', 'child', {id: $scope.id})
                .then(function(data) {
                    if (data.result) {
                        $scope.data.list = data.result;
                        $scope.data.flag = false;
                    } else {
                        $scope.data.flag = true;
                        $yikeUtils.toast('您暂时还没有该级别下级');
                    }
                    $scope.$digest();
                })
        }
    }
})();