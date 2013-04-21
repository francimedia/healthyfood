/*global module:false*/
module.exports = function (grunt) {
    var baseDir = '../public/assets/';
    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg:grunt.file.readJSON('package.json'),
        files:{
            'srcCSS': baseDir + 'css/base.less',
            'destCSS': baseDir + 'app/app.css',
            'srcJS':[
                baseDir + 'js/lib/mapbox.js',
                baseDir + 'js/lib/jquery-1.9.1.min.js',
                baseDir + 'js/lib/jquery.hammer.min.js',
                baseDir + 'js/lib/lodash.js',
                baseDir + 'js/lib/backbone.js',
                baseDir + 'js/lib/backbone.marionette.js',
                baseDir + 'js/swipe.js',
                baseDir + 'js/app.js'
                ],
            'destJS':baseDir + 'app/app.js'
        },
        concat:{
            distJS:{
                src:'<%= files.srcJS %>',
                dest:'<%= files.destJS %>'
            },
            distCSS:{
                'src':['<%= files.srcCSS %>', '<%= files.destCSS %>'],
                'dest':'<%= files.destCSS %>'
            },
            libCSS:{
                src:baseDir + 'css/lib/*.css',
                dest:baseDir + 'css/_libs.css'
            }
        },
        less:{
            prod:{
                options:{
                    yuicompress: false
                },
                files:{
                    '<%= files.destCSS %>':'<%= files.srcCSS %>'
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
                tasks:['compile']
            }
        }
    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

    grunt.registerTask('compile', ['less', 'concat:distJS']);

    grunt.registerTask('default', ['compile', 'watch:dev']);
};