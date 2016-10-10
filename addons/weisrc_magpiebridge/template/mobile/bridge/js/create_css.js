var CSSCreate = function() {
	var style;

	function create() {
		style = document.createElement("style");
		style.type = "text/css";
	}

	var main = {
		produce: function() {
			document.body.appendChild(style);
		},
		add: function(name, content) {
			style.innerHTML += name + "{" + content + "}";
		}
	}
	create();
	return main;
}