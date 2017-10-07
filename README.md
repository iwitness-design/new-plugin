# NewPlugin
A brand new plugin that you are building.

## Setup
Alright! You are creating a new plugin and that is fantastic! There are a few things that you need to do right off the bat to get things started correctly.

### Setup Composer
First, update the psr-4 autoload setting from `NewPlugin` to whatever you want to use as a namespace for this new plugin. Keep it short but unique. Use [CamelCase](https://en.wikipedia.org/wiki/Camel_case).

Now, navigate to the plugin directory in your terminal and run `composer install`. This should setup composer and install and setup Grunt.


### Update package.json
`package.json` is the file where we will store all of our plugin specific meta. This is where you will update the plugin name, description, version number, and more. Check out that file and update all of the relevant information.

### Update main file
* Update the main plugin file name
* Update the textdomain
* Update the class name and function

## Gulp
Gulp is setup to run and each task has it's own file in the `gulp/tasks` directory.

Run `gulp watch` to watch and process styles and scripts.