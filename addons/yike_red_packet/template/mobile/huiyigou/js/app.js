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
