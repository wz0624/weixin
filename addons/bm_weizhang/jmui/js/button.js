JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event;
	var isTouchDevice = J.platform.touchDevice;

	var startEvt = isTouchDevice ? "touchstart" : "mousedown";
	var moveEvt = isTouchDevice ?  "touchmove" : "mousemove";
	var endEvt = isTouchDevice ?  "touchend" : "mouseup";
	
	var Button = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.activeClassName = options.activeClassName || "active";
			this.disableClassName = options.disableClassName || "disable";
			this.bindHandler();
		},
		_handleEvent:function(e){
			switch (e.type) {
				case startEvt: this._onStartEvt(e); break;
				case moveEvt: this._onMoveEvt(e); break;
				case endEvt: this._onEndEvt(e); break;
			}
		},
		_onStartEvt:function(e){debugger;
			if(this._disable) return;
			var b = this.elem;
			var activeClassName = this.activeClassName;
			if(!$D.hasClass(b,activeClassName)){
				$D.addClass(b,activeClassName);
			}
		},
		_onMoveEvt:function(e){
			if(this._disable) return;
			e.preventDefault();//修复android touchend不触发的bug
		},
		_onEndEvt:function(e){
			if(this._disable) return;
			var b = this.elem;
			var activeClassName = this.activeClassName;
			if($D.hasClass(b,activeClassName)){
				$D.removeClass(b,activeClassName);
			}
			
		},
		bindHandler:function(){
			var self = this;
			var b = this.elem;
			var _handleEvent = this._handleEvent = J.bind(this._handleEvent,this);
	
			$E.on(b,startEvt,_handleEvent);
			$E.on(b,moveEvt,_handleEvent);
			$E.on(document.body,endEvt,_handleEvent);		
		},
		_setEnable:function(enable){
			$D[enable?"removeClass":"addClass"](this.elem,this.disableClassName);
		},
		enable:function(){
			this._disable = false;
			this._setEnable(true);
		},
		disable:function(){
			this._disable = true;
			this._setEnable(false);
		},
		destory:function(){
			var  b = this.elem;
			$E.off(b,[startEvt,moveEvt,endEvt].join(" "),this._handleEvent);
			$D.remove(b);
		}
	});
	this.Button = Button;
	
});
JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event,
		$T = J.type;
	var isTouchDevice = J.platform.touchDevice,
		startEvt,
		moveEvt,
		endEvt;

	isTouchDevice ? startEvt = "touchstart" : "mousedown";
	isTouchDevice ? moveEvt = "touchmove" : "mousemove";
	isTouchDevice ? endEvt = "touchend" : "mouseup";	


 	var ButtonList = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.list = $D.tagName("button" ,this.elem);
			this.activeClassName = options.activeClassName || "active";
			this._initIndex();
			this.bindHandlers();
		},
		_initIndex:function(){
			var self = this;
			J.each(this.list,function(c,i){
				c.setAttribute("_index",i);
			});
		},
		_onStart:function(e){
			var target = e.target || e.srcElement;
			var activeClassName = this.activeClassName;
			var btn = target.parentNode;
			if(!$D.hasClass(btn,activeClassName)){
				$D.addClass(btn,activeClassName);
			}
		},
		_onEnd:function(e){
			var target = e.target || e.srcElement;
			var activeClassName = this.activeClassName;
			var btn = target.parentNode;
			if($D.hasClass(btn,activeClassName)){
				$D.removeClass(btn,activeClassName);
			}
		},
		bindHandlers:function(){
			var self = this;
			var ele = this.elem;
			$E.on(ele,startEvt,function(e){
				self._onStart(e);
			});
			$E.on(ele,moveEvt,function(e){
				e.preventDefault();//修复android touchend不触发的bug
			});
			$E.on(ele,endEvt,function(e){
				self._onEnd(e);
			});
		}
	});
	this.ButtonList = ButtonList;
});