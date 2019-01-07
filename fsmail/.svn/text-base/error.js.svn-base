var nodemailer = require('./node_modules/nodemailer'),
	queue = require("./lib/queue"),
	hash = require("./lib/hash"),
	KEY_QUEUE = 'errors',
	KEY_HASH = 'unique_error',
	WAIT_TIME = 60000,
	count = 0;

console.log('Initialize queue is OK');
console.log('Initialize count to zero');

// Create reusable transport method (opens pool of SMTP connections)
var transport = nodemailer.createTransport("SMTP",{
    service: "qq",
    auth: {
        user: "noreply@fishsaying.com",
        pass: "yushuo@123"
    }
});

console.log('Sendmail Configured');

// Message object
var message = function(subject, res) {

    var content = '', context = '';

    if(res.hasOwnProperty('message')) {
        content = res.message;
    }

    if(res.hasOwnProperty('context')) {
        context = res.context;
    }

	return {
		// sender info
	    from: 'FishSaying <noreply@fishsaying.com>',

	    // Comma separated list of recipients
	    to: '"FishSaying Server Error & Exception" <baohan@fishsaying.com>',

	    // Subject of the message
	    subject: "[staging] " + subject,

	    // plaintext body
	    text: '',

	    // HTML body
	    html:'<pre>' + res.message + '</pre>' + '<br />' + '<pre>' + context + '</pre>'
	};
};

var title = function(res) {
    var lines = [];
    if(res.hasOwnProperty('message')) {
        lines = res.message.split("\n");
    }
	var subject = false;
	if(lines.length) {
		subject = lines[0];
	} else {
		subject = '来自FishSaying API server的未知错误';
	}
	
	return subject;
};

var sending = function(res) {
	hash.get(KEY_HASH, title(res), res);
};

hash.on('data', function(res, count){
	var subj = title(res);
	if(count) {
		subj += " ("+ count +")";
	} 
	
	console.log('Sending Mail');

	transport.sendMail(message(subj, res), function(error){
	    if(error){
	        console.log('Error occured');
	        console.log(error.message);
	        return;
	    }
	    console.log('Message sent successfully!');
	});
	
	hash.del(KEY_HASH, title(res));
	queue.dequeue(KEY_QUEUE);
});

hash.on('deleted', function(res){
	console.log('hash deleted');
});

queue.on('data', function(res){
	if(res == null) {
		setTimeout(function(){
			queue.dequeue(KEY_QUEUE);
		}, WAIT_TIME);
		return ;
	}
	sending(JSON.parse(res));
});

// Start to check whether there is new arrival came...
queue.dequeue(KEY_QUEUE);   