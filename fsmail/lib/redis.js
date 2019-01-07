var redis = require("../node_modules/redis");

exports.initialize = function(port, host, db) {
	port = port || 6773;
	host = host || '127.0.0.1';
	db = db || 0;
	
	client = redis.createClient(port, host);
	
	// if you'd like to select database 3, instead of 0 (default), call
	client.select(db, function(err) { 
		if(err != null) {
			console.log("Error " + err);
		} else {
			console.log('DB: ' + db + ' selected');
		}
	});
	
	client.on("error", function (err) {
	    console.log("Error " + err);
	});
	
	console.log('Redis configured');
	
	return client;
}