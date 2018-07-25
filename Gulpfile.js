var gulp = require('gulp');
var sass = require('gulp-sass');
var run = require('gulp-run');
var notify = require('gulp-notify');
var autoprefixer = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var log = require('fancy-log'); 
var watch = require('gulp-watch'); 
var sourcemaps = require('gulp-sourcemaps');
var plumber = require('gulp-plumber');
var imagemin = require('gulp-imagemin');
var htmlmin = require('gulp-htmlmin'); 
var inject = require('gulp-inject'); 
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

// For Favicons
var realFavicon = require ('gulp-real-favicon');
var fs = require('fs');


// **************** FAVICONS ***************
// File where the favicon markups are stored
var FAVICON_DATA_FILE = 'faviconData.json';

// Generate the icons. This task takes a few seconds to complete.
// You should run it at least once to create the icons. Then,
// you should run it whenever RealFaviconGenerator updates its
// package (see the check-for-favicon-update task below).
gulp.task('generate-favicon', function(done) {
	realFavicon.generateFavicon({
		masterPicture: './src/img/srcs/icons.png',
		dest: './htdocs/',
		iconsPath: '/',
		design: {
			ios: {
				pictureAspect: 'backgroundAndMargin',
				backgroundColor: '#132d40',
				margin: '0%',
				assets: {
					ios6AndPriorIcons: false,
					ios7AndLaterIcons: false,
					precomposedIcons: true,
					declareOnlyDefaultIcon: true
				}
			},
			desktopBrowser: {},
			windows: {
				pictureAspect: 'noChange',
				backgroundColor: '#132d40',
				onConflict: 'override',
				assets: {
					windows80Ie10Tile: true,
					windows10Ie11EdgeTiles: {
						small: false,
						medium: true,
						big: false,
						rectangle: false
					}
				}
			},
			androidChrome: {
				pictureAspect: 'backgroundAndMargin',
				margin: '0%',
				backgroundColor: '#132d40',
				themeColor: '#132d40',
				manifest: {
					name: 'EnterYourDreams',
					startUrl: 'http://www.enteryoudreams.com',
					display: 'standalone',
					orientation: 'notSet',
					onConflict: 'override',
					declared: true
				},
				assets: {
					legacyIcon: false,
					lowResolutionIcons: false
				}
			},
			safariPinnedTab: {
				pictureAspect: 'blackAndWhite',
				threshold: 82.8125,
				themeColor: '#132d40'
			}
		},
		settings: {
			compression: 3,
			scalingAlgorithm: 'Mitchell',
			errorOnImageTooSmall: false,
			readmeFile: false,
			htmlCodeFile: false,
			usePathAsIs: false
		},
		markupFile: FAVICON_DATA_FILE
	}, function() {
		done();
	});
});

// Inject the favicon markups in your HTML pages. You should run
// this task whenever you modify a page. You can keep this task
// as is or refactor your existing HTML pipeline.
gulp.task('inject-favicon-markups', function() {
	return gulp.src([ './src/html/shared/header-index.html','./src/html/shared/header-product.html' ])
		.pipe(realFavicon.injectFaviconMarkups(JSON.parse(fs.readFileSync(FAVICON_DATA_FILE)).favicon.html_code))
		.pipe(gulp.dest('./htdocs/'));
});

// Check for updates on RealFaviconGenerator (think: Apple has just
// released a new Touch icon along with the latest version of iOS).
// Run this task from time to time. Ideally, make it part of your
// continuous integration system.
gulp.task('check-for-favicon-update', function(done) {
	var currentVersion = JSON.parse(fs.readFileSync(FAVICON_DATA_FILE)).version;
	realFavicon.checkForUpdates(currentVersion, function(err) {
		if (err) {
			throw err;
		}
	});
});
// **************** FAVICONS ***************



// Lozad.js
gulp.task('lozad', function(cb) { 
  return gulp.src('src/js/plugins/lozad.js')
        .pipe(concat('lozad.min.js'))
        .pipe(gulp.dest('htdocs/js'))
        .pipe(uglify())
        .pipe(gulp.dest('htdocs/js'))
        .on('end', function(){ log('JS Done!'); })
}); 

// JS
gulp.task('js', function(cb) { 
  return gulp.src([
            'src/js/admin/*'])
        .pipe(concat('enteryourdreams.min.js'))
        .pipe(gulp.dest('htdocs/js'))
        .pipe(uglify())
        .pipe(gulp.dest('htdocs/js'))
        .on('end', function(){ log('JS Done!'); })
}); 


// Polyfills (see footer.html)
gulp.task('poly_js', function(cb) { 
  return gulp.src(['src/js/polyfills/*'])
        .pipe(concat('pollyfills.min.js'))
        .pipe(gulp.dest('htdocs/js'))
        .pipe(uglify())
        .pipe(gulp.dest('htdocs/js'))
        .on('end', function(){ log('JS POLYFILLS Done!'); })
}); 


// CSS
gulp.task('css', function() {
    return gulp.src([
		 	 './src/scss/enteryourdreams.scss'])
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(autoprefixer())
        .pipe(sass({ style: 'ulgligfy' }))
        .on('end', function(){ log('Almost there...'); })
        .pipe(minifycss())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./htdocs/css'))
        .on('end', function(){ log('CSS Done!'); });
});


// IMAGE MING
gulp.task('img', function() {
    return gulp.src([
        'src/img/**/*.png',
        'src/img/**/*.jpg',
        'src/img/**/*.svg',
        'src/img/**/*.gif',
        '!src/img/srcs/**/*',])
        .pipe(imagemin( 
          imagemin.jpegtran({progressive: true}),
          imagemin.svgo({
            plugins: [
              {removeViewBox: false},
              {cleanupIDs: true}
            ]
          })))
        .pipe(gulp.dest('./htdocs/img')) 
        .on('end', function(){ log('IMAGES Done!'); });
});

 
// WATCH!
gulp.task('watch', function () {
    gulp.watch('./src/scss/**/*.scss', ['css']);
    gulp.watch('./src/img/**/*', ['img']);
    gulp.watch(['./src/js/framework/*.js','./src/js/plugins/*.js','./src/js/ux/*.js'], ['js']);
});

gulp.task('default', ['css','img','js','watch']); // html