const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');


function compilarSass() {
    return gulp
        .src('styles/main.scss')
        .pipe(sass({
            outputStyle: 'compressed'
        }))
        .pipe(autoprefixer({
            Browserslist: ['last 2 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('assets/css'))
        .pipe(browserSync.stream());
}

gulp.task('sass', compilarSass);


function gulpJS() {
    return gulp.src([
            'node_modules/uikit/dist/js/uikit-core.min.js',
            'node_modules/uikit/dist/js/uikit.min.js',
            'scripts/icons.js',
            'scripts/owl.carousel.min.js',
            'scripts/owlScript.js',
            'scripts/gMap.js'
        ])

        .pipe(concat('main.js'))
        .pipe(uglify('main.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe(browserSync.stream());
}

gulp.task('mainjs', gulpJS);



function watchproject() {
    gulp.watch('styles/*.scss', compilarSass);
    gulp.watch('scripts/*.js', gulpJS);
}

gulp.task('watch', watchproject);

gulp.task('default', gulp.parallel('watch', 'sass', 'mainjs'));