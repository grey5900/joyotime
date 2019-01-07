var nodemailer = require('./node_modules/nodemailer'),
	queue = require("./lib/queue"),
	i18n = require("./node_modules/i18n"),
	emailTemplates = require('./node_modules/email-templates'),
	path = require('path'),
	templatesDir = path.resolve(__dirname, 'templates'),
	KEY = 'welcome',
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
	var subject = '', username = '', email = '', locale = 'zh_CN';
    var ios = '', android = ''; // download link for ios and android
	
	if(json.hasOwnProperty('locale')) {
		locale = json.locale;
	}
	if(json.hasOwnProperty('username')) {
		username = json.username;
	}
	if(json.hasOwnProperty('email')) {
		email = json.email;
	}
    if(json.hasOwnProperty('ios')) {
        ios = json.ios;
    }
    if(json.hasOwnProperty('android')) {
        android = json.android;
    }

    i18n.configure({
        locales:['en_US', 'zh_CN'],
        directory: './locales/welcome',
        defaultLocale: locale
    });
    i18n.init();
    console.log('i18n Configured');

	subject = i18n.__('欢迎加入鱼说！');
	
	
	emailTemplates(templatesDir, function(err, template) {
	  	if (err) {
	      	console.log(err);
	      	
	  	} else {
        	//页面文字
        	var tempMsg = {
	          	tmpMsg: {
		            username:username,
		            hyjr: i18n.__('欢迎加入鱼说！'),
		            msks: i18n.__('马上开始你的鱼说之旅吧！'),
		            ysdr: i18n.__('鱼说的任意门'),
		            xzys: i18n.__('下载鱼说客户端'),
		            xtyj: i18n.__('系统邮件，请勿回复'),
		            lxwm: i18n.__('联系我们：'),
		            iosurl: ios,
		            androidurl: android
	          	}
        	};
        	template('welcome', tempMsg, function(err, html, text) {
		        if (err) {
		            console.log(err);
		        } else {
		        	var _data={
						// sender info
					    from: 'FishSaying <noreply@fishsaying.com>',
					    // Comma separated list of recipients
					    to: '"'+username+'" <'+email+'>',
					    // Subject of the message
					    subject: subject, //
					    // plaintext body
					    text: '',
					    // HTML body
					    html:html
					};
		            console.log('Sending Mail');
					transport.sendMail(_data, function(error){
					    if(error){
					        console.log('Error occured');
					        console.log(error.message);
					        return;
					    }
					    console.log('Message sent successfully!');
					});
		        }
          	});
      	}
    });
};

queue.on('data', function(res){
	if(res == null) {
		setTimeout(function(){
			queue.dequeue(KEY);
		}, WAIT_TIME);
		return ;
	}
	message(JSON.parse(res));
	queue.dequeue(KEY);
});

// Start to check whether there is new arrival came...
queue.dequeue(KEY);