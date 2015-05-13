module.exports = function(grunt) {

  grunt.initConfig({

    autoprefixer: {
      css: {
        src: 'css/**/*.css'
      }
    },

    sass: {
      dev: {
        files: {
          'css/elit-slideshow.css': 'scss/elit-slideshow.scss'
        }
      }
    },

    watch: {
      styles: {
        files: ['scss/**/*.scss'],
        tasks: ['sass:dev', 'autoprefixer:css'] 
      }
    } // watch
  }); // initConfig
  
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks("grunt-autoprefixer");
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.registerTask( 'default', ['watch'] );

}; // exports

