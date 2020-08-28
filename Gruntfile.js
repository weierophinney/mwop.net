/*jslint node: true */
/*jshint strict:false */
'use strict';

const packageJson= require('./package.json');
const path = require('path');
const sass = require('node-sass');
const swPrecache = require('sw-precache');

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
          'public/css/bootstrap.css': 'public/css/bootstrap.scss'
        }
      }
    },

    useminPrepare: {
      options: {
        root: 'public',
        dest: '.'
      },
      'site-styles': {
        src: 'templates/layout/styles.phtml',
      },
      'site-scripts': {
        src: 'templates/layout/scripts.phtml',
      },
      'blog-styles': {
        src: 'src/Blog/templates/blog/styles.phtml',
      },
      'blog-scripts': {
        src: 'src/Blog/templates/blog/scripts.phtml',
      }
    },

    usemin: {
      html: ['build/**/*.phtml']
    },

    swPrecache: {
      dev: {
        handleFetch: false,
        rootDir: 'public'
      },
      prod: {
        handleFetch: true,
        rootDir: 'public'
      }
    }
  });

  function writeServiceWorkerFile(rootDir, handleFetch, callback) {
    var config = {
      cacheId: packageJson.name,
      dynamicUrlToDependencies: {
      },
      handleFetch: handleFetch,
      logger: grunt.log.writeln,
      staticFileGlobs: [
        rootDir + '/offline.html',
        rootDir + '/css/blog.min.css',
        rootDir + '/css/site.min.css',
        rootDir + '/images/favicon/apple-touch-icon-57x57.png',
        rootDir + '/images/favicon/apple-touch-icon-60x60.png',
        rootDir + '/images/favicon/apple-touch-icon-72x72.png',
        rootDir + '/images/favicon/favicon-32x32.png',
        rootDir + '/images/favicon/favicon-16x16.png',
        rootDir + '/images/logo.gif',
        rootDir + '/images/mwop-coffee-dpc09.jpg',
        rootDir + '/manifest.json',
        rootDir + '/js/ga.js',
        rootDir + '/js/blog.min.js',
        rootDir + '/js/search_terms.json',
        rootDir + '/js/site.min.js',
        rootDir + '/js/twitter.js',
      ],
      runtimeCaching: [
        {
          urlPattern: /\/$/,
          handler: 'fastest'
        },
        {
          urlPattern: /\/resume$/,
          handler: 'fastest'
        },
        {
          urlPattern: /\/blog\/?$/,
          handler: 'fastest',
          options: {
            cache: {
              maxEntries: 10,
              name: 'blog-cache'
            }
          }
        },
        {
          urlPattern: '/blog/:id.html',
          handler: 'fastest',
          options: {
            cache: {
              maxEntries: 10,
              name: 'blog-cache'
            }
          }
        },
        {
          urlPattern: /\/auth(\/.*)?/,
          handler: 'networkOnly'
        },
        {
          urlPattern: '/comics',
          handler: 'networkOnly'
        },
        {
          urlPattern: /\/contact(\/.*)?/,
          handler: 'networkOnly'
        }
      ],
      stripPrefix: rootDir + '/',
      verbose: true
    };

    swPrecache.write(path.join(rootDir, 'service-worker.js'), config, callback);
  }

  grunt.registerMultiTask('swPrecache', function () {
    var done = this.async();
    var rootDir = this.data.rootDir;
    var handleFetch = this.data.handleFetch;

    writeServiceWorkerFile(rootDir, handleFetch, function (error) {
      if (error) {
        grunt.fail.warn(error);
      }
      done();
    });
  });

  grunt.registerTask('default', [
    'sass',
    'useminPrepare',
    'concat:generated',
    'cssmin:generated',
    'uglify:generated' /*,
    'swPrecache:prod' */
  ]);
};
