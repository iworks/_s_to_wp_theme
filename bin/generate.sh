#!/bin/sh
if [ $# -ne 2 ]; then
    echo "Usage: $0 name slug"
    exit 1
fi

SLUG=$2

THEME=${PWD}/${SLUG}
ASSETS=${PWD}/${SLUG}/assets
#
# get "_s" theme
#
curl https://underscores.me/ -d "underscoresme_name=$1&underscoresme_slug=${SLUG}&underscoresme_sass=1&underscoresme_generate=1" -o ${SLUG}.zip
unzip ${SLUG}.zip
rm ${SLUG}.zip
rm ${THEME}/composer.json
#
# SASS
#
mkdir -p ${ASSETS}/sass/
mv ${THEME}/sass ${ASSETS}/sass/frontend
#
# script
#
mkdir -p ${ASSETS}/scripts/frontend
mv ${THEME}/js/navigation.js ${ASSETS}/scripts/frontend/
mkdir -p ${ASSETS}/scripts/admin
mv ${THEME}/js/customizer.js ${THEME}/assets/scripts/admin
rm -rf ${THEME}/js
#
# inc
#
mv ${THEME}/inc ${THEME}/includes
#
# package.json
#
cat << EOF >> package.json
{
    "name": "${SLUG}",
    "title": "${SLUG}",
    "uri": "",
    "description": "",
    "version": "1.0.0",
    "repository": {
        "type": "git",
        "url": ""
    },
    "author": "",
    "author_uri": "",
    "contributors": [
        {
            "name": "Marcin Pietrzak",
            "email": "marcin@iworks.pl"
        }
    ],
    "license": "GPL-2.0",
    "devDependencies": {
        "grunt": "^0.4.5",
        "grunt-autoprefixer": "^0.7.2",
        "grunt-contrib-clean": "^0.5.0",
        "grunt-contrib-compass": "^0.6.0",
        "grunt-contrib-compress": "^0.5.2",
        "grunt-contrib-concat": "^0.1.2",
        "grunt-contrib-copy": "^0.4.1",
        "grunt-contrib-cssmin": "^0.6.0",
        "grunt-contrib-jshint": "^0.10.0",
        "grunt-contrib-nodeunit": "^1.0.0",
        "grunt-contrib-sass": "^0.4.0",
        "grunt-contrib-uglify": "^4.0.1",
        "grunt-contrib-watch": "^0.2.0",
        "grunt-eslint": "^23",
        "grunt-exec": "^0.4.6",
        "grunt-phpunit": "^0.3.5",
        "grunt-wp-i18n": "^1.0.0",
        "time-grunt": "^0.2.3",
        "grunt-clear": "^0.2.1",
        "load-grunt-tasks": "^0.2.0",
        "grunt-git": "^0.3.5",
        "grunt-replace": "^0.11.0",
        "grunt-phplint": "^0.0.8",
        "grunt-phpcs": "^0.4.0",
        "grunt-jsvalidate": "^0.2.2",
        "grunt-concat-css": "^0.3",
        "grunt-po2mo": "^0",
        "eslint": "^4.19.1",
        "eslint-config-wordpress": "^2.0.0",
        "prettier": "^1.12.1",
        "prettier-eslint": "^8.8.1",
        "prettier-stylelint": "^0.4.2",
        "stylelint": "^9.1.3",
        "stylelint-config-wordpress": "^13.0.0",
        "stylelint-order": "^0.8.1"
    },
    "browserslist": [
        "> 1%",
        "ie >= 11",
        "last 1 Android versions",
        "last 1 ChromeAndroid versions",
        "last 2 Chrome versions",
        "last 2 Firefox versions",
        "last 2 Safari versions",
        "last 2 iOS versions",
        "last 2 Edge versions",
        "last 2 Opera versions"
    ],
    "dependencies": {
        "postcss": "^7.0.26",
        "postcss-scss": "^2.0.0"
    }
}
EOF
#
# Gruntfile.js
#
cat << EOF >> Gruntfile.js
/*global require*/

/**
 * When grunt command does not execute try these steps:
 *
 * - delete folder 'node_modules' and run command in console:
 *   $ npm install
 *
 * - Run test-command in console, to find syntax errors in script:
 *   $ grunt hello
 */

module.exports = function(grunt) {

    // Show elapsed time at the end.
    require('time-grunt')(grunt);

    // Load all grunt tasks.
    require('load-grunt-tasks')(grunt);

    var buildtime = new Date().toISOString();

    var conf = {
        buildtime: buildtime,

        // Concatenate those JS files into a single file (target: [source, source, ...]).
        js_files_concat: {
            "assets/scripts/customizer.js": [
                "assets/scripts/src/frontend/customizer.js",
            ],
            "assets/scripts/frontend.js": [
                "assets/scripts/src/frontend/common.js",
                "assets/scripts/src/frontend/navigation.js",
                "assets/scripts/src/frontend/cookie-notice-front.js",
                "assets/scripts/src/frontend/fonts.js",
            ],
        },

        // SASS files to process. Resulting CSS files will be minified as well.
        css_files_compile: {
            "assets/css/frontend/settings.css": "assets/sass/frontend/settings.scss",
            "assets/css/frontend/_s.css": "assets/sass/frontend/_s/style.scss",
            "assets/css/frontend/font-family.css": "assets/sass/frontend/font-family.scss",
            "assets/css/frontend/layout.css": "assets/sass/frontend/layout.scss",
            "assets/css/frontend/content.css": "assets/sass/frontend/content.scss",
            /**
             * Last at ALL!
             */
            "assets/css/frontend/print.css": "assets/sass/frontend/print.scss",
        },

        // BUILD patterns to exclude code for specific builds.
        replaces: {
            patterns: [{
                match: /THEME_VERSION/g,
                replace: "<%= pkg.version %>"
            }, {
                match: /BUILDTIME/g,
                replace: buildtime
            }, ],

            // Files to apply above patterns to (not only php files).
            files: {
                expand: true,
                src: [
                    "**/*.php",
                    "**/*.css",
                    "**/*.js",
                    "**/*.html",
                    "**/*.txt",
                    "!node_modules/**",
                    "!lib/**",
                    "!docs/**",
                    "!release/**",
                    "!Gruntfile.js",
                    "!build/**",
                    "!tests/**",
                    "!.git/**",
                    "!vendor/**",
                ],
                dest: "./release/<%= pkg.name %>/",
            },
        },

        // Regex patterns to exclude from transation.
        translation: {
            ignore_files: [
                "node_modules/.*",
                "(^.php)", // Ignore non-php files.
                "inc/external/.*", // External libraries.
                "release/.*", // Temp release files.
                "tests/.*", // Unit testing.
            ],
            pot_dir: "languages/", // With trailing slash.
            textdomain: "<%= pkg.name %>",
        },

        dir: "<%= pkg.name %>/",
    };

    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // JS - Concat .js source files into a single .js file.
        concat: {
            options: {
                stripBanners: true,
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
                    ' * Licensed GPLv2+\n' +
                    ' */\n'
            },
            scripts: {
                files: conf.js_files_concat
            }
        },

        // JS - Validate .js source code.
        jshint: {
            all: ['Gruntfile.js', 'assets/scripts/src/**/*.js'],
            options: {
                curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                undef: true,
                boss: true,
                eqnull: true,
                globals: {
                    exports: true,
                    module: false
                }
            }
        },

        // JS - Uglyfies the source code of .js files (to make files smaller).
        uglify: {
            my_target: {
                files: [{
                    expand: true,
                    src: [
                        'assets/scripts/*.js',
                        '!assets/scripts/*.min.js'
                    ],
                    dest: '.',
                    cwd: '.',
                    rename: function(dst, src) {

                        // To keep the source js files and make new files as '*.min.js':
                        return dst + '/' + src.replace('.js', '.min.js');

                        // Or to override to src:
                        return src;
                    }
                }]
            },
            options: {
                banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
                    ' * Licensed GPLv2+\n' +
                    ' */\n',
                mangle: {
                    reserved: ['jQuery']
                }
            }
        },

        // CSS - Compile a .scss file into a normal .css file.
        sass: {
            all: {
                options: {
                    'sourcemap=auto': true, // 'sourcemap': 'none' does not work...
                    unixNewlines: true,
                    style: 'expanded'
                },
                files: conf.css_files_compile
            }
        },

        // CSS - Automaticaly create prefixed attributes in css file if needed.
        // e.g. add '-webkit-border-radius' if 'border-radius' is used.
        autoprefixer: {
            options: {
                browsers: ['last 2 version', 'ie 8', 'ie 9', 'ie 10', 'ie 11'],
                diff: false
            },
            single_file: {
                files: [{
                    expand: true,
                    src: ['**/*.css', '!**/*.min.css'],
                    cwd: 'assets/css/',
                    dest: 'assets/css/',
                    ext: '.css',
                    extDot: 'last',
                    flatten: false
                }]
            }
        },

        concat_css: {
            options: {

                // Task-specific options go here.
            },
            all: {
                src: ['assets/css/frontend/layout.css', 'assets/css/frontend/*.css'],
                dest: 'assets/css/style.css'
            }
        },

        // CSS - Required for CSS-autoprefixer and maybe some SCSS function.
        compass: {
            options: {},
            server: {
                options: {
                    debugInfo: true
                }
            }
        },

        // CSS - Minify all .css files.
        cssmin: {
            options: {
                banner: '/*!\n' +
                    'Theme Name: <%= pkg.title %>\n' +
                    'Theme URI: <%= pkg.uri %>\n' +
                    'Author: <%= pkg.author %>\n' +
                    'Author URI: <%= pkg.author_uri %>\n' +
                    'Description: <%= pkg.description %>\n' +
                    'Version: <%= pkg.version %>.<%= new Date().getTime() %>\n' +
                    'License: GNU General Public License v2 or later\n' +
                    'Text Domain: ' + conf.translation.textdomain + '\n' +
                    '\n' +
                    ' */\n'
            },
            minify: {
                expand: true,
                src: 'style.css',
                cwd: 'assets/css/',
                dest: '',
                ext: '.css',
                extDot: 'last'
            }
        },

        // WATCH - Watch filesystem for changes during development.
        watch: {
            sass: {
                files: ['assets/sass/**/*.scss'],
                tasks: ['css'],
                options: {
                    debounceDelay: 500
                }
            },

            scripts: {
                files: [
                    'assets/scripts/src/**/*.js',
                    'assets/scripts/vendor/**/*.js'
                ],

                //tasks: ['jshint', 'concat', 'uglify' ],
                tasks: ['js'],
                options: {
                    debounceDelay: 500
                }
            }
        },

        // BUILD - Create a zip-version of the plugin.
        compress: {
            target: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>.zip'
                },
                expand: true,
                cwd: './release/<%= pkg.name %>/',
                src: ['**/*']
            }
        },

        // BUILD - update the translation index .po file.
        makepot: {
            target: {
                options: {
                    cwd: '',
                    domainPath: conf.translation.pot_dir,
                    exclude: conf.translation.ignore_files,
                    mainFile: 'style.css',
                    potComments: '',
                    potFilename: conf.translation.textdomain + '.pot',
                    potHeaders: {
                        poedit: true, // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },
                    processPot: null, // A callback function for manipulating the POT file.
                    type: 'wp-theme', // wp-plugin or wp-theme
                    updateTimestamp: true, // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: true // Whether to update PO files in the same directory as the POT file.
                }
            }
        },

        po2mo: {
            files: {
                src: 'languages/pl_PL.po',
                dest: 'languages/pl_PL.mo'
            },
            options: {
                checkDomain: true
            }
        },

        // BUILD: Replace conditional tags in code.
        replace: {
            target: {
                options: {
                    patterns: conf.replaces.patterns
                },
                files: [conf.replaces.files]
            }
        },

        clean: {
            options: {
                force: true
            },
            release: {
                options: {
                    force: true
                },
                src: [
                    './assets/css/**css',
                    './assets/css/**map',
                    './assets/css/admin/**css',
                    './assets/css/admin/**map',
                    './release',
                    './release/*',
                    './release/**'
                ]
            }
        },

        copy: {
            release: {
                expand: true,
                src: [
                    '*',
                    '**',
                    '!composer.json',
                    '!node_modules',
                    '!node_modules/*',
                    '!node_modules/**',
                    '!bitbucket-pipelines.yml',
                    '!.idea', // PHPStorm settings
                    '!.git',
                    '!Gruntfile.js',
                    '!package.json',
                    '!package-lock.json',
                    '!tests/*',
                    '!tests/**',
                    '!assets/scripts/src',
                    '!assets/scripts/src/*',
                    '!assets/scripts/src/**',
                    '!assets/css',
                    '!assets/css/*',
                    '!assets/css/**',
                    '!assets/sass',
                    '!assets/sass/*',
                    '!assets/sass/**',
                    '!phpcs.xml.dist',
                    '!README.md',
                    '!stylelint.config.js',
                    '!vendor',
                    '!vendor/*',
                    '!vendor/**'
                ],
                dest: './release/<%= pkg.name %>/',
                noEmpty: true
            }
        },

        eslint: {
            target: conf.js_files_concat['assets/scripts/frontend.js']
        },
    });

    // Test task.
    grunt.registerTask('hello', 'Test if grunt is working', function() {
        grunt.log.subhead('Hi there :)');
        grunt.log.writeln('Looks like grunt is installed!');
    });

    grunt.registerTask('release', 'Generating release copy', function() {
        grunt.task.run('clean');
        grunt.task.run('js');
        grunt.task.run('css');
        grunt.task.run('makepot');

        //        grunt.task.run( 'po2mo');
        grunt.task.run('copy');
        grunt.task.run('replace');
        grunt.task.run('compress');
    });

    // Default task.

    //grunt.registerTask( 'default', ['clean', 'jshint', 'concat', 'uglify', 'sass', 'autoprefixer', 'concat_css', 'cssmin'] );
    grunt.registerTask('default', [
        'clean',
        'sass',
        'autoprefixer',
        'concat_css',
        'cssmin'
    ]);
    grunt.registerTask('build', ['release']);
    grunt.registerTask('i18n', ['makepot', 'po2mo']);
    grunt.registerTask('js', ['eslint', 'concat', 'uglify']);
    grunt.registerTask('css', ['clean', 'sass', 'autoprefixer', 'concat_css', 'cssmin']);

    grunt.task.run('clear');
    grunt.util.linefeed = '\n';
};
EOF

