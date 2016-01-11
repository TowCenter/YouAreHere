/*!
 * https://packagecontrol.io/packages/SCSS <-- drop into packages dir, restart sublime
 * gulp
 * $ gem install sass
 * $ gem install compass --pre
 * $ compass init
 * $ sudo npm cache clean
 * $ sudo npm install -g n
 * $ sudo n stable
 * $ npm init
 * $ npm install -g gulp
 * $ npm install gulp gulp-ruby-sass gulp-compass gulp-autoprefixer gulp-sourcemaps gulp-minify-css gulp-uglify del browser-sync gulp-useref gulp-if run-sequence --save-dev
 */

 // Load plugins
var gulp = require('gulp')
	,sass = require('gulp-ruby-sass')
	,compass = require('gulp-compass')
	,autoprefixer = require('gulp-autoprefixer')
	,sourcemaps = require('gulp-sourcemaps')
	,minifyCSS = require('gulp-minify-css')
	,uglify = require('gulp-uglify')
	,browsersync = require('browser-sync')
	,del = require('del')
	,useref = require('gulp-useref')
	,gulpIf = require('gulp-if')
	,runSequence = require('run-sequence');


// Development Tasks 
// -----------------

// Live reload task via Browser Sync
gulp.task('browsersync', function() {
	browsersync({
		server: {
			baseDir: "src"
		}
	});
});

// Compile our SASS into CSS into one minified file
gulp.task('sass', function() {
	return sass('src/sass/styles.scss', { compass: true, sourcemap: true })
	    .pipe(autoprefixer('last 2 version'))
	    .pipe(gulp.dest('src/css/'))
	    .pipe(browsersync.reload({stream:true}));
});

// Watchers: Reload browser with CSS, JS, HTML as we develop
gulp.task('watch', ['browsersync'], function() {

  gulp.watch('src/sass/**/*.scss', ['sass'], browsersync.reload);
  gulp.watch('src/js/**/*.js', browsersync.reload);
  gulp.watch('src/*.html', browsersync.reload);

});


// Optimization Tasks 
// ------------------

// Optimizing CSS and JavaScript 
gulp.task('useref', function(){
  var assets = useref.assets();

  return gulp.src('src/*.html')
    .pipe(assets)
    // Minifies only if it's a CSS file
    .pipe(gulpIf('src/*.css', minifyCSS()))
    // Uglifies only if it's a Javascript file
    .pipe(gulpIf('src/*.js', uglify()))
    .pipe(assets.restore())
    .pipe(useref())
    .pipe(gulp.dest('build'))
});

// Copy php to our build folder
gulp.task('php', function() {
  return gulp.src('src/*.php')
  .pipe(gulp.dest('build'));
});

// Copy images to our build folder
gulp.task('images', function() {
  return gulp.src('src/img/**/*')
  .pipe(gulp.dest('build/img'));
});

// Copy fonts to our build folder
gulp.task('fonts', function() {
  return gulp.src('src/fonts/**/*')
  .pipe(gulp.dest('build/fonts'));
})

// Clean up! Delete the previous build
gulp.task('clean', function() {
    return del('build');
});


// Build Tasks
// -----------

// Default: Clean and then update CSS + JS
gulp.task('default', ['clean'], function() {
    gulp.start('watch');
});

// Build: Clean and then prepare assets for uploading to server
gulp.task('build', ['clean'], function() {
    runSequence( ['sass', 'useref', 'php', 'images', 'fonts'] );
});