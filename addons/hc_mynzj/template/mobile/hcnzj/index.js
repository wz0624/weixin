!
function e(t, s, a) {
	function n(r, o) {
		if (!s[r]) {
			if (!t[r]) {
				var c = "function" == typeof require && require;
				if (!o && c) return c(r, !0);
				if (i) return i(r, !0);
				var l = new Error("Cannot find module '" + r + "'");
				throw l.code = "MODULE_NOT_FOUND", l
			}
			var h = s[r] = {
				exports: {}
			};
			t[r][0].call(h.exports, function(e) {
				var s = t[r][1][e];
				return n(s ? s : e)
			}, h, h.exports, e, t, s, a)
		}
		return s[r].exports
	}
	for (var i = "function" == typeof require && require, r = 0; r < a.length; r++) n(a[r]);
	return n
}({
	1: [function(e, t, s) {
		var a = {
			shareTitle: "我的年终奖",
			shareMessage: "天啊~我的年终奖超过10万,~你来试试看",
			shareUrl: "http://www.baidu.com",
			isAutoAlert: 0,
			shareType: 0
		},
			n = function() {
				if (window.urlproxy) {
					var e = JSON.stringify(a);
					urlproxy.shareDetail(e)
				} else $.ajax({
					url: "",
					type: "post",
					dataType: "json",
					timeout: 5e3,
					data: a,
					error: function(e) {},
					success: function(e) {}
				})
			};
		n();
		var i = e("../../../../common/tips/js/tips.js"),
			r = {
				init: function() {
					var e = this,
						t = $("#page"),
						s = 117.935 * t.height() / 190;
					$(".second, .share").css("height", t.height() + "px");
					var a = t.height() / 837;
					$(".wrap").css({
						"margin-left": (t.width() - s) / 2 + "px",
						"-webkit-transform": "scale(" + a + ")"
					});
					var n = 10,
						r = setInterval(function() {
							n >= 70 ? clearInterval(r) : (n += 10, $(".after").css("width", n + "px"))
						}, 1e3),
						o = document.createElement("style"),
						c = ".willScale{-webkit-transform:scale(" + 1 / a + ");}";
					o.type = "text/css", o.styleSheet ? o.styleSheet.cssText = c : o.innerHTML = c, document.getElementsByTagName("head")[0].appendChild(o), this._type = e.getQueryString("TYPE"), this._tips = i($("#J_tips")), this._shareUrl = "", this._dicParam = {
						shareType: 0,
						isAutoAlert: 1,
						shareTitle: "Title",
						shareMessage: "",
						shareUrl: "http://www.baidu.com"
					}, this.bind(), this.rotateDeg = 0, this.selectType = "constellation", this.constellationTpl = "", this.workAgeTpl = '<div class="item willScale age_item"><p data-value="0">2年以下</p> </div> <div class="item willScale age_item"> <p data-value="0.05">2年 - 4年</p> </div> <div class="item willScale age_item"> <p data-value="0.1">4年 - 6年</p> </div> <div class="item willScale age_item"> <p data-value="0.15">6年以上</p> </div>';
					try {
						this.track = new TBJ_TRACK({
							tid: "500",
							type: "website"
						}), this.track.pageView({})
					} catch (l) {}
					this._shareToAll = {
						title: "天啊~我的年终奖超过10万,~你来试试看",
						type: "link",
						link: location.href,
						imgUrl: "./share.jpg",
						success: function() {},
						cancel: function() {}
					}, this._shareToOne = {
						title: "",
						desc: "天啊~我的年终奖超过10万,~你来试试看",
						type: "link",
						link: location.href,
						imgUrl: "./share.jpg",
						success: function() {},
						cancel: function() {}
					}, $.ajax({
						url: "metnn.com",
						type: "GET",
						dataType: "jsonp",
						success: function(t) {
							wx.config({
								debug: !1,
								appId: t.appId,
								timestamp: t.timestamp,
								nonceStr: t.nonceStr,
								signature: t.signature,
								jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo"]
							}), wx.ready(function() {
								var t = e._shareToAll,
									s = e._shareToOne;
								wx.onMenuShareTimeline(t), wx.onMenuShareAppMessage(s), wx.onMenuShareQQ(t), wx.onMenuShareWeibo(t)
							})
						},
						error: function(e, t) {}
					})
				},
				trackFn: function(e, t) {
					var s = this,
						a = t ? e : "DIV" == e.target.tagName ? e.target.innerHTML : e.target.value;
					try {
						s.track.eventView({
							cat: "view",
							act: "click",
							lab: a
						})
					} catch (n) {}
				},
				getQueryString: function(e) {
					var t = new RegExp("(^|&)" + e + "=([^&]*)(&|$)"),
						s = window.location.search.substr(1).match(t);
					return null != s ? unescape(s[2]) : null
				},
				showSelect: function(e) {
					this.selectType != e && (this.selectType = e, "constellation" == e ? (this.workAgeTpl = $(".text_wrap").html(), $(".text_wrap").html(this.constellationTpl)) : (this.constellationTpl = $(".text_wrap").html(), $(".text_wrap").html(this.workAgeTpl)))
				},
				hideSelect: function(e, t, s) {
					$(".select").css("display", "none"), "constellation" == e && ($(".constellation").html(t), this.rotateDeg += 20, $(".knob").css("-webkit-transform", "rotate(" + this.rotateDeg + "deg)")), "work_age" == e && ($(".work_age").html(t), this._age = s, $("#light").addClass("green_light")), "选择您的星座" != $(".constellation").html() && "输入您的工龄" != $(".work_age").html() && $(".start_btn p").css("opacity", "1")
				},
				start: function() {
					if ("选择您的星座" == $(".constellation").html() || "输入您的工龄" == $(".work_age").html()) this._tips.errorMsg("资料未填 无法探测");
					else {
						this.trackFn("开始探测", !0);
						var e = Math.random();
						e + Number(this._age) >= 1 ? this._bonus = 60 : (this._bonus = 50 * (e + Number(this._age)) + 10, this._bonus = this._bonus.toFixed(0)), this._shareMsg = "简直啦~我的年终奖有：" + this._bonus + "万。说我骗人~算算你的咧！", this._shareToAll.title = this._shareMsg, this._shareToOne.desc = this._shareMsg, a.shareMessage = this._shareMsg, n(), $(".money span").html(this._bonus), $("#page").addClass("page"), $(".second").addClass("second_show"), setTimeout(function() {
							$("#page").removeClass("page")
						}, 6e3)
					}
				},
				share: function() {
					$(".name_wrap input").val() && (this._shareMsg = $(".name_wrap input").val() + "万岁，我的年终奖算出来啦：" + this._bonus + "万，求打赏！"), "APP" != this._type ? (this._shareToAll.title = this._shareMsg, this._shareToOne.desc = this._shareMsg) : (a.shareMessage = this._shareMsg, n()), this._tips.errorMsg("请点击右上角分享")
				},
				bind: function() {
					var e = this;
					$("#page").on("click", function(t) {
						var s = $(t.target);
						s.attr("data-track") && e.trackFn(s.attr("data-track"), !0);
						var a = s.attr("data-name"),
							n = s.closest("div");
						if (n.hasClass("item")) {
							var i = n.children("p").html();
							$(".text_wrap .item").removeClass("item_selected"), e.trackFn(i, !0), n.addClass("item_selected"), e.hideSelect(e.selectType, i, n.children("p").attr("data-value"))
						}
						switch (t.stopPropagation(), a) {
						case "constellation":
							e.showSelect(a), $(".select").css("display", "block");
							break;
						case "start":
							e.start();
							break;
						case "work_age":
							e.showSelect(a), $(".select").css("display", "block");
							break;
						case "close":
							e.hideSelect();
							break;
						case "btn1":
							$(".share").css("display", "block");
							break;
						case "share":
							$(".share .btn").css("display", "none"), $(".name_wrap").css("display", "block"), navigator.userAgent.match(/Android/g) && ($(".name_wrap input").on("focus", function() {
								$(".share").css("-webkit-transform", "translateY(-100px)")
							}), $(".name_wrap input").on("blur", function() {
								$(".share").css("-webkit-transform", "translateY(0px)")
							}));
							break;
						case "cancel":
							$(".share .btn").css("display", "block"), $(".name_wrap").css("display", "none");
							break;
						case "ok":
							e.share();
							break;
						case "hide_share":
							$(".share").css("display", "none");
							break;
						case "btn2":
							"APP" == e._type ? ($(".text_img").attr("src", "./tcode.png"), $(".money").css("display", "none")) : window.location.href = "http://m.tzg.cn/r?key=021347"
						}
					})
				}
			};
		r.init(), window.onload = function() {
			$(".after").css("width", "80px"), setTimeout(function() {
				$(".loading_wrap").css("display", "none")
			}, 100)
		}
	}, {
		"../../../../common/tips/js/tips.js": 2
	}],
	2: [function(e, t, s) {
		var a = function(e) {
				this.dom = e, this.dom.addClass("componentErrBlock")
			};
		a.prototype = {
			errorMsg: function(e, t) {
				var s = this,
					t = t || 2e3;
				s.dom.html(e), s.dom.css("display", "block"), setTimeout(function() {
					s.dom.css("display", "none")
				}, t)
			}
		}, t.exports = function(e) {
			return new a(e)
		}
	}, {}]
}, {}, [1]);