# Thero Custom
A brand new plugin for Thero.

## Setup
Alright! You are creating a new plugin and that is fantastic! There are a few things that you need to do right off the bat to get things started correctly.

### Setup Composer
Now, navigate to the plugin directory in your terminal and run `composer install`. This should setup composer and install and setup Gulp.

Composer is setup to use autoloading. Any file placed in the `includes` folder that uses the [psr 4](http://www.php-fig.org/psr/psr-4/) format will be loaded automatically when called.


### Update package.json
`package.json` is the file where we will store all of our plugin specific meta. This is where you will update the plugin name, description, version number, and more. Check out that file and update all of the relevant information.

### Update main file
* Update the main plugin file name
* Update the textdomain
* Update the class name and function

## Gulp
Gulp is setup to run and each task has it's own file in the `gulp/tasks` directory.

Run `gulp watch` to watch and process styles and scripts.