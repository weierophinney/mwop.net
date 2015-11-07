/*jslint node: true */
/*jshint strict:false */
'use strict';

module.exports = function (grunt) {

  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    cssmin: {
      dist: {
        files: {
          'public/css/site.min.css': [
            'public/css/skeleton/base.css',
            'public/css/skeleton/skeleton.css',
            'public/css/skeleton/layout.css',
            'public/css/site.css'
          ],
          'public/css/blog.min.css': [
            'public/css/blog.css',
            'public/css/prism.css'
          ],
          'public/css/fonts.min.css': [
            'public/css/fonts.css'
          ]
        }
      }
    }
  });

  grunt.registerTask('default', [
    'cssmin'
  ]);
};
