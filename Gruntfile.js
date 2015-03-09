module.exports = function( grunt ) {

  grunt.initConfig({

    sass: {
      dist: {
        files: {
          'css/elit-responsive-slides.css': 'elit-responsive-slides.scss'
        }
      }
    }
  }); // initConfig
  
  grunt.loadNpmTasks( 'grunt-contrib-sass' );
  grunt.registerTask( 'default', ['sass'] );

}; // exports

