/*jslint node: true */
/*jshint strict:false */
'use strict';

const packageJson= require('./package.json');
const path = require('path');
const sass = require('node-sass');

module.exports = function (grunt) {

  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    sass: {
      dist: {
        options: {
          implementation: sass,
          sourceMap: false
        },
        files: {
          'build/css/bootstrap.css': 'css/bootstrap.scss'
        }
      }
    },

    concat: {
      js: {
        src: [
          'node_modules/jquery/dist/jquery.js',
          'node_modules/autocomplete.js/dist/autocomplete.jquery.js',
          'node_modules/bootstrap/dist/js/bootstrap.js',
          'js/search.js'
        ],
        dest: 'build/js/site.js'
      },
      css: {
        src: [
          'build/css/bootstrap.css',
          'css/prism.css',
          'css/site.css',
          'css/blog.css'
        ],
        dest: 'build/css/styles.css'
      }
    },

    cssmin: {
      options: {
        sourceMap: true
      },
      target: {
        files: {
          'build/css/styles.min.css': ['build/css/styles.css']
        }
      }
    },

    uglify: {
      options: {
        sourceMap: true
      },
      js: {
        files: {
          'build/js/site.min.js': ['build/js/site.js']
        }
      },
    },

    copy: {
      js: {
        files: [{
          src: [
            'js/disqus.js',
            'js/instagram.js',
            'js/prism.js',
            'js/twitter.js'
          ],
          dest: 'build/'
        }]
      }
    }
  });

  grunt.registerTask('default', [
    'sass',
    'concat',
    'cssmin',
    'copy:js',
    'uglify:js'
  ]);
};
