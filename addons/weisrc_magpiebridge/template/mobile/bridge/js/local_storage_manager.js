window.fakeStorage = {
  _data: {},

  setItem: function(id, val) {
    return this._data[id] = String(val);
  },

  getItem: function(id) {
    return this._data.hasOwnProperty(id) ? this._data[id] : undefined;
  },

  removeItem: function(id) {
    return delete this._data[id];
  },

  clear: function() {
    return this._data = {};
  }
};

function LocalStorageManager() {
  this.bestScoreKey = "bestScore_" + window.config_custom.NAME;
  this.needGuideKey = "needGuide_" + window.config_custom.NAME;

  var supported = this.localStorageSupported();
  this.storage = supported ? window.localStorage : window.fakeStorage;
}

LocalStorageManager.prototype.localStorageSupported = function() {
  var testKey = "test";
  var storage = window.localStorage;

  try {
    storage.setItem(testKey, "1");
    storage.removeItem(testKey);
    return true;
  } catch (error) {
    return false;
  }
};

LocalStorageManager.prototype.getBestScore = function() {
  return this.storage.getItem(this.bestScoreKey) || 0;
};

LocalStorageManager.prototype.setBestScore = function(score) {
  this.storage.setItem(this.bestScoreKey, score);
};

LocalStorageManager.prototype.getGuide = function() {
  return this.storage.getItem(this.needGuideKey) || true;
};

LocalStorageManager.prototype.setGuide = function(flag) {
  this.storage.setItem(this.needGuideKey, flag);
};