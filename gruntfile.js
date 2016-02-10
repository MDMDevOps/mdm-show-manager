module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-newer');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.initConfig({
        // Reference package.json
        pkg: grunt.file.readJSON('package.json'),

        // Compile SCSS with the Compass Compiler
        compass: {
            dist: {
                options: {
                    sassDir     : 'src/scss',
                    cssDir      : 'assets/styles',
                    outputStyle : 'compressed',
                    cacheDir    : 'src/scss/.sass-cache',
                    sourcemap   : true
                }
            }
        },

        // Run Autoprefixer on compiled css
        autoprefixer: {
            options: {
                browsers: ['last 3 version', '> 1%', 'ie 8', 'ie 9', 'ie 10'],
                map: true
            },
            admin : {
                src  : 'assets/styles/admin.css',
                dest : 'assets/styles/admin.css'
            },
            public : {
                src  : 'assets/styles/public.css',
                dest : 'assets/styles/public.css'
            },
        },

        // JSHint - Check Javascript for errors
        jshint: {
            options: {
                globals: {
                  jQuery: true
                }
            },
            admin : {
                src: [ 'src/js/admin.js' ],
            },
            public : {
                src: [ 'src/js/public.js' ],
            }
        },

        // Concat & Minify JS
        uglify: {
            options: {
              sourceMap : true
            },
            admin : {
                files : {
                    'assets/scripts/admin.js' : [ 'src/js/libs/moment.js', 'src/js/libs/select2.min.js', 'src/js/admin.js' ]
                }
            },
            public : {
                files : {
                    'assets/scripts/public.js' : [ 'src/js/public.js' ]
                }
            }
        },

        // Watch
        watch: {
            compass: {
                files: 'src/scss/**/*.scss',
                tasks: ['compass', 'newer:autoprefixer'],
            },
            js_post_process_admin: {
                files: [ 'src/js/admin.js' ],
                tasks: ['jshint:admin', 'uglify:admin'],
            },
            js_post_process_public: {
                files: [ 'src/js/public.js' ],
                tasks: ['jshint:public', 'uglify:public'],
            },
            livereload: {
                options: { livereload: true },
                files: ['assets/css/*.css', 'assets/js/*.js', '*.html', 'images/*', '*.php'],
            },
        },
    });
    grunt.registerTask('default', ['watch']);
};