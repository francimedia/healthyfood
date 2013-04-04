/*global module:false*/
module.exports = function (grunt) {
    var baseDir = '../public/assets/';
    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg:grunt.file.readJSON('package.json'),
        files:{
            'srcCSS':[baseDir + 'css/_libs.css', baseDir + 'css/base.less', baseDir + 'css/*.less'],
            'destCSS':[baseDir + 'app/app.css', baseDir + 'css/_custom.css'],

            'srcJS':[
                baseDir + 'js/lib/quo.debug.js',
                baseDir + 'js/lib/lungo.js',
                baseDir + 'js/lib/moment.min.js',
                baseDir + 'js/lib/mapbox.js',
                baseDir + 'js/lib/morpheus.min.js',
                baseDir + 'js/*.js'],
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
            dev:{
                files:{
                    '<%= files.destCSS[1] %>':'<%= files.srcCSS[1] %>'
                }
            },
            prod:{
                options:{
                    yuicompress:true
                },
                files:{
                    '<%= files.destCSS[1] %>':'<%= files.srcCSS[1] %>'
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
                files:['<%= files.srcCSS[2] %>', '<%= files.srcJS %>'],
                tasks:['compileCSS', 'concat:distJS']
            }
        }
    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

    grunt.registerTask('compileCSS', ['concat:libCSS', 'less:dev', 'concat:distCSS']);
    grunt.registerTask('compile', ['compileCSS', 'concat:distJS']);

    grunt.registerTask('default', ['compile', 'watch:dev']);
};