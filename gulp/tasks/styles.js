'use strict';

var path = require('path');

var stylesTask = function (gulp, plugins, config, helpers) {

  gulp.task('styles', ['lint-css'], function() {
    return gulp.src(path.join(config.directories.sass, '*.scss'))
      .pipe(plugins.sourcemaps.init())
      .pipe(plugins.plumber(helpers.onError))
      .pipe(plugins.sassGlob())
      .pipe(plugins.sass({ outputStyle: 'expanded', includePaths: ['node_modules'] }))
      .pipe(plugins.sourcemaps.write('./'))
      .pipe(gulp.dest(config.directories.css));
  });

};

module.exports = stylesTask;
