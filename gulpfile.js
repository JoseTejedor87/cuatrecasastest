// Initialize modules
// Importing specific gulp API functions lets us write them below as series() instead of gulp.series()
const { src, dest, watch, series, parallel } = require('gulp');
// Importing all the Gulp-related packages we want to use
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
var replace = require('gulp-replace');

// var processorsArray = [
// require('autoprefixer')({ grid: true, browsers: ['>1%'] })
// ];

// File paths
const files = {
  // ############# OLD
  scssPath: 'templates/web/src/scss/**/*.scss',

  // ############# NEW
  // scssPath: 'templates/web/src/stylesheets/main.scss',
  jsPath: 'templates/web/src/js/**/*.js'
}

// Sass task: compiles the style.scss file into style.css
function scssTask() {
  return src(files.scssPath)
         // ############# OLD
        .pipe(concat('styles.css'))

        // ############# NEW
        // .pipe(concat('main.css'))

        .pipe(sourcemaps.init()) // initialize sourcemaps first
        .pipe(sass()) // compile SCSS to CSS
        .pipe(postcss([autoprefixer(), cssnano()])) // PostCSS plugins
        .pipe(sourcemaps.write('.')) // write sourcemaps file in current directory
        .pipe(dest('public/web/assets/css')); // put final CSS in dist folder
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
// then watch task
exports.default = series(
  parallel(scssTask, jsTask),
  watchTask
);
