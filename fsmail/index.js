
var queue = require("./queue"),
	key = 'feedbacks';

console.log(queue);
//queue.dequeue(key);

setTimeout(function(){
	queue.dequeue(key);
}, 1000);

queue.on('data', function(res){
	console.log(res);
	obj = JSON.parse(res);
	console.log(obj);
//	console.log(obj.user_id);
});