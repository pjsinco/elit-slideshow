module.exports = function( grunt ) {

  grunt.initConfig({

    sass: {
      dist: {
        files: {
          'css/elit-slideshow.css': 'elit-slideshow.scss'
        }
      }
    }
  }); // initConfig
  
  grunt.loadNpmTasks( 'grunt-contrib-sass' );
  grunt.registerTask( 'default', ['sass'] );

}; // exports

