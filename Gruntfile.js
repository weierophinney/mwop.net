/*jslint node: true */
/*jshint strict:false */
'use strict';

module.exports = function (grunt) {

  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    useminPrepare: {
      options: {
        root: 'public',
        dest: '.'
      },
      'site-styles': {
        src: 'templates/layout/styles.mustache',
      },
      'site-scripts': {
        src: 'templates/layout/scripts.mustache',
      },
      'blog-styles': {
        src: 'templates/blog/styles.mustache',
      },
      'blog-scripts': {
        src: 'templates/blog/scripts.mustache',
      }
    },

    usemin: {
      html: ['build/**/*.mustache']
    }
  });

  grunt.registerTask('default', [
    'useminPrepare',
    'concat:generated',
    'cssmin:generated',
    'uglify:generated'
  ]);
};
