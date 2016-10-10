(function () {
    'use strict';

    angular
        .module('dash.controller', [])
        .controller('DashCtrl', DashCtrl);

    DashCtrl.$inject = ['$scope', '$state', '$ionicSlideBoxDelegate', '$rootScope', '$yikeUtils'];

    /* @ngInject */
    function DashCtrl($scope, $state, $ionicSlideBoxDelegate, $rootScope, $yikeUtils) {
        $scope.data = {
            'level': '1',
            'realname': '',
            'wx': '',
            'mobiel': '',
            'address': '',
            'mid': mid,
            'price': 100,
            'priceList': {
                '1': 1,
                '2': 4,
                '3': 6
            }
        };
        $scope.buy = buy;
        $scope.selectLevel = selectLevel;
        $scope.levelList = levelList;
        $scope.data.priceList = levelPrice;
        $scope.data.price = levelPrice['1'];

        $rootScope.hideTabs = true;

        $scope.$on('$ionicView.beforeEnter', function() {
            if (ME.inviter_level != '0' && ME.inviter_level) {
                location.href = WX_API_URL + '?i='+WX_ID+'&c=entry&do=shop&m=ewei_shop';
            }
        });

        init();

        ////////////////

        function init() {
            if (ME.inviter_level != '0' && ME.inviter_level) {
                //$yikeUtils.toast('您已经购买过会员权限了');
                $state.go('tab.account');
            }
            getBanners();
            getLevelList();
        }

        function getLevelList() {
            yike.query('home', '', 'level_list', {})
                .then(function(data) {
                    $scope.data.priceList = data.result;
                    $scope.data.price = data.result[$scope.data.level];
                    $scope.$digest();
                })
        }

        function getBanners() {
            yike.getBanners()
                .then(function(data) {
                    $scope.banners = [data.result.banners || '/addons/yike_red_packet/template/mobile/huiyigou/img/default.jpg'];
                    $ionicSlideBoxDelegate.update();
                    $scope.$digest();
                })
        }

        function buy() {
            if (!$scope.data.realname) {
                $yikeUtils.toast('请填写姓名');
                return false;
            }
            if (!$scope.data.mobile) {
                $yikeUtils.toast('请填写手机号');
                return false;
            }
            location.href = PAY_URL + '&mid=' + $scope.data.mid + '&money=' + $scope.data.price + '&level=' + $scope.data.level + '&realname=' + $scope.data.realname + '&wx=' + $scope.data.wx + '&mobile=' + $scope.data.mobile + '&address=' + $scope.data.address;
        }

        function selectLevel(level) {
            if (isUpgrage == 1) {
                if (level != 1) {
                    $yikeUtils.toast('权限不足');
                }
            } else {
                $scope.data.level = level;
                $scope.data.price = $scope.data.priceList[level];
            }
        }
    }
})();