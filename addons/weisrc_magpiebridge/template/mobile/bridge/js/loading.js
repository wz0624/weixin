var loadingBox = function(_p){
	var panelClass = {
		BASE:"loadingBox",
		SHOW:"show"
	};
	var panelBox = null;

	function createLoading(){
		panelBox = document.createElement("div");
		panelBox.className = panelClass.BASE;
		_p.appendChild(panelBox);

		panelBox.ontouchmove = function(e) {
            e.preventDefault();
        }
	}

	var main = {
		show:function(){
			if(!panelBox)
				return;
			panelBox.classList.add(panelClass.SHOW);
		},
		hide:function(){
			if(!panelBox)
				return;
			panelBox.classList.remove(panelClass.SHOW);
		}
	};

	createLoading();
	return main;
}