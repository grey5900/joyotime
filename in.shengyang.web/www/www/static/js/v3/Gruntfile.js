module.exports = function(grunt){
	grunt.initConfig({
		transport:{
			options:{
				format:'dest/seajs/{{filename}}',
			},
			seajs:{
				files:{
					'.build':['seajs/main.js','seajs/nav.js','seajs/banner.js']
				}
			}
		},
		concat:{
			lib:{
				src:[
					"lib/jquery-1.8.3.min.js",
					"lib/bootstrap.js",
					"lib/jquery.infinitescroll.js",
					"lib/jquery.ajaxfileupload.js",
					"lib/jquery.placeholder.js",
					"lib/jquery.masonry.min.js",
					"v2/extend.js",
					"extend.js",
					"common.js",
					"nav.js"					
				],
				dest:"dest/lib.js"
			},
			seajs:{
				options:{
					relative:true
				},
				files:{
					'dest/seajs/main-debug.js':['.build/seajs/main.js']					
					,'dest/seajs/banner-debug.js':['.build/seajs/banner.js']
					,'dest/seajs/nav-debug.js':['.build/seajs/nav.js']					
				}
			}
		},
		uglify:{
			build:{
				src:'dest/lib.js',
				dest:'dest/lib.min.js'
			},
			common:{
				src:'common.js',
				dest:'dest/common.min.js'
			},
			module:{
				files:{
					'dest/waterfall.min.js':['waterfall.js'],
					'dest/channel.min.js':['channel.js'],
					'dest/timeline.min.js':['timeline.js']
				}
			},
			seajs:{
				files:{
					//'seajs/main.min.js':['dest/seajs/main-debug.js']
					'dest/seajs/banner.min.js':['dest/seajs/banner-debug.js']
					,'dest/seajs/nav.min.js':['dest/seajs/nav-debug.js']
				}
			}
		},
		clean:{
			build:['.build']
		}
	});
	grunt.loadNpmTasks('grunt-cmd-transport');
    //grunt.loadNpmTasks('grunt-cmd-concat');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.registerTask('default',['transport','concat','uglify','clean']);
	grunt.registerTask('build',['transport','concat:seajs','uglify:seajs','clean']);
	grunt.registerTask('com',['uglify:common','clean']);
	grunt.registerTask('module',['uglify:module','clean']);
	grunt.registerTask('lib',['concat:lib','uglify:build','clean']);
}