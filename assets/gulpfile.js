'use strict';

const concat = require('gulp-concat');
const del    = require('del');
const gulp   = require('gulp');
const minify = require('gulp-clean-css');
const rename = require('gulp-rename');
const sass   = require('gulp-sass')(require('node-sass'));
const uglify = require('gulp-uglify');

// CSS

gulp.task('css-process-sass', () => {
    return gulp.src([
        'css/bootstrap.scss',
        'css/toggle-bootstrap-print.scss',
    ])
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('build/css'));
});

gulp.task('css-global-styles', () => {
    return gulp.src([
        'build/css/bootstrap.css',
        'node_modules/prism-solarized-dark/prism-solarizeddark.css',
    ])
    .pipe(concat('styles.css'))
    .pipe(gulp.dest('build/css/'));
});

gulp.task('css-minify', () => {
    return gulp.src(
        [
            'build/css/styles.css',
            'build/css/toggle-bootstrap-print.css',
        ],
        { sourcemaps: true }
    )
    .pipe(minify())
    .pipe(rename((file) => {
        file.extname = '.min.css';
    }))
    .pipe(gulp.dest('build/css'));
});

gulp.task('css-clean', () => {
    return del(['build/css/*.*']);
});

gulp.task('css', gulp.series('css-process-sass', 'css-global-styles', 'css-minify'));

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
    .pipe(concat('site.min.js'))
    .pipe(uglify())
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
