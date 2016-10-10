JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event;

	var Checkbox = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.checkboxElem = $D.$("input[type=checkbox]",this.elem)[0];
			this.checked = this.checkboxElem.checked;
			this.value = this.checkboxElem.value;
			this.checkedClass = options.checkedClass || "checked";
			this.bindHandler();
		},
		setValue:function(val){
			this.checkboxElem.value = this.value = val;
		},
		_handleEvent:function(e){
			var type = e.type;
			if(type == "click"){
				this._onChange(e);
			}
		},
		bindHandler:function(){
			var _handleEvent = this._handleEvent = J.bind(this._handleEvent,this);
			$E.on(this.checkboxElem,"click",_handleEvent);
		},
		_onChange:function(e){
			var target = e.target || e.srcElement; 
			this._changeState(target.checked);
		},
		check:function(){
			this._changeState(true);
		},
		uncheck:function(){
			this._changeState(false);
		},
		_changeState:function(checked){
			if(this.checked == checked) return;

			var ele = this.elem;
			var checkedClass = this.checkedClass;
			this.checked = this.checkboxElem.checked = checked;

			if(checked){
				$D.addClass(ele,checkedClass);
			}
			else{
				$D.removeClass(ele,checkedClass);
			}		
			$E.fire(this,"changed",{
				checked:checked
			});
		},
		destory:function(){
			$E.off(this.checkboxElem,"click",this._handleEvent);
			$D.remove(this.checkboxElem);
		}
	
	});
	this.Checkbox = Checkbox;
});
JM.$package("MUI",function(J){
	var $D = J.dom,
		$E = J.event,
		$T = J.type;

	var isCheckbox = function(elem){
		return elem.tagName == "INPUT" && elem.type == "checkbox";
	}


 	var CheckboxList = J.Class({
		init:function(options){
			this.elem = $D.id(options.id);
			this.list = $D.$("input[type=checkbox]",this.elem);
			this.checkedClass = options.checkedClass || "checked";
			this._initCheckboxes();
			this.bindHandlers();
		},
		getSelectedCheckBoxes:function(){
			var selectedArr = [];
			J.each(this.list ,function(c,i){
				if(c.checked) selectedArr.push(c);
			});
			return selectedArr;
		},
		getCheckbox:function(index){
			if($T.isNumber(index)){
				return this.radioBtns[index];
			}
			else if(isCheckbox(index)){
				return index;
			}
		},
		_initCheckboxes:function(){
			var self = this;
			var checkedClass = this.checkedClass;
			J.each(this.list,function(c,i){
				if(c.checked) $D.addClass(c.parentNode ,checkedClass);
				c.setAttribute("_index",i);
			});
		},
		_onClick:function(e,target){
			var cp = target.parentNode;
			var checkedClass = this.checkedClass;
			if(target.checked){
				$D.addClass(cp ,checkedClass);
			}
			else{
				$D.removeClass(cp ,checkedClass);
			}

			$E.fire(this,"change",{
				originalEventObj:e,
				checkboxChanged:target,
				index:parseInt(target.getAttribute("_index"))
			});
		},
		bindHandlers:function(){
			var self = this;
			$E.on(this.elem,"click",function(e){
				var target = e.target || e.srcElement;
				//避免点击label触发两次事件
				if(!isCheckbox(target)) return;
				self._onClick(e,target);
			});
		}
	});
	this.CheckboxList = CheckboxList;
});