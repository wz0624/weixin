var pop_up_note_mode = true;
var note_id = 1;
function _objid(name) {
	return document.getElementById(name);
}

function switchsound() {
	au = _objid('bgsound');
	ai = _objid('sound_image');
	if(au.paused) {
		au.play();
		ai.src = "../addons/wdl_pyqqt/template/public/img/music_note_big.png";
		pop_up_note_mode = true;
		popup_note();
		_objid("mp3").innerHTML = "打开";
		_objid("mp3").style.visibility = "visible";
		setTimeout(function(){_objid("mp3").style.visibility="hidden"}, 2500);
	} else {
		pop_up_note_mode = false;
		au.pause();
		ai.src = "../addons/wdl_pyqqt/template/public/img/music_note_big.png";
		_objid("mp3").innerHTML = "关闭";
		_objid("mp3").style.visibility = "visible";
		setTimeout(function(){_objid("mp3").style.visibility="hidden"}, 2500);
	}
}

function on_pop_note_end(event) {
	note = event.target;	
	if(note.parentNode == _objid("note_box")) {
		_objid("note_box").removeChild(note);
	}
}

function popup_note() {
	box = _objid("note_box");	
	note = document.createElement("span");
	note.style.cssText = "visibility:visible;position:absolute;background-image:url('../addons/wdl_pyqqt/template/public/img/music_note_small.png');width:15px;height:25px";
	note.style.left = Math.random() * 20 + 20;
	note.style.top = "75px";
	this_node = "music_note_" + note_id;
	note.setAttribute("ID", this_node);
	note_id += 1;
	scale = Math.random() * 0.4 + 0.4;
	note.style.webkitTransform = "rotate(" + Math.floor(360 * Math.random()) + "deg) scale(" + scale + "," + scale + ")";
	note.style.webkitTransition = "top 2s ease-in, opacity 2s ease-in, left 2s ease-in";
	note.addEventListener("webkitTransitionEnd", on_pop_note_end);
	box.appendChild(note);
	setTimeout("document.getElementById('" + this_node + "').style.left = '0px';", 100);
	setTimeout("document.getElementById('" + this_node + "').style.top = '0px';", 100);
	setTimeout("document.getElementById('" + this_node + "').style.opacity = '0';", 100);
	if(pop_up_note_mode) {
		setTimeout("popup_note()", 600);
	}	
}

function playbksound() {
	var audiocontainer = _objid('audiocontainer');
	if(audiocontainer != undefined) {
		audiocontainer.innerHTML = '<audio id="bgsound" loop="loop" autoplay="autoplay"> <source src="' + gSound + '" /> </audio>';
	}	
	var audio = _objid('bgsound');
	audio.play();
	sound_div = document.createElement("div");
	sound_div.setAttribute("ID", "cardsound");
	sound_div.style.cssText = "position:fixed;right:20px;top:40px;z-index:50000;visibility:visible;";
	sound_div.onclick = switchsound;
	if(document.body.offsetWidth > 400) {
	    bg_htm = "<img id='sound_image' src='../addons/wdl_pyqqt/template/public/img/music_note_big.png'>";
    	box_htm = "<div id='note_box' style='height:100px;width:44px;position:absolute;left:-20px;top:-80px'></div>";
	} else {
	    bg_htm = "<img id='sound_image' width='30px' src='../addons/wdl_pyqqt/template/public/img/music_note_big.png'>";
    	box_htm = "<div id='note_box' style='height:100px;width:44px;position:absolute;left:-5px;top:-80px'></div>";
	}
	txt_htm = "<div id='mp3' style='color:white;position:absolute;left:-40px;top:30px;width:60px'></div>"
	sound_div.innerHTML = bg_htm + box_htm + txt_htm;
	document.body.appendChild(sound_div);
	setTimeout("popup_note()", 100);
}	
setTimeout("playbksound()", 500);