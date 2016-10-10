/**
 * Created by huhale on 15/2/2.
 */

var WxSuper = function(){
  var _this = this;
};
WxSuper.prototype = {
  init:function(options){
    this.options = $.extend({
      appId:'wx74a4b71d90e4110a'
    },options);
    var _this = this;
    this.getToken(function(data){
      _this.bindWx(data);
    });
    return _this;
  },
  /*
   * 获取token
   * @param success => 得到token后回调，回调一个参数
   *  @回调param data => token信息
   */
  getToken:function(success){
    var _this = this;
    $.ajax({
      url:'http://app.flyfinger.com/wxOauth/getSign.htm?appid='+_this.options.appId+'&url='+encodeURIComponent(location.href.split('#')[0]),
      dataType:'jsonp',
      cache:false,
      success:function(data) {
        success.call(_this, data);
      }
    });
  },
  /*
   * 配置微信事件列表
   */
  bindWx:function(data){
    var _this = this;
    wx.config({
      debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
      appId: _this.options.appId, // 必填，公众号的唯一标识
      timestamp: data.data.timestamp, // 必填，生成签名的时间戳
      nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
      signature: data.data.signature,// 必填，签名，见附录1
      jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
      ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
  },
  /*
   * 注册微信初始化事件
   * @param success => 传入的要执行的函数
   */
  setWxReady:function(success){
    wx.ready(function(){
      success();
    });
  }
};

window.wxSuper = new WxSuper();

