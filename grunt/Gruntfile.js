/*global module:false*/
module.exports = function (grunt) {
    var baseDir = '../public/assets/';
    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg:grunt.file.readJSON('package.json'),
        files:{
            'srcCSS':[baseDir + 'css/global.less'],
            'destCSS':[baseDir + 'app/app.css', baseDir + 'css/_custom.css'],

            'srcJS':[
                baseDir + 'js/lib/mapbox.js',
                baseDir + 'js/lib/jquery-1.9.1.min.js',
                baseDir + 'js/lib/jquery.hammer.min.js',
                baseDir + 'js/swipe.js'],
            'destJS':baseDir + 'app/app.js'
        },
        concat:{
            distJS:{
                src:'<%= files.srcJS %>',
                dest:'<%= files.destJS %>'
            },
            distCSS:{
                'src':['<%= files.srcCSS[0] %>', '<%= files.destCSS[1] %>'],
                'dest':'<%= files.destCSS[0] %>'
            },
            libCSS:{
                src:baseDir + 'css/lib/*.css',
                dest:baseDir + 'css/_libs.css'
            }
        },
        less:{
            prod:{
                options:{
                    yuicompress:true
                },
                files:{
                    '<%= files.destCSS[1] %>':'<%= files.srcCSS %>'
                }
            }
        },
        uglify:{
            dist:{
                src:'<%= concat.dist.dest %>',
                dest:'dist/<%= pkg.name %>.min.js'
            }
        },
        watch:{
            dev:{
                files:['<%= files.srcCSS %>', '<%= files.srcJS %>'],
                tasks:['compileCSS', 'concat:distJS']
            }
        }
    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

    grunt.registerTask('compileCSS', ['concat:libCSS', 'less', 'concat:distCSS']);
    grunt.registerTask('compile', ['compileCSS', 'concat:distJS']);

    grunt.registerTask('default', ['compile', 'watch:dev']);
};