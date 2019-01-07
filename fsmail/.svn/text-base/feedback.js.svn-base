var nodemailer = require('./node_modules/nodemailer'),
	queue = require("./lib/queue"),
	KEY = 'feedbacks',
	WAIT_TIME = 10000,
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
var message = function(json) {
	
	var subject, username, contact;
	
	if(!json.hasOwnProperty('user_id')) {
		subject = '来自匿名用户的反馈';
		username = '匿名';
	} else {
		subject = '来自用户[' + json.username + ']的反馈';
		username = json.username;
	}
	
	if(json.hasOwnProperty('contact')) {
		contact = json.contact;
	} else if(json.hasOwnProperty('email')) {
		contact = json.email;
	} else {
		contact = '没有留下任何联系方式';
	}
	
	return {
		// sender info
	    from: 'FishSaying <noreply@fishsaying.com>',

	    // Comma separated list of recipients
	    to: '"FishSaying Feedback" <feedback@fishsaying.com>',

	    // Subject of the message
	    subject: subject, //

	    // plaintext body
	    text: '',

	    // HTML body
	    html:'<p><b>用户名称：</b> ' + username + '</p>'+
	         '<p><b>联系方式：</b> ' + contact + '</p>' + 
	         '<p><b>UserAgent：</b> ' + json.user_agent + '</p>' + 
	         '<p>' + json.content + '</p>'
	};
};

queue.on('data', function(res){
	if(res == null) {
		console.log('Waiting for new feedback...');
		setTimeout(function(){
			queue.dequeue(KEY);
		}, WAIT_TIME);
		return ;
	}
	
	console.log('Sending Mail');

	transport.sendMail(message(JSON.parse(res)), function(error){
	    if(error){
	        console.log('Error occured');
	        console.log(error.message);
	        return;
	    }
	    console.log('Message sent successfully!');
	});
	
	queue.dequeue(KEY);
});

// Start to check whether there is new arrival came...
queue.dequeue(KEY);