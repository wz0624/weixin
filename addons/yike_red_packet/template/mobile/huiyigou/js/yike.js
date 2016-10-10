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
