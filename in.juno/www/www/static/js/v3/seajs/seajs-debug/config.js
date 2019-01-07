define(function(){
	var rules = [];
	rules.push([
		'http://inbeta.joyotime.com/static/js/',
		'http://localhost:8080/trunk/www/www/static/js/'
	]);
	seajs.config({'map':rules});
});