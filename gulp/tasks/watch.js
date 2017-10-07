'use strict';

var path = require('path');

var serveTask = function (gulp, plugins, config, helpers) {
  gulp.task('watch', ['scripts', 'styles'], function() {
    gulp.watch(path.join(config.directories.css, '**/*scss'), ['styles']);
    gulp.watch(path.join(config.directories.js, '**/*js'), ['scripts']);
  });
};

module.exports = serveTask;
