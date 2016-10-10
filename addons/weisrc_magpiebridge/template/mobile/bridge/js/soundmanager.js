var SoundManager = (function() {
	var soundCache = {};
	var soundCurrent = null;

	var main = {
		removeAllSounds: function() {
			for (var i in soundCache) {
				if (soundCache[i]) {
					soundCache[i].release();
				}
			}
		},
		registerSound: function(data) {
			soundCache[data.id] = new Sound(data.src);
		},
		play: function(id, option) {
			option = option || {};
			var loopTimes = option.loop || 0;
			var loop = loopTimes < 0 ? true : false;
			var canplaythrough = option.canplaythrough;
			if (typeof canplaythrough === "function") {
				soundCache[id].addEventListener("canplaythrough", function() {
					soundCache[id].play(loop);
				});
			} else {
				if(soundCurrent && soundCurrent != soundCache[id]){
					soundCache[id].stop();
				}
				soundCache[id].play(loop);
			}
			soundCurrent = soundCache[id];
			return soundCache[id];
		}
	};
	return main;
})();