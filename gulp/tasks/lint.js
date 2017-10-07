'use strict';

var path = require('path');

var lintTask = function (gulp, plugins, config) {
  gulp.task('lint-css', function() {
    return gulp.src(path.join(config.directories.css, '**/*.scss'))
      .pipe(plugins.stylelint({
        reporters: [
          {formatter: 'string', console: true}
        ]
      }));
  });
};

module.exports = lintTask;
