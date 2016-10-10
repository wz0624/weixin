// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.services' is found in services.js
// 'starter.controllers' is found in controllers.js
angular.module('starter', ['ionic', 'yike.utils', 'tabs.module', 'controller'])

    .run(function ($ionicPlatform) {
        $ionicPlatform.ready(function () {
            // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
            // for form inputs)
            if (window.cordova && window.cordova.plugins && window.cordova.plugins.Keyboard) {
                cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
                cordova.plugins.Keyboard.disableScroll(true);

            }
            if (window.StatusBar) {
                // org.apache.cordova.statusbar required
                StatusBar.styleDefault();
            }
        });
    })

    .config(function ($stateProvider, $urlRouterProvider, $ionicConfigProvider) {
        //这里对android进行一些配置,为了保证ios和安卓平台显示效果一致
        $ionicConfigProvider.tabs.position('bottom');
        $ionicConfigProvider.tabs.style('standard');
        $ionicConfigProvider.navBar.alignTitle('center');
        $ionicConfigProvider.navBar.positionPrimaryButtons('left');
        $ionicConfigProvider.backButton.icon('ion-ios-arrow-left');
        $ionicConfigProvider.views.swipeBackEnabled(false);
        $ionicConfigProvider.views.maxCache(0);

        // Ionic uses AngularUI Router which uses the concept of states
        // Learn more here: https://github.com/angular-ui/ui-router
        // Set up the various states which the app can be in.
        // Each state's controller can be found in controllers.js
        $stateProvider

        // setup an abstract state for the tabs directive
            .state('tab', {
                url: '/tab',
                abstract: true,
                templateUrl: STATIC_PATH + 'templates/tabs.html'
            })

            // Each tab has its own nav history stack:

            .state('tab.dash', {
                url: '/dash',
                views: {
                    'tab-dash': {
                        templateUrl: STATIC_PATH + 'templates/tab-dash.html',
                        controller: 'DashCtrl'
                    }
                }
            })

            .state('tab.order', {
                url: '/order',
                views: {
                    'tab-order': {
                        templateUrl: STATIC_PATH + 'templates/tab-order.html',
                        controller: 'OrderCtrl'
                    }
                }
            })

            .state('tab.account', {
                url: '/account',
                views: {
                    'tab-account': {
                        templateUrl: STATIC_PATH + 'templates/tab-account.html',
                        controller: 'AccountCtrl'
                    }
                }
            })

            .state('level', {
                url: '/level/:id',
                templateUrl: STATIC_PATH + 'templates/level.html',
                controller: 'LevelCtrl'
            })

            .state('rebates', {
                url: '/rebates/:status',
                templateUrl: STATIC_PATH + 'templates/rebates.html',
                controller: 'RebatesCtrl'
            })

            .state('tab.qrcode', {
                url: '/qrcode',
                views: {
                    'tab-qrcode': {
                        templateUrl: STATIC_PATH + 'templates/tab-qrcode.html',
                        controller: 'QrcodeCtrl'
                    }
                }
            });

        // if none of the above states are matched, use this as the fallback
        $urlRouterProvider.otherwise('/tab/account');

    });

(function () {
  'use strict';

  angular
    .module('yike.utils', ['ionic'])
    .factory('$yikeUtils', $yikeUtils);

  $yikeUtils.$inject = ['$rootScope', '$state', '$ionicPopup', '$timeout', '$ionicLoading'];

  /* @ngInject */
  function $yikeUtils($rootScope, $state, $ionicPopup, $timeout, $ionicLoading) {
    return {
      go: go,
      alert: alert,
      confirm: confirm,
      show: show,
      toast: toast
    };

    ////////////////

    function go(target, params, options) {
      $state.go(target, params, options);
    }

    function toast(message, position, stick, time) {
      $ionicLoading.show({ template: message, noBackdrop: true, duration: 1000 });
    }

    function alert(title, template) {
      var _alert = $ionicPopup.alert({
        title: title,
        template: template,
        'okType': 'button-assertive'
      });

      $timeout(function() {
        _alert.close();
      }, 1500);

      return _alert;
    }

    function confirm(title, template) {
      var _alert = $ionicPopup.confirm({
        'title': title,
        'template': template,
        'okType': 'button-assertive',
        'cancelText': '取消',
        'okText': '确认'
      });

      $timeout(function() {
        _alert.close(); //close the popup after 3 seconds for some reason
      }, 3000);

      return _alert;
    }

    function show(title, template, scope, buttons) {
      var _alert = $ionicPopup.show({
        title: title,
        template: template,
        scope: scope,
        buttons: buttons
      });
      $timeout(function() {
        _alert.close(); //close the popup after 3 seconds for some reason
      }, 3000);

      return _alert;
    }
  }
})();

/**
 * 易客商城
 * @param uid
 * @param openid
 * @constructor
 */
function Yike(url, uid, openid, module) {
    this.url = url;
    this.uid = uid;
    this.openid = openid;
    this.module = module;
}

Yike.prototype = {
    constructor: Yike,
    /**
     * 基础查询函数
     * @param _do
     * @param action
     * @param op
     * @returns {AV.Promise}
     */
    query: function(_do, action, op, data) {
        var self = this;
        data.openid = self.openid;
        var m = self.module;

        var promise = new AV.Promise();
        var req = {
            'url': self.url + '?i=' + self.uid + '&c=entry' + '&do=' + _do + '&m=' + m + '&p=' + action + '&op=' + op,
            'data': data,
            'dataType': 'json'
        };

        $.ajax({
            'url': req.url,
            'data': req.data,
            'type' : 'post',
            'beforeSend': function(xhr) { xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')},
            'success':function(data){
                data = JSON.parse(data);
                promise.resolve(data);
            },
            'error' : function(i, data){
                promise.reject(data);
            }
        });

        return promise;
    },

    /**
     * 易客+ 查询函数
     * @param controller
     * @param action
     * @param op
     * @returns {AV.Promise}
     */
    plus: function(controller, action, op, data) {
        var self = this;
        data.openid = self.openid;

        var promise = new AV.Promise();
        var req = {
            'url': self.url + '?i=' + self.uid + '&c=entry' + '&do=' + controller + '&m=yike_plus' + '&p=' + action + '&op=' + op,
            'data': data,
            'dataType': 'json'
        };

        $.ajax({
            'url': req.url,
            'data': req.data,
            'type' : 'post',
            'beforeSend': function(xhr) { xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')},
            'success':function(data){
                data = JSON.parse(data);
                promise.resolve(data);
            },
            'error' : function(i, data){
                promise.reject(data);
            }
        });

        return promise;
    },

    /**
     * 基础插件查询函数
     * @param controller
     * @param action
     * @param op
     * @returns {AV.Promise}
     */
    plugin: function(controller, action, op, data) {
        var self = this;
        data.openid = self.openid;

        var promise = new AV.Promise();
        var req = {
            'url': self.url + '?i=' + self.uid + '&c=entry' + '&do=plugin&m=ewei_shop' + '&method=' + action + '&op=' + op + '&p=' + controller,
            'data': data,
            'dataType': 'json'
        };

        $.ajax({
            'url': req.url,
            'data': req.data,
            'type' : 'post',
            'beforeSend': function(xhr) { xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')},
            'success':function(data){
                data = JSON.parse(data);
                promise.resolve(data);
            },
            'error' : function(i, data){
                promise.reject(data);
            }
        });

        return promise;
    },

    getBanners: function() {
        return this.query('home', '', 'banners', {});
    }
};

var openid = '';
var yike = new Yike(WX_API_URL, WX_ID, openid, 'yike_red_packet');

(function () {
    'use strict';

    angular
        .module('controller', ['level', 'rebates']);
})();
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
            //getLevelList();
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
(function () {
    'use strict';

    angular
        .module('tabs.module', ['account.controller', 'dash.controller','order.controller','qrcode.controller']);
})();