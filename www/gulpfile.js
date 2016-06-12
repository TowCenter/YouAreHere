/*!
 * https://packagecontrol.io/packages/SCSS <-- drop into packages dir, restart sublime
 * gulp
 * $ gem install sass
 * $ compass init
 * $ sudo npm cache clean
 * $ sudo npm install -g n
 * $ sudo n stable
 * $ npm init
 */

 // Load plugins
var gulp = require('gulp')
	,sass = require('gulp-sass')
	,minify = require('gulp-minify-css')
	,uglify = require('gulp-uglify')
	,del = require('del')
	,useref = require('gulp-useref')
	,gulpIf = require('gulp-if')
	,runSequence = require('run-sequence')
  ,eol = require('gulp-eol');

var path = {
  JS: 'src/js/**/*.js',
  DEST_JS_SRC: 'src/js/',
  SASS: 'src/sass/**/*.scss',
  CSS: 'src/css/**/*.css',
  DEST_CSS_SRC: 'src/css/',
  FONTS: 'src/fonts/**/*',
  IMG: 'src/img/**/*',
  LIB: 'src/lib/**/*',
  TMPL: 'src/tmpl/**/*.handlebars',
  HTML: 'src/*.html',
  CONFIG: 'src/config.json',
  SRC: 'src/',
  BUILD: 'build/'
};

var staticFiles = [
  path.FONTS,
  path.IMG,
  path.LIB,
  path.TMPL,
  path.CONFIG
];

// Development Tasks 
// -----------------
gulp.task('watch', function() {
  gulp.watch(path.SASS, ['sass']); 
});

// Compile our SASS into CSS into one minified file
gulp.task('sass', function() {
  return gulp.src(path.SASS)
    .pipe(sass())
    .pipe(gulp.dest(path.DEST_CSS_SRC));
});

// Optimization + Build Tasks 
// --------------------------
// Clean up! Delete the previous build
gulp.task('clean', function() {
    return del(path.BUILD);
});

// Copy static assets over
// fonts, images, twilio lib
gulp.task('static', function() {
  gulp.src(staticFiles, { base: path.SRC })
  .pipe(gulp.dest(path.BUILD));
});

// Optimizing CSS and JavaScript 
gulp.task('useref', function(){
  return gulp.src(path.HTML)
    .pipe(eol('\r\n'))
    .pipe(useref())
    // Minifies only if it's a CSS file
    .pipe(gulpIf(path.CSS, minify()))
    // Uglifies only if it's a Javascript file
    .pipe(gulpIf(path.JS, uglify()))
    .pipe(gulp.dest(path.BUILD))
});

// Build: Clean and then prepare assets for uploading to server
gulp.task('build', ['clean', 'sass'], function() {
    // run build tasks
    runSequence( ['static', 'useref'] );
});

// Default: Clean and then update CSS + JS
gulp.task('default', ['clean'], function() {
    gulp.start('watch');
});