'use strict';

const concat     = require('gulp-concat');
const del        = require('del');
const gulp       = require('gulp');
const minify     = require('gulp-clean-css');
const rename     = require('gulp-rename');
const sass       = require('gulp-sass')(require('node-sass'));
const sourcemaps = require('gulp-sourcemaps');
const uglify     = require('gulp-uglify');

// CSS

gulp.task('css-screen', () => {
    return gulp.src('css/bootstrap.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(minify())
        .pipe(rename((file) => {
            file.basename = 'styles';
            file.extname = '.min.css';
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('build/css'));
});

gulp.task('css-fa-fonts', () => {
    return gulp.src('node_modules/@fortawesome/fontawesome-free/webfonts/*.{ttf,woff2}')
        .pipe(gulp.dest('build/css/webfonts'));
});

gulp.task('css-print', () => {
    return gulp.src('css/toggle-bootstrap-print.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(minify())
        .pipe(rename((file) => {
            file.basename = 'print';
            file.extname = '.min.css';
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('build/css'));
});

gulp.task('css-clean', () => {
    return del(['build/css/*.*']);
});

gulp.task('css', gulp.series('css-screen', 'css-fa-fonts'));

// JS

gulp.task('js-global', () => {
    return gulp.src(
            [
            'node_modules/jquery/dist/jquery.js',
            'node_modules/autocomplete.js/dist/autocomplete.jquery.js',
            'node_modules/bootstrap/dist/js/bootstrap.js',
            'js/search.js'
            ],
            { sourcemaps: true }
        )
        .pipe(sourcemaps.init())
        .pipe(concat('site.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('build/js'));
});

gulp.task('js-extra', () => {
    return gulp.src([
        'js/disqus.js',
        'js/prism.js',
        'js/twitter.js',
    ])
    .pipe(gulp.dest('build/js'));
});

gulp.task('js-clean', () => {
    return del(['build/js/*.*']);
});

gulp.task('js', gulp.series('js-global', 'js-extra'));

// Clean

gulp.task('clean', gulp.series('css-clean', 'js-clean'));

// All

gulp.task('default', gulp.series('css', 'js'));
