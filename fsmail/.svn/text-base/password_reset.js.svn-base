var nodemailer = require('./node_modules/nodemailer'),
	queue = require("./lib/queue"),
	i18n = require("./node_modules/i18n"),
	emailTemplates = require('./node_modules/email-templates'),
	path = require('path'),
	templatesDir = path.resolve(__dirname, 'templates'),
	KEY = 'password_reset',
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
	console.log(json);
	var subject = '', username = '', url = '', email = '', locale = 'zh_CN';
    var ios = '', android = ''; // download link for ios and android
	
	if(json.hasOwnProperty('locale')) {
		locale = json.locale;
	}
	if(json.hasOwnProperty('url')) {
		url = json.url;
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
        directory: './locales/password_reset',
        defaultLocale: locale
    });
    i18n.init();
    console.log('i18n Configured');

	subject = i18n.__('重置鱼说密码');
	
	emailTemplates(templatesDir, function(err, template){
		console.log(templatesDir);
      	if (err) {
          	console.log(err);
      	} else {
          	//页面文字
	        var tempMsg = {
	          	tmpMsg: {
		            username:username,
		            qad: i18n.__('亲爱的'),
                    separator:i18n.__('名字分行符'),
		            ntjl: i18n.__('您提交了重置密码申请，点击以下链接，即可重置密码：'),
		            pwdurl: url,
		            rgnw: i18n.__('(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)'),
		            wlbz: i18n.__('为了保障您帐号的安全性，请在 24小时内完成重置密码操作，此链接将在密码重置成功后失效！'),
		            xzys: i18n.__('下载鱼说客户端'),
		            xtyj: i18n.__('系统邮件，请勿回复'),
		            lxwm: i18n.__('联系我们：'),
		            iosurl: ios,
		            androidurl: android
	          	}
	        };
        
	        template('password_reset', tempMsg, function(err, html, text) {
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
						html:html
					    // HTML body
					    // html:'<p>'+i18n.__('亲爱的')+' '+username+':</p>'+
							 // '<p>'+i18n.__('您提交了重置密码申请，点击以下链接，即可重置密码：')+'</p>'+
					         // '<p>'+url+'</p>' + 
							 // '<p>'+i18n.__('(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)')+'</p>'+
							 // '<p>'+i18n.__('为了保障您帐号的安全性，请在 24小时内完成重置密码操作，此链接将在密码重置成功后失效！')+'</p>'+
							 // '<p>'+i18n.__('下载鱼说客户端')+'<a href="'+ios+'">Apple</a> <a href="'+android+'">Android</a></p>'+
							 // '<p>'+i18n.__('联系我们：')+' contact@fishsaying.com</p>'+
							 // '<p>'+i18n.__('系统邮件，请勿回复')+'</p>'+
							 // '<p>Copyright 2013 FishSaying Technology Co., Ltd. All rights reserved</p>'
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
		console.log('Waiting for new request...');
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