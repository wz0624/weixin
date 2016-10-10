(function () {
    'use strict';

    angular
        .module('qrcode.controller', [])
        .controller('QrcodeCtrl', QrcodeCtrl);

    QrcodeCtrl.$inject = ['$scope', '$rootScope', '$yikeUtils', '$state'];

    /* @ngInject */
    function QrcodeCtrl($scope, $rootScope, $yikeUtils, $state) {
        $scope.data = {};
        $scope.preview = preview;
        $rootScope.hideTabs = false;
        init();

        ////////////////

        function init() {
            if (ME.inviter_level == '0' || !ME.inviter_level) {
                $yikeUtils.toast('请先购买会员权限');
                $state.go('tab.dash');
            } else {
                yike.query('home', '', 'get_qrcode', {})
                    .then(function(data) {
                        $scope.data.qrcode = data.result;
                        $scope.$digest();
                    });
            }
        }

        function preview(data) {
            wx.previewImage({
                current: data, // 当前显示图片的http链接
                urls: [data] // 需要预览的图片http链接列表
            });
        }
    }

})();