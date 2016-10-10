JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event;
	var isTouchDevice = J.platform.touchDevice;
	var dragingElem;
	var isTouchDevice = J.platform.touchDevice;
	var startEvt = isTouchDevice ? "touchstart" : "mousedown";
	var moveEvt = isTouchDevice ? "touchmove" : "mousemove";
	var endEvt = isTouchDevice ? "touchend" : "mouseup";
	var hasClientRect = "getBoundingClientRect" in document.body;

	var Slide = J.Class({
		init:function(options){
			
			this.elem = $D.id(options.id)||options.id;
			this.wrapClassName = options.wrapClassName || "wrap";
		
			this.contentWrap = $D.$("." + this.wrapClassName,this.elem)[0];
			this.contents = $D.$("." + this.wrapClassName + ">li",this.contentWrap);
			this.count = this.contents.length;
			this.currentIndex = options.currentIndex || 0;
			this.moveDist = 0;
			this.runType = options.runType || "ease-out";
			this.slideTime = options.slideTime || 200;
			this.fastChange = options.fastChange;
			this._sizeAdjust();
			this._moveTo(this.currentIndex * -this.contentWidth);
			this.bindHandlers();
		},
		bindHandlers:function(){
			var startX = 0;
			var self = this;
			var elem = this.elem;
			$E.on(this.contentWrap,"webkitTransitionEnd",function(e){
				// self._removeAnimation();
				$E.fire(self ,"changed" ,{
					type:"changed",
					currentIndex:self.currentIndex
				});
			});
			//用于fastchange恢复
			$E.on(self ,"changed" ,function(e){
				if(!self.fastChange || !self.hideArr) return;
				while(self.hideArr[0]){
					$D.setStyle(self.hideArr[0],"display","");
					self.hideArr.shift();
				}
				self._removeAnimation();
				self._moveTo(e.currentIndex * -self.contentWidth);
			});
		},
		_removeAnimation:function(ele){
			this.contentWrap.style["-webkit-transition"] = "";//删除动画效果	
		},
		_sizeAdjust:function(){
			var ele = this.elem;
			var count = this.count;
			//幻灯片宽度
			var contentWidth = hasClientRect ? ele.getBoundingClientRect().width : ele.offsetWidth;
		
			$D.setStyle(this.contentWrap , "width" ,contentWidth * count + "px");
			J.each(this.contents ,function(e){
				$D.setStyle(e,"width",contentWidth + "px");
			});

			this.contentWidth = contentWidth;
		},
		_moveTo:function(x){
			//webkit和moz可用3D加速，ms和o只能使用translate
			this.contentWrap.style["-webkit-transform"] = "translate3d("+ x + "px, 0,0 )";
		},
		slideTo:function(index){
			var self = this;
			var currentIndex = this.currentIndex;
			var d_index = index - currentIndex;
			this.currentIndex  = index ;
			
			if(this.fastChange && d_index && Math.abs(d_index) != 1){
				if(d_index != 0){
					var l,p;
					var cts = this.contents;
					if(!this.hideArr) this.hideArr = [];
					if(d_index > 0) {
						l = d_index -1;
						p = 1; 
						index = currentIndex + 1;
					}
					else {
						l = -(d_index + 1);
						p = -1; 
						this._removeAnimation();
						this._moveTo((this.currentIndex+1) * -this.contentWidth);
					}
				
					for(var i = 1;i <= l; i++){
						var ct = cts[currentIndex + i * p];

						$D.setStyle(ct,"display","none");
						this.hideArr.push(ct);
					}
					
				}
			}
			//不加setTimeout 0 在fastchange并且倒行的时候会闪白
			setTimeout(function(){
				self.contentWrap.style["-webkit-transition"] = "all " + self.slideTime/1000 +"s " + self.runType;
				self._moveTo(index * -self.contentWidth);
			},0);
		},
		next:function(){
			var index = this.currentIndex + 1;
			if(index >= this.count) return;
			this.slideTo(index);
		},
		pre:function(){
			var index = this.currentIndex - 1;
			if(index < 0) return;
			this.slideTo(index);
		}

	});
	this.Slide = Slide;
});JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event;
	var isTouchDevice = J.platform.touchDevice;
	var dragingElem;
	var isTouchDevice = J.platform.touchDevice;
	var startEvt = isTouchDevice ? "touchstart" : "mousedown";
	var moveEvt = isTouchDevice ? "touchmove" : "mousemove";
	var endEvt = isTouchDevice ? "touchend" : "mouseup";
	var hasClientRect = "getBoundingClientRect" in document.body;

	var SwipeChange = J.Class({extend:MUI.Slide},{
		init:function(options){
			SwipeChange.callSuper(this,"init",options);
			this.startX = 0;
		},
		_handleEvent:function(e){
			SwipeChange.callSuper(this,"_handleEvent",e);
			switch (e.type) {
				case startEvt: this._onStartEvt(e); break;
				case moveEvt: this._onMoveEvt(e); break;
				case endEvt: this._onEndEvt(e); break;
			}
		},
		_onStartEvt:function(e){
			var elem = this.elem;
			var target = e.target||e.srcElement;
			if(!$D.closest(target ,"." + this.wrapClassName)) return;
			dragingElem = target;
			var tou = e.touches? e.touches[0] : e;
			var elemLeft = hasClientRect ? elem.getBoundingClientRect().left : elem.offsetLeft;

			var x = tou.clientX - elemLeft;
			this.startX = x;//相对于container
		},
		_onMoveEvt:function(e){
			if(!dragingElem) return;
			e.preventDefault();
			var elem = this.elem;
			var tou = e.touches? e.touches[0] : e;
			var x = tou.clientX;
			var elemLeft = hasClientRect ? elem.getBoundingClientRect().left : elem.offsetLeft;
			var elemRight = elemLeft + this.contentWidth;

			if(x < elemLeft || x > elemRight) return;
			x = x - elemLeft;

			this.moveDist = x - this.startX;
			this._removeAnimation();
			this._moveTo(this.currentIndex * -this.contentWidth + this.moveDist);
			// e.preventDefault();
				
		},
		_onEndEvt:function(e){
			if(!dragingElem) return;

			var d = this.moveDist;
			var elem = this.elem;
			var currentIndex = this.currentIndex;
			var elemLeft = hasClientRect ? elem.getBoundingClientRect().left : elem.offsetLeft;
			var elemHalf = elemLeft + this.contentWidth/2;
			
			if(d > elemHalf) {
				currentIndex = Math.max(0 ,currentIndex - 1);
			}
			else if(d < - elemHalf) {
				currentIndex = Math.min(this.contents.length - 1 ,currentIndex + 1);
			}
			// self._moveTo(currentIndex * -self.contentWidth);
			this.slideTo(currentIndex);
			dragingElem = null;
		},
		bindHandlers:function(){
			var _handleEvent = this._handleEvent = J.bind(this._handleEvent , this);
			SwipeChange.callSuper(this,"bindHandlers");
			$E.on(this.elem,[startEvt,moveEvt,endEvt].join(" "), _handleEvent);
		},
		destory:function(){
			$E.off(this.elem,[startEvt,moveEvt,endEvt].join(" "), this._handleEvent);	
			SwipeChange.callSuper(this,"destory");
		}

	});
	this.SwipeChange = SwipeChange;
});