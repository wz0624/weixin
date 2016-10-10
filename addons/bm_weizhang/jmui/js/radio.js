JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event;

	var Radio = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.radioElem = $D.$("input[type=radio]",this.elem)[0];
			this.checkedClassName = options.checkedClassName || "checked";
			this.checked = this.radioElem.checked;
			this.value = this.radioElem.value;
			this.bindHandler();
		},
		_handleEvent:function(e){
			var type = e.type;
			if(type == "click"){
				this._onClick(e);
			}
		},
		bindHandler:function(){
			var _handleEvent = this._handleEvent = J.bind(this._handleEvent,this);
			$E.on(this.radioElem,"click",_handleEvent);
		},
		setValue:function(val){
			this.radioElem.value = this.value = val;
		},
		uncheck:function(){
			this._changeState(false);
		},
		check:function(){
			this._changeState(true);
		},
		_changeState:function(checked){
			var re = this.radioElem;
			var checkedClassName = this.checkedClassName;

			this.checked = re.checked = checked;
			if(checked)
				$D.addClass(this.elem,checkedClassName);
			else
				$D.removeClass(this.elem,checkedClassName);
			//触发selected事件
			$E.fire(this,"chaged",{
				checked:checked
			});
		},
		_onClick:function(e){
			//当已经是选中状态，不触发selected事件
			if(this.checked) return;
			this._changeState(this.radioElem.checked);
		},
		destory:function(){
			$E.off(this.radioElem,"click",this._handleEvent);
			$D.remove(this.elem);
		}
	
	});
	this.Radio = Radio;
});JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event,
		$T = J.type;

	var isRadio = function(elem){
		return elem.tagName == "INPUT" && elem.type == "radio";
	}

 	var RadioList = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.list = $D.$("input[type=radio]",this.elem);
			this.checkedClassName = options.checkedClassName || "checked";

			this._initRadios();
			this.bindHandlers();
		},
		getSelectedIndex:function(){
			return this.selectedIndex;
		},
		getSelectedRadio:function(){
			return this.list[this.selectedIndex];
		},
		_initRadios:function(){
			var self = this;
			var checkedClassName = this.checkedClassName;
			J.each(this.list,function(r,i){
				if(r.checked) {
					$D.addClass(r.parentNode ,checkedClassName);
					self.selectedIndex = i;
				}
				r.setAttribute("_index",i);
			});
		},
		_onClick:function(e,target){
			var checkedClassName = this.checkedClassName;
			var selectedIndex = target.getAttribute("_index");
			//点击相同项不触发chang事件
			if(selectedIndex == this.selectedIndex) return;
			//更换样式
			$D.addClass(target.parentNode ,checkedClassName);
			if(!$T.isUndefined(this.selectedIndex))
				$D.removeClass(this.list[this.selectedIndex].parentNode,checkedClassName);
			//重置selectedIndex
			this.selectedIndex = selectedIndex;

			//触发change事件
			$E.fire(this,"change",{
				originalEventObj:e,
				radioSelected:target,
				selectedIndex:selectedIndex
			});
		},
		bindHandlers:function(){
			var self = this;
			$E.on(this.elem,"click",function(e){
				var target = e.target || e.srcElement;
				//避免点击label触发两次事件
				if(!isRadio(target)) return;
				self._onClick(e,target);
			});
		}
	});
	this.RadioList = RadioList;
});