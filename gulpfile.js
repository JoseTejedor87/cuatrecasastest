// Initialize modules
// Importing specific gulp API functions lets us write them below as series() instead of gulp.series()
const { src, dest, watch, series, parallel } = require('gulp');
// Importing all the Gulp-related packages we want to use
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

// var replace = require('gulp-replace');

// var processorsArray = [
// require('autoprefixer')({ grid: true, browsers: ['>1%'] })
// ];

// var processorsArray = [
//   require('autoprefixer')({ grid: true, browsers: ['>1%'] })
// ];


// File paths
const files = {
  scssFile: 'templates/web/src/stylesheets/main.scss',
  scssPath: 'templates/web/src/stylesheets/**/*.scss',
  jsPath: 'templates/web/src/js/**/*.js'
}

// Sass task: compiles the style.scss file into style.css
// https://github.com/HosseinKarami/fastshell/blob/master/gulpfile.js
function scssTask() {
  return src(files.scssFile)
        .pipe(concat('main.css'))
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(sourcemaps.write(''))
        .pipe(dest('public/web/assets/css'));
}

// JS task: concatenates and uglifies JS files to script.js
function jsTask() {
  return src(files.jsPath)
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(dest('public/web/assets/js'));
}

// Watch task: watch SCSS and JS files for changes
// If any change, run scss and js tasks simultaneously
function watchTask() {
  watch([files.scssPath, files.jsPath],
    series(
      parallel(scssTask, jsTask)
    )
  );
}

// Export the default Gulp task so it can be run
// Runs the scss and js tasks simultaneously
// then watch task = cmd console projectPath/gulp
exports.default = series(
  parallel(scssTask, jsTask),
  watchTask
);
