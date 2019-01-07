var redis = require("./redis"),
	util = require("util"),
	EventEmitter = require("events").EventEmitter,
	client = redis.initialize('6377', '127.0.0.1', 1);

var Queue = function(){
	
	var self = this;
	
	EventEmitter.call(this);
	
	this.dequeue = function(key) {
		key = key || 'queue';
		
		client.rpop(key, function(err, res){
			self.emit('data', res);
		});
	};
};

util.inherits(Queue, EventEmitter);

module.exports = new Queue();