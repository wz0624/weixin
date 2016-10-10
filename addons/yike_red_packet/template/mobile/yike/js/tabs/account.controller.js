(function () {
    'use strict';

    angular
        .module('account.controller', [])
        .controller('AccountCtrl', AccountCtrl);

    AccountCtrl.$inject = ['$scope', '$rootScope', '$yikeUtils', '$ionicActionSheet', '$state'];

    /* @ngInject */
    function AccountCtrl($scope, $rootScope, $yikeUtils, $ionicActionSheet, $state) {
        $scope.data = {};
        $scope.user = ME;
        $scope.upgrade = upgrade;
        $scope.upgradeLevel = upgradeLevel;
        $scope.levelList = levelList;
        $scope.levelPrice = levelPrice;
        $rootScope.hideTabs = false;
        init();

        ////////////////

        function init() {
            if (ME.inviter_level == '0' || !ME.inviter_level) {
                $yikeUtils.toast('请先购买会员权限');
                $state.go('tab.dash');
            } else {
                getChildren();
                getLevelList();
            }
        }

        function getLevelList() {
            $scope.data.priceList = levelPrice;
            $scope.data.price = levelPrice[$scope.data.level];
        }

        function getChildren() {
            yike.query('home', '', 'children', {})
                .then(function(data) {
                    var result = data.result;
                    $scope.data.count1 = result.count1;
                    $scope.data.count2 = result.count2;
                    $scope.data.count3 = result.count3;
                    var originMe = ME;
                    ME = result.user;
                    ME.inviterLevelName = originMe.inviterLevelName;
                    $scope.user = ME;
                    $scope.$digest();
                })
        }

        function upgradeLevel(level) {
            var price;
            if (level == 2) {
                price = $scope.data.priceList['2'] - $scope.data.priceList[ME.inviter_level];
            } else if (level == 3) {
                price = $scope.data.priceList['3'] - $scope.data.priceList[ME.inviter_level];
            }
            price = price.toString();
            location.href = PAY_URL + '&money=' + price + '&level=' + level ;
        }

        function upgrade() {
            var buttons = [];
            if (ME.inviter_level < 3) {
                buttons.push({text: '全国代理'});
            }
            if (ME.inviter_level < 2) {
                buttons.push({text: '省级代理'});
            }
            var hideSheet = $ionicActionSheet.show({
                buttons: buttons,
                titleText: '请选择要升级的等级',
                cancelText: '取消',
                cancel: function() {
                    // add cancel code..
                },
                buttonClicked: function(index) {
                    if (!$scope.data.priceList) {
                        $yikeUtils.toast('系统错误,未查找到升级价格表');
                        return false;
                    }
                    var price = 0, level = 0;
                    if (index == 0) {
                        price = $scope.data.priceList['3'] - $scope.data.priceList[ME.inviter_level];
                        level = 3;
                    } else if (index  == 1) {
                        price = $scope.data.priceList['2'] - $scope.data.priceList[ME.inviter_level];
                        level = 2;
                    }
                    location.href = PAY_URL + '&money=' + price + '&level=' + level ;
                    return true;
                }
            });
        }
    }
})();