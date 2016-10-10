window.onload = function () {
    var btnCloseAd = document.querySelector(".btn-close-ad");
    var domTable = document.querySelector(".container-list .table-body");
    var domPrizeTable = document.querySelector(".container-prize .table-body");
    var tableScroll = document.querySelector(".container-list .table-scroll");
    var tablePrizeScroll = document.querySelector(".container-prize .table-scroll");
    var containerTitle = document.querySelector(".container-title");
    var tableTitle = document.querySelector(".table-title");
    var panelLayer = document.querySelector(".register-panel-layer");
    var panelBox = document.querySelector("#panel-got-prize");
    var panelBox2 = document.querySelector("#panel-without-times");
    var domAd = document.querySelector(".ads");
    var containerBody = document.querySelector(".container-body");
    var containerContent = document.querySelectorAll(".container-content");
    var formSubmitReward = document.querySelector("#form-submit-reward");
    var btnCancel = document.querySelector(".btn-cancel");
    var btnStart = document.querySelector(".btn-start");
    var btnShare = document.querySelector(".btn-share");
    var btnKnow = document.querySelector(".btn-know");
    var shareMask = document.getElementById("masklayer");
    var btnPrize = document.querySelector(".table-bottom .btn-prize");
    var bodyHeight = document.documentElement.offsetHeight;
    var tableTitleHeight = tableTitle.offsetHeight;
    var companyRightsHeight = document.querySelector(".company-rights").offsetHeight;
    var tableBottomHeight = document.querySelector(".table-bottom").offsetHeight;
    var tablePSHeight = document.querySelector("#table-ps").offsetHeight;
    var topHeight = containerTitle.offsetTop;
    var getPrizeDomTmp;

    var PRIZETYPE = {
        GET: 0,
        DONE: 1
    };

    var bodyCanMove = true;

    if (btnCloseAd) {
        btnCloseAd.onclick = function () {
            domAd.classList.remove("show");
            calTableHeight();
        }
    }

    containerTitle.onclick = function (e) {
        if (e.target.nodeName === "A") {
            switch (e.target.className) {
                case "btn-activity":
                    showContainerContent(0);
                    break;
                case "btn-prize":
                    showContainerContent(1);
                    break;
            }
        }
    }
    if(btnPrize){
        btnPrize.onclick = function(e){
            if(checkTimes()){
                e.preventDefault();
                return false;
            }
        }
    }
    

    btnKnow.onclick = function(){
        panelBox2.classList.remove("show");
        panelLayer.classList.remove("show");
    }

    document.addEventListener('touchmove', function (e) {
        if (!bodyCanMove) {
            e.preventDefault();
        }
    }, false);

    panelLayer.ontouchmove = function (e) {
        e.preventDefault();
    }

    panelBox.ontouchmove = function (e) {
        e.preventDefault();
    }
    panelBox2.ontouchmove = function (e) {
        e.preventDefault();
    }

    shareMask.ontouchmove = function (e) {
        e.preventDefault();
    }

    btnShare.onclick=function(){
      var className = shareMask.className;
      if(className == "masklayer")
        shareMask.className = "masklayer on";
      else
        shareMask.className = "masklayer";
    }

    shareMask.onclick=function(){
      shareMask.className = "masklayer";
    }


    btnStart.onclick = function (e) {
        e.preventDefault();

        var password = formSubmitReward.inputpassword;
        if(password.value == "")
            return;

        panelBox.classList.remove("show");
        panelLayer.classList.remove("show");

        var params = {};
        for (var i = 0; formSubmitReward[i]; i++) {
            var obj = formSubmitReward[i];
            if(obj.type === "text" || obj.type === "password" || obj.type === "tel" || obj.type === "hidden"){
                params[obj.name] = obj.value;
            }
        }

        getPrizeAjax(formSubmitReward, params, function (result) {
            var data = JSON.parse(result);
            if(data.status === 0){
                password.value = "";
                getPrizeDomTmp.dataset.type = data.type;
                changePrizeDomStatus();
            }else{
                panelBox2.querySelector(".plus").innerHTML = "密码错误, 请重新填写!";
                panelBox2.classList.add("show");
                panelLayer.classList.add("show");
            }
        });
    }

    btnCancel.onclick = function () {
        panelBox.classList.remove("show");
        panelLayer.classList.remove("show");
    }

    var rewardList = new iScroll(tableScroll, {
        onScrollStart: function () {
            bodyCanMove = false;
        },
        onTouchEnd: function () {
            bodyCanMove = true;
        }
    });

    var prizeList = new iScroll(tablePrizeScroll, {
        onScrollStart: function () {
            bodyCanMove = false;
        },
        onTouchEnd: function () {
            bodyCanMove = true;
        }
    });

    createRewardData(window.rewardData);
    createPrizeData(window.prizeData);

    calTableHeight();


    function checkTimes(){
        if (window.reward_page_config.TOTALRESTTIMES <= 0 || window.reward_page_config.RESTTIMES <= 0) {
            if (window.reward_page_config.TOTALRESTTIMES <= 0) {
                panelBox2.querySelector(".plus").innerHTML = "你的游戏机会已经用完<br/>期待下次活动开启!";
            }else{
                panelBox2.querySelector(".plus").innerHTML = "你的游戏机会已经用完<br/>明天再来吧!";
            }
            panelBox2.classList.add("show");
            panelLayer.classList.add("show");
            return true;
        }
        return false;
    }


    function calTableHeight() {
        var num = bodyHeight - ( containerTitle.offsetHeight + tableTitleHeight + tableBottomHeight + tablePSHeight + domAd.offsetHeight + companyRightsHeight + topHeight);
        var num2 = num + tableTitleHeight;
        if (tableScroll.offsetHeight != num) {
            tableScroll.style.height = num + "px";
            tablePrizeScroll.style.height = num + "px";
            containerBody.style.height = num2 + "px";
            rewardList.refresh();
            prizeList.refresh();
        }
    }

    function showContainerContent(index) {
        for (var i = 0; containerContent[i]; i++) {
            var content = containerContent[i];
            content.style.display = (i === index) ? "block" : "none";
            (i === 0) ? rewardList.refresh() : prizeList.refresh();
        }
    }

    function createRewardData(data) {
        var fragment = document.createDocumentFragment();
        for (var i = 0; data[i]; i++) {
            var tr = document.createElement("tr");

            var td = document.createElement("td");
            tr.appendChild(td);

            var img = new Image();
            img.src = data[i]["img"];
            td.appendChild(img);

            var td = document.createElement("td");
            td.textContent = data[i]["type"];
            tr.appendChild(td);

            var td = document.createElement("td");
            td.textContent = data[i]["name"];
            tr.appendChild(td);

            var td = document.createElement("td");
            td.textContent = data[i]["num"];
            tr.appendChild(td);

            fragment.appendChild(tr);
        }
        domTable.appendChild(fragment);
        rewardList.refresh();
    }

    function createPrizeData(data) {
        var fragment = document.createDocumentFragment();
        if(data.length === 0){
            var tr = document.createElement("tr");

            var td = document.createElement("td");
            td.setAttribute("colspan",4);
            td.textContent = "您暂时还没有奖品，去参加游戏赢大奖吧～";
            tr.appendChild(td);
            
            fragment.appendChild(tr);
        }else{
            for (var i = 0; data[i]; i++) {
                var tr = document.createElement("tr");

                var td = document.createElement("td");
                tr.appendChild(td);

                var img = new Image();
                img.src = data[i]["img"];
                td.appendChild(img);

                var td = document.createElement("td");
                td.textContent = data[i]["name"];
                tr.appendChild(td);

                var td = document.createElement("td");
                td.textContent = data[i]["sn"];
                tr.appendChild(td);

                var td = document.createElement("td");
                tr.appendChild(td);

                var span = document.createElement("span");
                span.className = "btn-get-prize";
                span.dataset.type = data[i]["type"];
                span.dataset.id = data[i]["id"];
                changePrizeDomStatus(span);
                span.addEventListener("click", function (dom, _data) {
                    getPrizeDomTmp = dom;
                    getPrize();
                }.bind(this, span, data[i]), false);
                td.appendChild(span);

                fragment.appendChild(tr);
            }
        }
        
        domPrizeTable.appendChild(fragment);
        prizeList.refresh();
    }

    function getPrize() {
        var data = getPrizeDomTmp.dataset;
        if (data.type != 0)
            return;

        panelBox.classList.add("show");
        panelLayer.classList.add("show");
        panelBox.querySelector("[name=inputid]").value = data.id;
    }

    function getPrizeAjax(frm, dataPara, fn) {
        $.ajax({
            url: frm.action,
            type: frm.method,
            data: dataPara,
            success: fn
        });
    }

    function changePrizeDomStatus(dom) {
        dom = dom || getPrizeDomTmp;
        if (!dom)
            return;

        var data = dom.dataset;
        switch (parseInt(data["type"])) {
            case PRIZETYPE.GET:
                dom.textContent = "领奖";
                break;
            case PRIZETYPE.DONE:
                dom.textContent = "已领奖";
                dom.classList.add("done");
                break;
            default :
                dom.textContent = "待定";
                dom.classList.add("done");
        }
    }
}