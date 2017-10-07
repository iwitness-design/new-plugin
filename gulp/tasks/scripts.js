'use strict';

var path = require('path');

var scriptsTask = function (gulp, plugins, config, helpers) {
  function buildScript(watch) {
    var props = {
      entries: path.join(config.directories.js, 'custom.js'),
      debug: true
    };

    var bundler = watch ? plugins.watchify(plugins.browserify(props)) : plugins.browserify(props);

    function rebundle() {
      var stream = bundler.bundle();

      if(watch) {
        return stream
          .on('error', helpers.onError)
          .pipe(plugins.vinylSourceStream('custom.min.js'))
          .pipe(plugins.vinylBuffer())
          .pipe(plugins.sourcemaps.init({loadMaps: true}))
          .pipe(plugins.uglify())
          .pipe(plugins.sourcemaps.write('./'))
          .pipe(gulp.dest(config.directories.js));
      }

      return stream
        .pipe(plugins.vinylSourceStream('custom.min.js'))
        .pipe(plugins.vinylBuffer())
        .pipe(plugins.sourcemaps.init({loadMaps: true}))
        .pipe(plugins.uglify())
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(config.directories.js));
    }

    bundler.on('update', rebundle);
    return rebundle();
  }

  gulp.task('scripts', function() { buildScript(false); });
  gulp.task('scripts-watch', function() { buildScript(true); });
};

module.exports = scriptsTask;
