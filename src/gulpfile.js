
/* ----- Used Modules ---- */

var gulp = require('gulp');
var rename = require("gulp-rename");
var changed = require('gulp-changed');
var compass = require('gulp-compass');
var filter = require('gulp-filter');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var livereload = require('gulp-livereload');


/* ----- App Config ---- */
var appConfig = require('./config.json');


/* ----- Paths ---- */
var paths = {
	src: {
		sass: 'sass/**/*.scss',
		js: 'js/**/*.js'
	},

	dest: {
		css: '../public/css',
		js: '../public/js'
	},
	
	reloadWatch: [
		'../public/js/**/*.js',
		'../public/css/**/*.css',
	]
};


/* ----- Reload Timeout setup ---- */
var reloadDelay = 700;
var reloadTimeout;


// error handling, prevent gulp from exiting on error
function onError(err) {
	console.log(err);
	this.emit('end');
}


// copy static assets
gulp.task('copy_assets', function() {
	for (var i = 0; i < appConfig.copyAssets.length; i++) {
		var item = appConfig.copyAssets[i];
		if(item.src && item.dest && item.type == "pluginCSS") {
			gulp.src(item.src).pipe(rename(function (path) {
				path.basename = "_"+path.basename;
				path.extname = ".scss"
			})).pipe(gulp.dest(item.dest));
		} else {
			gulp.src(item.src).pipe(gulp.dest(item.dest));
		}
	}
});


// JS, compile in order of appJs config array
gulp.task('scripts', function () {
	return gulp.src(appConfig.appJs)
		.pipe(concat('main.js'))
		.pipe(uglify())
		.on('error', onError)
		.pipe(gulp.dest(paths.dest.js));
});


// SASS + compass
gulp.task('compass', function () {
	return gulp.src(paths.src.sass)
		.pipe(compass({
			config_file: './config.rb',
			css: paths.dest.css,
			sass: './sass',
			sourcemap: true
		}))
		.on('error', onError)
		.pipe(gulp.dest(paths.dest.css));
});


// watch for file updates
gulp.task('watch', function () {
    livereload.listen();

	// watch for sass changes
    gulp.watch(paths.src.sass, ['compass']);

	// watch for script changes
    gulp.watch(paths.src.js, ['scripts']);

	// watch for livereload
    gulp.watch(paths.reloadWatch).on('change', function(file) {
		if(reloadDelay > 0){
			// delayed reload
			if(reloadTimeout){
				clearTimeout(reloadTimeout);
			}
			reloadTimeout = setTimeout(function(){
				livereload.changed(file.path);
			}, reloadDelay);
			
		} else {
			// instant reload
			livereload.changed(file.path);
		}
    });

});


// default task
gulp.task('default', ['scripts', 'compass', 'copy_assets']);
