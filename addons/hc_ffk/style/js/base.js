var RES_DIR = "";
var USE_NATIVE_SOUND = !1;
var IS_IOS = navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? !0 : !1;
var IS_ANDROID = false;
var IS_NATIVE_ANDROID = IS_ANDROID && -1 < navigator.userAgent.indexOf("Version");

var stage, W = 640, H = 1000, IS_TOUCH, SCREEN_SHOW_ALL = !1;

$$Client.contextPath = $('#settings').attr('data-path');

$$Client.minscore = $('#settings').attr('data-minscore');
onload = function () {
	$$Client.time = $('#settings').attr('data-time');
	$$Client.type = $('#settings').attr('data-type');
	$$Client.isv = $('#settings').attr('data-isv');
	getResource();
	Game.time = parseInt($$Client.time);
	Game.type = $$Client.type;
	if(Game.type == 'easy'){
		Game.cols = 3;
		Game.rows = 4;
	}else if(Game.type == 'normal'){
		Game.cols = 4;
		Game.rows = 4;
	}else{
		Game.cols = 4;
		Game.rows = 5;	
	}	
	stage = new createjs.Stage("game");
	if (IS_TOUCH = createjs.Touch.isSupported()) {
		createjs.Touch.enable(stage, !0);
	}
	createjs.Ticker.setFPS(60);
	setTimeout(setCanvas, 100);
	createjs.Ticker.on("tick", stage);
	$('#btnReplay').add('#btnToReplay').on('click',function(){		
		Game.wait = false;
		clearInterval(Game.interval);
		$('.dialog').add('.dialog-box-out').hide();		
		Game.startGame();
	})
	$('#btnHome').add('#btnToHome').on('click',function(){
		clearInterval(Game.interval);
		Game.wait = false;
		stage.removeAllChildren();
		Game.vhome = new Game.vHome;
		stage.addChild(Game.vhome);		
		$('#container').removeClass('gameBg').addClass('homeBg');
		$('.dialog').add('.dialog-box-out').hide();
	})
	
	$('#btnContinue').on('click',function(){
		Game.wait = false;
		$('.dialog').hide();	
	})

	$('.dialog-box-share').on('click',function(){
		$('.dialog-box-share').css("z-index",-2000);
		$('.dialog-box-share').hide();
	})
	
	
};
function unload(){
	console.log(Game);
	Game = null;
	console.log(Game);
	$$Client = null;
}
onresize = setCanvas;
function setCanvas() {
	var a = stage.canvas, b = window.innerWidth, c = window.innerHeight - 3;
	if (SCREEN_SHOW_ALL) {
		var d = c;
		b / c > W / H ? b = W * c / H : c = H * b / W;
		a.style.marginTop = (d - c) / 2 + "px";
	} else {
		d = W * c / H, b >= d ? (b = d, stage.x = 0) : stage.x = (b - d) / 2;
	}
	$('#container').height(c+4);
	a.width = W;
	a.height = H;
	a.style.marginLeft = -(b/2)+"px";
	a.style.width = b + "px";
	a.style.height = c + "px";
}
createjs.DisplayObject.prototype.do_cache = function () {
	var a = this.getBounds();
	this.cache(a.x, a.y, a.width, a.height);
};

function ProgressBar(a, b) {
	this.initialize();
}
ProgressBar.prototype = new createjs.Container;
ProgressBar.prototype.completeCallback = function (a) {
	this.parent.removeChild(this);
};

ProgressBar.prototype.forQueue = function (a) {
	this.errorList = [];
	a.on("complete", this.completeCallback, this, !0);
	a.on("error", function (a) {
	}, null, !0);
	a.on("error", function (a) {
		this.errorList.push(a.item.src);
	}, this);
};



USE_NATIVE_SOUND ? createjs.Sound.play = function (a) {

} : IS_NATIVE_ANDROID && (createjs.Sound.play = function (a, b) {
	var c = queue.getResult("sound");
	c.currentTime = this.soundSprite[a];
	c.play();
	void 0 != b && !0 == b && (null != g_androidsoundtimer && (clearTimeout(g_androidsoundtimer), g_androidsoundtimer = null), g_androidsoundtimer = setTimeout(function () {
		createjs.Sound.play("silenttail");
	}, 1000));
}, createjs.Sound.registMySound = function (a, b) {
	this.soundSprite || (this.soundSprite = {});
	this.soundSprite[a] = b;
});


