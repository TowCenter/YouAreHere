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

var path = {
  PHP: 'src/*.php',
  ALL: ['src/js/*.js', 'src/js/**/*.js', 'src/index.php'],
  JS: ['src/js/*.js', 'src/js/**/*.js'],
  MINIFIED_JS_OUT: 'main.min.js',
  SASS: 'src/sass/styles.scss',
  CSS: ['src/css/*.css', 'src/css/**/*.css'],
  MINIFIED_CSS_OUT: 'styles.min.css',
  LIB: 'src/lib/**/*',
  IMG: 'src/img/**/*',
  FONTS: 'src/fonts/**/*',
  DEST_CSS_SRC: 'src/css/',
  DEST_JS_SRC: 'src/js/',
  SRC: 'src/',
  BUILD: 'build/'
};

// Development Tasks 
// -----------------

// Live reload task via Browser Sync
gulp.task('browsersync', function() {
	browsersync({
		server: {
			baseDir: path.SRC
		}
	});
});

// Watchers: Reload browser with CSS, JS, HTML as we develop
gulp.task('watch', ['browsersync'], function() {

  gulp.watch('src/sass/**/*.scss', ['sass'], browsersync.reload);
  gulp.watch(path.JS, browsersync.reload);
  gulp.watch(path.PHP, browsersync.reload);

});


// Optimization + Build Tasks 
// --------------------------

// Clean up! Delete the previous build
gulp.task('clean', function() {
    return del(path.BUILD);
});

// Compile our SASS into CSS into one minified file
gulp.task('sass', function() {
  return sass(path.SASS, { compass: true, sourcemap: true })
      .pipe(autoprefixer('last 2 version'))
      .pipe(gulp.dest(path.DEST_CSS_SRC))
      .pipe(browsersync.reload({stream:true}));
});

// Copy fonts to our build folder
gulp.task('fonts', function() {
  return gulp.src(path.FONTS)
  .pipe(gulp.dest(path.BUILD+'fonts'));
});

// Copy images to our build folder
gulp.task('images', function() {
  return gulp.src(path.IMG)
  .pipe(gulp.dest(path.BUILD+'img'));
});

// Copy lib to our build folder
gulp.task('lib', function() {
  return gulp.src(path.LIB)
  .pipe(gulp.dest(path.BUILD+'lib'));
});

// Copy php to our build folder
gulp.task('php', function() {
  return gulp.src(path.PHP)
  .pipe(gulp.dest(path.BUILD));
});

// Optimizing CSS and JavaScript 
gulp.task('useref', function(){
  // var assets = useref.assets();

  return gulp.src('src/index.php')
    // .pipe(assets)
    // Minifies only if it's a CSS file
    .pipe(gulpIf(['src/css/*.css', 'src/css/**/*.css'], minifyCSS()))
    // Uglifies only if it's a Javascript file
    .pipe(gulpIf(['src/js/*.js', 'src/js/**/*.js'], uglify()))
    // .pipe(assets.restore())
    .pipe(useref())
    .pipe(gulp.dest(path.BUILD))
});


// Build Tasks
// -----------

// Default: Clean and then update CSS + JS
gulp.task('default', ['clean'], function() {
    gulp.start('watch');
});

// Build: Clean and then prepare assets for uploading to server
gulp.task('build', ['clean'], function() {
    runSequence( ['sass', 'fonts', 'images', 'lib', 'php', 'useref'] );
});