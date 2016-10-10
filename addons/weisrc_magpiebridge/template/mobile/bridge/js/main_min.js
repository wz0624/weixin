(function () {
    for (var B = 0, q = ["webkit", "moz"], f = 0; f < q.length && !window.requestAnimationFrame; ++f)window.requestAnimationFrame = window[q[f] + "RequestAnimationFrame"], window.cancelAnimationFrame = window[q[f] + "CancelAnimationFrame"] || window[q[f] + "CancelRequestAnimationFrame"];
    window.requestAnimationFrame || (window.requestAnimationFrame = function (f, q) {
        var y = (new Date).getTime(), z = Math.max(0, 16 - (y - B)), Q = window.setTimeout(function () {
            f(y + z)
        }, z);
        B = y + z;
        return Q
    });
    window.cancelAnimationFrame || (window.cancelAnimationFrame =
        function (f) {
            clearTimeout(f)
        })
})();
var soundManager;
window.onload = function () {
    function B() {
        R--;
        S = [];
        g = T = 0;
        C = D = !1;
        k = 0;
        U = !0;
        V = I = W = !1;
        q();
        e.classList.remove("show");
        e.topData = e.topDataInit;
        e.style[s] = "translate3d(0," + e.topData + "px,0)";
        ga = Math.floor(Math.random() * t.length);
        J.innerHTML = E(k);
        m = ha.offsetWidth / 4;
        bodyHeight = document.documentElement.offsetHeight;
        h = Math.round(188 * m / 160);
        ia = window.innerHeight % h;
        X.style.height = h + "px";
        X.style.display = "block";
        q();
        Y(n[0]);
        Y(n[1], 1)
    }

    function q() {
        var a = l && l.classList.contains("show") ? l.scrollHeight : 0;
        K[0] = window.innerHeight - 1 * h - a;
        K[1] = window.innerHeight - 2.5 * h - a
    }

    function f() {
        D = !0;
        cancelAnimationFrame(ja);
        ra(window.config_custom.URL.SUBMIT, function (data) {
            if (data.success == 0) {
                alert(data.msg);
                return false;
            }
            u.className = "";
            window.config_custom.RESULT.SCORE = g;
            window.config_custom.RESULT.TIME = E(k, !0);
            I && 0 < g && (Z.classList.add("show"), F.classList.add("show"), soundManager.play("win"));
            L.style.webkitTransform = "none";
            p.classList.add("show");
            p.classList.add("anim");
            u.style.display = "none";
            document.getElementById("game-layer-score-text").className = 0 < g ? "success" : "fail";
            document.getElementById("game-layer-score-score").innerHTML = 0 < g ? E(k) : "\u5931\u8d25\u4e86!";
            document.getElementById("game-layer-score-rank").innerHTML = (data && data.rank) + "&nbsp;&nbsp;";
            //\u5269\u4f59\u6b21\u6570
            window.config_custom.NUMLIMIT ? document.getElementById("game-layer-score-times").innerHTML = "剩余次数:<b>" + R + "</b>" : document.getElementById("game-layer-score-times").innerHTML = "";

            score = data.score;

            data = data.score + "0" - 0;
            //\u65b0\u7eaa\u5f55

            sa.innerHTML = (!data || data > k) && 0 < g ? '<span class="icon-star"></span>新纪录' : '<span class="icon-star"></span>最佳 ' + score;
            //E(data);
            r.style.display = "block";
            ka()
        })
    }

    function qa(a) {
        var b = "pasnbxzqjm".split(""), c = "";
        a = (a + "").split("");
        a.forEach(function (a) {
            c += b[a - 0]
        });
        return c
    }

    function P() {
        if (!D) {
            var a = Date.now();
            k += a - ba;
            ba = a;
            k >= ta ? (J.innerHTML = "时间到！", g = 0, f(), soundManager.play("end")) : (J.innerHTML = E(k), ja = requestAnimationFrame(P))
        }
    }

    function y(url, b) {
        //alert('1');
        var c = {};
        la ? (c = {
            prize: .5 < Math.random() ? !0 : !1,
            status: 1
        }, ca = c.prize, "function" === typeof b && b(c)) : $.ajax({
            url: url, type: "post", data: c, success: function (a) {
                a = JSON.parse(a);
                ca = a.prize;
                "function" === typeof b && b(a)
            }, error: function () {
                M.hide();
                //报错处理
                z();
            }
        })
    }

    //报错处理
    function z() {
        da = !0;
        //\u7f51\u7edc\u7e41\u5fd9,\u8bf7\u91cd\u65b0\u5c1d\u8bd5!
        v.querySelector(".plus").innerHTML = "网络繁忙,请重新尝试!";
        v.classList.add("show");
        F.classList.add("show")
    }

    function Q(a) {
        var b = Date.now();
        return md5(b) + qa(a) + md5(b + Math.random())
    }

    function ra(url, b) {
        //var c = ("0" + k).substr(-5, 4), c = {score: Q(0 < g ? c : 0)};
        var c = ("0" + k).substr(-5, 4);
            //alert(k);
            c = {score: k,num: g};
            //c = {score: Q(0 < g ? c : 0)};
        la ? (c = {status: 1, rank: 1, prize: !0}, "function" === typeof b && b(c)) : (M.show(), $.ajax({
            url: url, type: "post", data: c, success: function (data) {
                M.hide();
                "function" === typeof b && b(JSON.parse(data))
            }, error: function () {
                M.hide();
                //报错处理
                z();
            }
        }))
    }

    function E(a, b) {
        var c = (1E6 + a + "").substr(-5, 4);
        return c = c.substr(0, 2) + "." + c.substr(2) + (b ? "" : '"')
    }

    function Y(a, b, c) {
        for (var aa = Math.floor(1E3 * Math.random()) % 4 + (b ? 0 : 4), d = 0; d < a.children.length; d++) {
            var e = a.children[d], f = e.style, g = Math.floor(d / 4);
            f.left = d % 4 * m + "px";
            f.bottom = g * h + "px";
            f.width = m + "px";
            f.height = h + "px";
            e.className = "";
            aa == d ? (f = 0 === t.length ? g : 0 === g % t.length ? t.length : g % t.length, 1 !== f || C || V ? !ca || I || W || g !== ga ? e.className = "tile t" + f : (W = !0, e.className = "tile prize t" + f) : (V = !0, e.className = "tile t1 start"),
                S.push({
                    cell: aa % 4,
                    id: e.id
                }), e.notEmpty = !0, aa = 4 * (g + 1) + Math.floor(1E3 * Math.random()) % 4) : e.notEmpty = !1
        }
        b ? (a.style.webkitTransitionDuration = "0ms", a.style.display = "none", a.y = -h * (Math.floor(a.children.length / 4) + (c || 0)) * b, setTimeout(function () {
            a.style[s] = "translate3D(0," + a.y + "px,0)";
            setTimeout(function () {
                a.style.display = "block"
            }, 100)
        }, 200)) : (a.y = 0, a.style[s] = "translate3D(0," + a.y + "px,0)");
        a.style[ea] = "150ms"
    }

    function ma(a) {
        if (D)return !1;
        if (g >= window.config_custom.NUM)f(); else {
            var b = a.target, c = a.clientY ||
                a.targetTouches[0].clientY;
            a = (a.clientX || a.targetTouches[0].clientX) - ha.offsetLeft;
            var d = S[T];
            if (c > K[0] || c < K[1])return !1;
            c = document.querySelector("#" + d.id);
            if (d.id == b.id && c.notEmpty || 0 == d.cell && a < m || 1 == d.cell && a > m && a < 2 * m || 2 == d.cell && a > 2 * m && a < 3 * m || 3 == d.cell && a > 3 * m)if (c = document.querySelector("#" + d.id), c.classList.add("clicked"), C || (X.style.display = "none", na = ba = Date.now(), C = !0, P()), Date.now() - na - 1E3 > k && P(), soundManager.play("tap"), c.classList.contains("prize") && ua(b), T++, g++, g >= window.config_custom.NUM)f();
            else for (g >= window.config_custom.NUM - Math.floor(window.innerHeight / h) && (e.topData += h, e.classList.add("show"), e.style[s] = "translate3D(0," + e.topData + "px,0)"), b = 0; b < n.length; b++)c = n[b], c.y += h, c.y > h * Math.floor(c.children.length / 4) ? Y(c, 1, -1) : c.style[s] = "translate3D(0," + c.y + "px,0)"; else C && !b.notEmpty && (soundManager.play("err"), g = 0, f(), b.className += " bad");
            return !1
        }
    }

    function ua(a) {
        I = !0;
        p.classList.add("show");
        G.style.webkitTransform = "translate3d(" + (a.offsetLeft + a.offsetWidth / 2) + "px," + (window.innerHeight -
            a.offsetHeight) + "px,0)";
        G.classList.add("anim");
        G.addEventListener("webkitTransitionEnd", function () {
            G.classList.remove("anim");
            p.classList.add("anim");
            setTimeout(function () {
                D || (p.classList.remove("show"), p.classList.remove("anim"))
            }, 2E3)
        }, !1);
        setTimeout(function () {
            G.style.webkitTransform = "translate3d(" + p.offsetLeft + "px," + p.offsetTop + "px,0) scale(0.5)"
        }, 20)
    }

    function va() {
        for (var a = 1; 2 >= a; a++)for (var b = document.querySelector("#game-layer" + a), c = 0; 10 > c; c++)for (var d = 0; 4 > d; d++) {
            var e = document.createElement("div");
            e.id = "game-layer" + a + "-" + (d + 4 * c);
            e.setAttribute("num", d + 4 * c);
            b.appendChild(e)
        }
    }

    function ka() {
        var a = 440;
        if (l && l.classList.contains("show")) {
            var b = window.innerHeight - l.offsetHeight;
            440 < b ? a = b : window.innerHeight < a && (a = window.innerHeight);
            r.classList.add("modify");
            r.style.height = a + "px";
            l.style.top = a + "px"
        } else r.style.height = window.innerHeight + "px"
    }

    var ha = document.querySelector("#gameBody") || document.body, n = [], u = document.querySelector("#game-layer-bg"), K = [], J, m = 0, h = 0, s, ea, S = [], T = 0, D = !1, C = !1, U = !0, da = !1, ja,
        k, g, A = "touchstart"in document.documentElement ? "touchstart" : "click";
    document.querySelector("#btn-back");
    var d = document.querySelector("#btn-replay"),
        w = document.querySelectorAll(".btn-close-ad"),
        p = document.querySelector("#btn-prize"), wa = document.querySelector("#btn-share"), oa = document.querySelectorAll(".btn-know"), H = document.getElementById("masklayer"), fa = document.querySelectorAll(".ads"), X = document.querySelector("#game-start-tap"), e = document.querySelector("#game-end-tap"), G = document.querySelector("#game-icon-prize"),
        N = document.querySelectorAll(".company-rights"), r = document.querySelector("#game-layer-score"), sa = document.getElementById("game-layer-score-best");
    document.getElementById("game-layer-score-btn");
    var F = document.querySelector(".register-panel-layer"), Z = document.querySelector("#panel-got-prize"), v = document.querySelector("#panel-without-times"), x = document.querySelector("#guide-panel"), O = document.querySelector("#loading-panel"), L = document.querySelector("#main-box"), l = document.querySelector("#game-panel-ad"),
        xa = O.querySelector(".loading-panel-progress"), ya = O.querySelector(".loading-panel-progress-current"), t = window.config_custom.IMG.CLICK, I = !1, ca = !1, W = !1, V = !1, ga = 0, ia = 0, la = window.config_custom.DEBUG || !1, R = window.config_custom.RESTTIMES, ta = 1E3 * window.config_custom.TIMETOTAL, pa = new LocalStorageManager, M = new loadingBox(document.body), za = pa.getGuide(), Aa = function () {
            var a = navigator.userAgent;
            return {
                mobile: !!a.match(/AppleWebKit.*Mobile.*/),
                ios: !!a.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
                android: -1 < a.indexOf("Android") ||
                -1 < a.indexOf("Linux")
            }
        }();
    soundManager = createjs.Sound;
    Aa.android && (soundManager = SoundManager);
    window.config_custom.NEEDGUIDE && ("false" == za ? x.classList.remove("show") : (x.classList.add("show"), pa.setGuide(!1)));
    d.addEventListener(A, function () {
        U ? y(window.config_custom.URL.RESTART, function (a) {
            0 >= R || 0 >= window.config_custom.TOTALRESTTIMES ? (0 >= window.config_custom.TOTALRESTTIMES ? v.querySelector(".plus").innerHTML = "你的游戏机会已经用完<br/>期待下次活动开启!" : v.querySelector(".plus").innerHTML = "你的游戏机会已经用完<br/>明天再来吧", v.classList.add("show"), F.classList.add("show"), a = !0) : a = !1;
            //\u4f60\u7684\u6e38\u620f\u673a\u4f1a\u5df2\u7ecf\u7528\u5b8c<br/>\u671f\u5f85\u4e0b\u6b21\u6d3b\u52a8\u5f00\u542f!
            //\u4f60\u7684\u6e38\u620f\u673a\u4f1a\u5df2\u7ecf\u7528\u5b8c<br/>\u660e\u5929\u518d\u6765\u5427!
            a || (B(), l && (l.style.top = "auto"), p.classList.remove("show"), p.classList.remove("anim"), r.style.display = "none", r.classList.remove("success"), r.classList.remove("fail"), u.style.display = "block", 0 < fa.length && (L.style.webkitTransform = "translate3d(0," + -l.offsetHeight + "px,0)"))
        }) : U = !1
    }, !1);
    if (0 < w.length) {
        for (d = 0; w[d]; d++)w[d].addEventListener(A,
            function () {
                for (var a = 0; fa[a]; a++)fa[a].classList.remove("show");
                for (a = 0; N[a]; a++)N[a].classList.add("low"), ka();
                L.style.webkitTransform = "none";
                r.style.height = window.innerHeight + "px";
                r.classList.remove("modify");
                q()
            }, !1);
        L.style.webkitTransform = "translate3d(0," + -l.offsetHeight + "px,0)"
    } else for (d = 0; N[d]; d++)N[d].classList.add("low");
    [H, u, e, Z, v, F, x, O].forEach(function (a) {
        a.ontouchmove = function (a) {
            a.preventDefault()
        }
    });
    x.onclick = function () {
        x.classList.remove("show")
    };
    x.onclick = function () {
        x.classList.remove("show")
    };
    wa.onclick = function () {
        H.className = "masklayer" == H.className ? "masklayer on" : "masklayer"
    };
    H.onclick = function () {
        H.className = "masklayer"
    };
    for (d = 0; oa[d]; d++)oa[d].onclick = function () {
        da ? (da = !1, f()) : (Z.classList.remove("show"), v.classList.remove("show"), F.classList.remove("show"))
    };
    A = [];
    for (d in window.config_custom.IMG)if (window.config_custom.IMG[d])if ("string" === typeof window.config_custom.IMG[d])A.push(window.config_custom.IMG[d]); else for (w = 0; window.config_custom.IMG[d][w]; w++)A.push(window.config_custom.IMG[d][w]);
    (function (a, b, c) {
        var d = a.length, e = 0, f = "function" === typeof b ? b : null, g = "function" === typeof c ? c : null;
        for (b = 0; a[b]; b++)c = new Image, c.src = a[b], c.onload = function () {
            e++;
            f && f(Math.round(100 * e / d));
            e === d && setTimeout(function () {
                g && g()
            }, 1500)
        }
    })(A, function (a) {
        xa.style.width = a + "%";
        ya.textContent = a + "%"
    }, function () {
        O.classList.remove("show");
        for (var a = new CSSCreate, b = 0; t[b]; b++)a.add(".t" + (b + 1), "background-image:url(" + t[b] + ");");
        a.add("#game-layer-bg", "background-image: url(" + window.config_custom.IMG.BG + ");");
        a.add("#game-layer-score",
            "background-image: url(" + window.config_custom.IMG.RESULTBG + ");");
        window.config_custom.IMG.GUIDEBG && a.add("#guide-panel", "background-image: url(" + window.config_custom.IMG.GUIDEBG + ");");
        a.produce();
        va();
        y(window.config_custom.URL.RESTART, function (a) {
            s = "webkitTransform";
            ea = "webkitTransitionDuration";
            J = document.querySelector("#game-layer-time");
            n.push(document.querySelector("#game-layer1"));
            n[0].children = n[0].querySelectorAll("div");
            n.push(document.querySelector("#game-layer2"));
            n[1].children = n[1].querySelectorAll("div");
            null === u.ontouchstart ? u.ontouchstart = ma : u.onmousedown = ma;
            soundManager.registerSound({src: window.config_custom.PATH.MUSIC + "err.mp3", id: "err"});
            soundManager.registerSound({src: window.config_custom.PATH.MUSIC + "end.mp3", id: "end"});
            soundManager.registerSound({src: window.config_custom.PATH.MUSIC + "tap.mp3", id: "tap"});
            soundManager.registerSound({src: window.config_custom.PATH.MUSIC + "win.mp3", id: "win"});
            B();
            a = Math.floor(window.innerHeight / h);
            for (var b = Math.ceil(window.innerHeight / h), d = 0; d < a; d++)for (var f = 0; 4 >
            f; f++) {
                var g = document.createElement("span");
                g.style.height = h + "px";
                e.appendChild(g)
            }
            e.topData = -b * h;
            e.topDataInit = -b * h;
            e.style.top = -(h - ia) + "px";
            e.style[s] = "translate3D(0," + e.topData + "px,0)";
            e.style[ea] = "150ms"
        })
    });
    var ba = 0, na = 0
};
window.onunload = function () {
    soundManager && soundManager.removeAllSounds()
};