
var Game;
null == Game && (Game = {});
(function () {
	Array.prototype.indexOf = function (a) {
		for (var b = 0; b < this.length; b++) {
			if (this[b] == a) {
				return b;
			}
		}
		return -1;
	};
	Array.prototype.remove = function (a) {
		a = this.indexOf(a);
		-1 < a && this.splice(a, 1);
	};
	Array.prototype.shuffle = function () {
		for (var a = this.length, b = 0; b < a; b++) {
			var c = Math.randomInt(a - b);
			this.push(this[c]);
			this.splice(c, 1);
		}
	};
	Array.prototype.clear = function () {
		this.length = 0;
	};
	Math.randomInt = function (a) {
		return parseInt(Math.random() * a);
	};
})();
(function () {
	
	//是否已经完成游戏主要素材加载
	Game.hasLoadResource = false;
	//明牌中
	Game.wait = false;
	//用户是否点击了开始游戏按钮
	Game.hasClickStart = false;
	Game.waitTimer = null;
	Game.getResult = function (name) {
		return Game.queueGame.getResult(name);
	};
	Game.getHomeResult = function (name) {
		return Game.queueHome.getResult(name);
	};
	Game.holePos = function (i, j) {
		if(Game.type == 'easy'){
			i = i * W / 3 + W / 8 + 2 * i +15;
			j = 100 + j*180;
		}else if(Game.type == 'normal'){
			i = i * W / 4 + W / 8 + 2 * i - 10;
			j = 100 + j*180;				
		}else{
			i = i * W / 4 + W / 8 + 2 * i - 10;
			j = 100 + j*155;			
		}
		return [i, j];
	};
	Game.closeAll = function (a) {
		if (2 <= Game.arrOpenedCard.length) {
			for (; 0 < Game.arrOpenedCard.length; ) {
				Game.arrOpenedCard.pop().close();
			}
		}
		Game.arrOpenedCard.push(a);
	};
	Game.play = function (card) {		
		Game.arrOpenedCard.length == 1 && card.cid == Game.arrOpenedCard[0].cid ? (card.overturn(function () {
			//Game.score += 100;
			createjs.Sound.play(Game.bonus, !0);
			Game.gv.getNumChildren() <=0 && (Game.replay(), Game.score += gametype(),newLevel());
		}), Game.arrOpenedCard.pop().overturn()) : Game.closeAll(card);
	};
	function newLevel(){
		var txtLevel = new createjs.Text('+'+gametype(),'bold 50px Arial','#FFEB91');
		txtLevel.x = 90;
		txtLevel.y = 100;
		stage.addChild(txtLevel);
		createjs.Tween.get(txtLevel).to({y:30,alpha:0},1000).call(function(){
			stage.removeChild(txtLevel);
		})
	}
	Game.replay = function () {
		Game.arrOpenedCard = [];
		Game.gv.clearCard();
		Game.gv.mouseChildren = !0;
		//明牌倒数
		var waitTxt = new createjs.Text("明牌时间:5", "bold 44px Arial", "#ffeecd");
		var waitNum = 4;
		waitTxt.x = (W-waitTxt.getBounds().width)/2+7;
		waitTxt.y = -85;
		if(Game.type == 'easy'){
			waitTxt.y = -125 ;
		}else if(Game.type == 'normal'){
			waitTxt.y = -115;		
		}else{
			waitTxt.y = -85;		
		}	
		Game.waitTimer = setInterval(function(){
			waitTxt.text = "明牌时间:"+waitNum--;		
		},1000);
		var txtBg = new createjs.Shape();
        txtBg.graphics.beginFill('#8C0919').drawRoundRect(210, waitTxt.y-8, 244, 64, 15);
		setTimeout(function(){
			Game.gv.removeChild(txtBg);
			Game.gv.removeChild(waitTxt);
		},5000)
		Game.gv.addChild(txtBg,waitTxt);
		
		var count = 0;
		if(Game.type == 'easy'){
			count = 6;
		}else if(Game.type == 'normal'){
			count = 8;
		}else{
			count = 10;
		}
		for(var a = [], b = 0; b < count; b++) {
			a[2 * b] = a[2 * b + 1] = b % 5 +1 ;
		}		
		a.shuffle();
		Game.gv.setupCard(a);
	};
	Game.startGame = function () {
		gametimes();		
	};
	Game.gameover = function () {
		Game.gv.mouseChildren = !1;
		Game.countDown = 0;
		//$('#scoreLast').html(Game.score+"分");
		//$('.dialog-box-out').show();

		MUZHI.endGame(Game.score, function(){
			Game.wait = false;
			clearInterval(Game.interval);
			$('.dialog').add('.dialog-box-out').hide();
			$('.dialog-box-share').css("z-index",-2000);
			$('.dialog-box-share').hide();
			Game.startGame();
		}, function(){
			$('.dialog-box-share').css("z-index",20000);
			$('.dialog-box-share').show();
		}, function(){
			clearInterval(Game.interval);
			Game.wait = false;
			stage.removeAllChildren();
			Game.vhome = new Game.vHome;
			stage.addChild(Game.vhome);
			$('#container').removeClass('gameBg').addClass('homeBg');
			$('.dialog').add('.dialog-box-out').hide();
			$('.dialog-box-share').css("z-index",-2000);
			$('.dialog-box-share').hide();
		},"");
		return;
	};
			
	//游戏首页
	Game.vHome = function(){
		this.initialize();
		this.x = this.y = 0;		
		//开始游戏按钮
		var btnStart = new BtnStart(0,570,110);							
			
		this.addChild(btnStart);	
	}
	Game.readyGo = function () {
		this.initialize();
		this.x = this.y = 0;
		this.addChild(new createjs.Bitmap(Game.getResult("bg")));
		var ready = new createjs.Bitmap;
		ready.regX = 290;
		ready.regY = 80;
		ready.x = 320;
		ready.y = 550;
		var bg = new createjs.Bitmap(Game.getResult('gameBg'));
		bg.x = bg.y = 0;
		this.addChild(bg);
		this.ready = function (callbackFn) {
			ready.image = Game.getResult("ready");
			this.addChild(ready);
			ready.scaleX = ready.scaleY = 1;
			ready.regX = ready.getBounds().width / 2;
			ready.regY = ready.getBounds().height / 2;
			ready.alpha = 1 ;
			createjs.Tween.get(ready).to({alpha:1, scaleX:1, scaleY:1}, 300).to({}, 900).call(function () {
				ready.image = Game.getResult("go");
				ready.regX = ready.getBounds().width / 2;
				ready.regY = ready.getBounds().height / 2;
				createjs.Tween.get(ready).to({scaleX:1}, 300).to({alpha:0}, 200).call(function () {
					ready.parent.removeChild(ready);
					callbackFn();
				});
			});
		};
	};
	Game.vCard = function () {
		this.initialize();
		this.x = W / 2 + 7;
		this.regX = W / 2;
		this.regY = H / 3;
		if(Game.type == 'easy'){
			this.y = H / 2 + 95;
		}else if(Game.type == 'normal'){
			this.y = H / 2 + 90;
		}else{
			this.y = H / 2 + 60;
		}
		this.scaleX = this.scaleY = 0.95;
		
		this.setupCard = function (a) {
			for (var i = 0; i < a.length; i++) {
				var c = parseInt(i / Game.rows),
					vHole = new Game.vHole(c, i % Game.rows, a[i]);
				Game.wait = true;
				if(Game.type == 'easy'){
					vHole.scaleX = vHole.scaleY = 1.2;
				}	
				
				this.addChild(vHole);			
			}
		};
		this.clearCard = function () {
			this.removeAllChildren();
		};
	};
	Game.vBar = function () {
		this.initialize();
		var scoreLine = new createjs.Text("0", "bold 60px Arial", "#ac0300");
		scoreLine.textAlign = "center";
		scoreLine.x = 140;
		scoreLine.outline = 3 ;
		var score = new createjs.Text("0", "bold 60px Arial", "#ffeecd");
		score.textAlign = "center";
		score.x = 140;
		
		var countDownLine = new createjs.Text(Game.time, "bold 60px Arial", "#ac0300");
		countDownLine.textAlign = "center";
		countDownLine.x = 575;
		countDownLine.outline = 3;

		var countDown = new createjs.Text(Game.time, "bold 60px Arial", "#ffeb91");
		countDown.textAlign = "center";
		countDown.x = 575;

		scoreLine.y = 188;
		score.y = 158;
		countDownLine.y = 155;
		countDown.y = 185;
		
		scoreLine.regY = countDown.regY = scoreLine.getBounds().height / 2;
		var timebar = new createjs.Bitmap(Game.getResult('timebar'));
		timebar.x = 0;
		timebar.y = 170;
		this.addChild(score,scoreLine, countDown,countDownLine,timebar);
		
		this.on("tick", function (c) {
			scoreLine.text = Game.score;
			score.text = Game.score;
			countDown.text = Game.countDown < 0 ? 0 : Game.countDown;
			countDownLine.text = Game.countDown < 0 ? 0 : Game.countDown;
		});
	};
	Game.vHole = function (a, b, c) {
		this.initialize();
		this.x = Game.holePos(a, b)[0];
		this.y = Game.holePos(a, b)[1];
		if(Game.type == 'easy'){
			this.scaleX = this.scaleY = this.initScale = 1.2;
		}else if(Game.type == 'normal'){
			this.scaleX = this.scaleY = this.initScale = 140 / 130;		
		}else{
			this.scaleX = this.scaleY = this.initScale = 140 / 130;		
		}	
		
		this.cid = c;
		this.mouseChildren = !1;
		a = new createjs.Bitmap(Game.getResult(c));
		a.name = "bm";
		var d = new createjs.Bitmap(Game.getResult("back"));
		d.name = "back";
		var e = new createjs.Shape;
		e.name = "frame";
		e.visible = !1;
		var dW = d.getBounds().width;	
		if(dW<135){
			var scale = (135 - dW )/ dW;			
			d.scaleX = d.scaleY = 1+scale;
		}
		this.regX = 67.5;
		this.regY = 67.5;
		this.addChild(e, a);
		var _this = this;
		setTimeout(function(){
			Game.wait = false;
			Game.gv.removeChild();
			_this.addChild(d);
			clearInterval(Game.waitTimer);
		},5000)
		var hitArea = new createjs.Shape;	
		hitArea.graphics.beginFill("#000").drawRect(a.x,a.y,a.getBounds().width,a.getBounds().height);		
		this.hitArea  = hitArea;
		this.onClick(function (obj) {
			if(Game.wait == true){
				return;
			}
			obj.target.open();
		});
		this.open = function () {
			d.visible && (this.mouseEnabled = !1, createjs.Tween.get(this).to({scaleX:0}, 50).call(function () {
				d.visible = !1;
				e.visible = !0;
				createjs.Tween.get(this).to({scaleX:this.initScale}, 50).call(function () {
					Game.play(this);
					this.mouseEnabled = !0;
				});
			}), createjs.Sound.play(Game.flip, !0));
		};
		this.close = function () {
			d.visible || (this.mouseEnabled = !1, createjs.Tween.get(this).to({scaleX:0}, 50).call(function () {
				d.visible = !0;
				e.visible = !1;
				createjs.Tween.get(this).to({scaleX:this.initScale}, 50).call(function () {
					this.mouseEnabled = !0;
				});
			}));
		};
		this.overturn = function (a) {
			createjs.Tween.get(this).wait(200).to({rotation:1080, scaleX:0, scaleY:0}, 100).call(function () {
				this.parent.removeChild(this);
				a && a();
			});
		};
	};
	Game.vEnd = function () {
		this.initialize();
	};
	Game.vHome.prototype = new createjs.Container;
	Game.vCard.prototype = new createjs.Container;
	Game.vBar.prototype = new createjs.Container;
	Game.vHole.prototype = new createjs.Container;
	Game.vEnd.prototype = new createjs.Container;
	Game.readyGo.prototype = new createjs.Container;
	createjs.DisplayObject.prototype.onClick = function (a) {
		this.on("click", function (b) {
			createjs.Touch.isSupported() && b.nativeEvent.constructor == MouseEvent || a(b);
		});
	};
})();
function setupGame(){
	$('#container').removeClass('homeBg').addClass('gameBg');
	Game.view = new Game.readyGo;
	Game.gv = new Game.vCard;
	Game.sv = new Game.vBar;
	Game.view.addChild(Game.gv,Game.sv);
	stage.removeChild(Game.vhome);
	stage.addChild(Game.view);
	Game.startGame();
	
}
_cfgGame.startFunc = setupGame;
var queue;
function loadResource() {
	SCREEN_SHOW_ALL = !0;
	H = 1000;	
	queue = Game.queueHome = new createjs.LoadQueue(false);
	queue.on("complete", _cfgHome.startFunc, null, !0);	
	_cfgHome.img && queue.loadManifest(_cfgHome.img);
	queue.load();
}
var _cfgHome = {
    startFunc: setup,
    img: {
    manifest: [
	{src : '../addons/hc_ffk/style/images/TB2TD3MaFXXXXXZXXXXXXXXXXXX_!!2076518726-2-tae.png',id : 'prizeIcon'},
	{src : '../addons/hc_ffk/style/images/TB2Cl3GaFXXXXXqXpXXXXXXXXXX_!!2076518726-2-tae.png',id : 'startIcon'},
	{src : '../addons/hc_ffk/style/images/TB29sAFaFXXXXaqXpXXXXXXXXXX_!!2076518726-2-tae.png',id : 'loading'},
	{src : '../addons/hc_ffk/style/images/TB2tCwEaFXXXXaRXpXXXXXXXXXX_!!2076518726-2-tae.png',id : 'loadingTxt'},
	{src : '',id : 'homeBg'}]
    }
};
function setup() {
	Game.vhome = new Game.vHome;
	stage.addChild(Game.vhome);
	var progressTxt = new createjs.Text('0%','40px Arial','#FFF');
	progressTxt.x = W/2;	
	progressTxt.y = 570-50;
	progressTxt.textAlign = "center";	
	progressTxt.name = "progress";
	progressTxt.alpha = 0;
	Game.vhome.addChild(progressTxt);
	//加载游戏主要资源
	Game.queueGame = new createjs.LoadQueue(false);
	_cfgGame.img && Game.queueGame.loadManifest(_cfgGame.img);
	Game.queueGame.on("complete", startGame , null, 0);	
	Game.queueGame.on("progress", loadingProgress, null);
	Game.queueGame.load();
	Game.queueGame.installPlugin(createjs.Sound);
	
	createjs.Sound.alternateExtensions = ["mp3"];
	createjs.Sound.addEventListener("fileload", playSound);
	Game.flip = "../addons/hc_ffk/style/audio/flip.mp3";
	Game.bonus = "../addons/hc_ffk/style/audio/bonus.mp3";
	createjs.Sound.registerSound(Game.bonus)
	createjs.Sound.registerSound(Game.flip)
	function playSound(event) {
		
	}
}
//游戏资源加载完成回调
function startGame(){
	Game.hasLoadResource = true;
	if(Game.hasClickStart == true){
		_cfgGame.startFunc();
	}
}
//加载进度回调
function loadingProgress(a){	
	Game.vhome.getChildByName('progress').text = parseInt(100 * a.progress) + "%";	
	if(Game.hasClickStart == true){			
		Game.vhome.getChildByName('progress').alpha =1;		
	}	
}
function BtnStart(x,y,r){
	this.initialize();
	this.x = this.y = 0;
	var line1 = new createjs.Shape();
	line1.graphics.beginStroke('#F52750').drawCircle(x, y, r+35).endStroke();
	line1.alpha = 0;
	var line2 = new createjs.Shape();
	line2.graphics.beginStroke('#F52750').drawCircle(x, y, r+25).endStroke();
	line2.alpha = 0.5;
	var line3 = new createjs.Shape();
	line3.graphics.beginStroke('#F52750').drawCircle(x, y, r+15).endStroke();
	line3.alpha = 1;
	line1.regX = line2.regX = line3.regX = -W/2;
	createjs.Tween.get(line1,{loop:true}).to({alpha:0.5},200).to({alpha:1},200).to({alpha:0.5},200).to({alpha:0},200).wait(100);
	createjs.Tween.get(line2,{loop:true}).to({alpha:1},200).to({alpha:0.5},200).to({alpha:0},200).to({alpha:0.5},200).wait(100);
	createjs.Tween.get(line3,{loop:true}).to({alpha:0.5},200).to({alpha:0},200).to({alpha:0.5},200).to({alpha:1},200).wait(100);
	var circle = new createjs.Shape();
	circle.graphics.setStrokeStyle(10, 'round', 'round').beginStroke("#FFF").beginFill("#f52750").drawCircle(x, y, r).endFill();		
	circle.regX = -W/2;
	
	var startIcon = new createjs.Bitmap(Game.getHomeResult('startIcon'));
	startIcon.x = (W-startIcon.getBounds().width)/2;
	startIcon.y = y-40;
	var txt1 = new createjs.Text();
	this.hasLoading = false;
	this.hitArea = new createjs.Shape;	
	this.hitArea.graphics.beginFill("#000").drawRect((W-260)/2,y-115,260,260);					
	var _this = this;
	this.addChild(line1,line2,line3,circle,startIcon);
	_this.onClick(function(obj){
		if(Game.hasLoadResource){
			//已经加载好资源，开始游戏
			_this.removeAllChildren();
			_cfgGame.startFunc();
		}else{
			Game.hasClickStart = true;
				if(_this.hasLoading)return;
				_this.hasLoading = true;
				createjs.Tween.get(_this).to({alpha:0.2},200).call(function(){
					_this.removeAllChildren();
					_this.alpha = 1;
					var loading = new createjs.Bitmap(Game.getHomeResult('loading'));
					loading.x = W/2;	
					loading.y = y;
					loading.regX = loading.regY = 125;
					loading.alpha = 0;
					createjs.Tween.get(loading, {loop:true}).to({rotation: 360}, 1000);
							
					var loadingTxt = new createjs.Bitmap(Game.getHomeResult('loadingTxt'))
					loadingTxt.x = (W-loadingTxt.getBounds().width)/2;	
					loadingTxt.y = y-100;
					loadingTxt.alpha = 0;
					_this.addChild(loading,loadingTxt);	
					createjs.Tween.get(loading).to({alpha: 1}, 200);
					createjs.Tween.get(loadingTxt).to({alpha: 1}, 200);							
			})
		}		

	})
}
BtnStart.prototype = new createjs.Container;