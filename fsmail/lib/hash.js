var redis = require("./redis"),
	util = require("util"),
	EventEmitter = require("events").EventEmitter,
	client = redis.initialize('6377', '127.0.0.1', 1);

var Hash = function(){
	
	var self = this;
	
	EventEmitter.call(this);
	
	this.get = function(key, field, res) {
		key = key || 'hash';
		
		client.hget(key, field, function(err, count){
			self.emit('data', res, count);
		});
	};
	
	this.del = function(key, field) {
		key = key || 'hash';
		
		client.hdel(key, field, function(err, res) {
			self.emit('deleted', res);
		});
	};
};

util.inherits(Hash, EventEmitter);

module.exports = new Hash();