/* jshint node:true */
module.exports = function(grunt) {
	'use strict';

	const path = require('path');

	const basedir = path.dirname(grunt.file.findup('Gruntfile.js'));
	const theme_name = grunt.file.readJSON(path.join(basedir, 'package.json')).name;

	// this is weird, but we need to parse a PHP array in JS
	let icon_map = {};

	let icomoon     = grunt.file.read( basedir + '/vamtam/assets/fonts/icons/list.php', { encoding: 'utf8' } ).split( "\n" );
	let theme_icons = grunt.file.read( basedir + '/vamtam/assets/fonts/theme-icons/list.php', { encoding: 'utf8' } ).split( "\n" );

	icomoon.forEach( function( line ) {
		line = line.split( '=>' );

		if ( line.length > 1 ) {
			let name = line[0].trim().replace( /['"]/g, '' );
			let code = line[1].trim().replace( ',', '' );

			icon_map[ 'vamtam-icomoon-' + name ] = code.replace( '0x', '\\' );
		}
	} );

	theme_icons.forEach( function( line ) {
		line = line.split( '=>' );

		if ( line.length > 1 ) {
			let name = line[0].trim().replace( /['"]/g, '' );
			let code = line[1].trim().replace( ',', '' );

			icon_map[ 'vamtam-theme-' + name ] = code.replace( '0x', '\\' );
		}
	} );

	function getBuildHash() {
		const head = require('child_process').execSync( 'git rev-parse HEAD', {
			cwd: basedir,
		} );

		return `// build: ${head}`;
	}

	return {
		pkg: grunt.file.readJSON('package.json'),
		basedir,
		uglify: {
			options: {
				screwIE8: true,
			},
			front: {
				src: '<%= pkg.jsLocation %>all.js',
				dest: '<%= pkg.jsLocation %>all.min.js',
			},
			theme_elementor: {
				src: '<%= pkg.jsLocation %>theme-elementor.js',
				dest: '<%= pkg.jsLocation %>theme-elementor.min.js',
			},
			admin: {
				src: '<%= pkg.adminJsLocation %>admin-all.js',
				dest: '<%= pkg.adminJsLocation %>admin-all.min.js',
			},
			customizer_resppos: {
				src: '<%= pkg.customizerLocation %>assets/js/responsive-position.js',
				dest: '<%= pkg.customizerLocation %>assets/js/responsive-position.min.js',
			},
			customizer_respboxfields: {
				src: '<%= pkg.customizerLocation %>assets/js/responsive-box-fields.js',
				dest: '<%= pkg.customizerLocation %>assets/js/responsive-box-fields.min.js',
			},
			customizer_crow: {
				src: '<%= pkg.customizerLocation %>assets/js/color-row.js',
				dest: '<%= pkg.customizerLocation %>assets/js/color-row.min.js',
			},
			customizer_background: {
				src: '<%= pkg.customizerLocation %>assets/js/background.js',
				dest: '<%= pkg.customizerLocation %>assets/js/background.min.js',
			},
			customizer_multicheck: {
				src: '<%= pkg.customizerLocation %>assets/js/multicheck.js',
				dest: '<%= pkg.customizerLocation %>assets/js/multicheck.min.js',
			},
			customizer_typography: {
				src: '<%= pkg.customizerLocation %>assets/js/typography.js',
				dest: '<%= pkg.customizerLocation %>assets/js/typography.min.js',
			},
			customizer_button: {
				src: '<%= pkg.customizerLocation %>assets/js/button.js',
				dest: '<%= pkg.customizerLocation %>assets/js/button.min.js',
			},
		},
		browserify: {
			customizer_preview: {
				options: {
					transform: [
						["babelify", {
							presets: [ '@babel/env' ],
						}]
					]
				},
				files: {
					'<%= pkg.adminJsLocation %>customizer-preview.js': ['<%= pkg.adminJsLocation %>customizer/preview.js'],
				}
			},
			customize_controls_conditionals: {
				options: {
					transform: [
						["babelify", {
							presets: [ '@babel/env' ],
						}]
					]
				},
				files: {
					'<%= pkg.adminJsLocation %>customize-controls-conditionals.js': ['<%= pkg.adminJsLocation %>customizer/controls-conditionals.js'],
				}
			},
		},
		jshint: {
			files: [
				'**/*.js',
				'!**/*.min.js',
				'!documentation/**',
				'!vamtam/plugins/*/**',
				'!style_switcher/**',
				'!vendor/**',
				'!vamtam/assets/js/all.js',
				'!vamtam/assets/js/build/**',
				'!vamtam/assets/js/polyfills.js',
				'!vamtam/assets/js/plugins/thirdparty/**',
				'!vamtam/redux/extensions/advanced_customizer/**',

				// to be processed by babel
				'!vamtam/admin/assets/js/customizer/**',
				'!vamtam/customizer/lib/assets/js/**',

				// generated by babel
				'!vamtam/admin/assets/js/customize-controls-conditionals.js',
				'!vamtam/admin/assets/js/customizer-preview.js',
				'!vamtam/admin/assets/js/customizer-preview-front.js',

				'!node_modules/**',
				'!build/**',
				'!dist/**',
				'!utils/grunt/**',
			],
			options: {
				// 'curly': true,
				// 'quotmark': 'single',
				'eqeqeq': true,
				'eqnull': true,
				'esversion': 11,
				'expr': true,
				'immed': true,
				'multistr': true,
				'noarg': true,
				'strict': true,
				'trailing': true,
				'undef': true,
				'unused': true,

				'browser': true,
				'devel': true,

				'globals': {
					'_': false,
					'ajaxurl': false,
					'autosave': false,
					'Backbone': false,
					'colorValidate': false,
					'jQuery': false,
					'Modernizr': false,
					'quicktags': false,
					'RetinaImage': false,
					'RetinaImagePath': false,
					'send_to_editor': false,
					'switchEditors': false,
					'tinyMCE': false,
					'tinymce': false,
					'tinyMCEPreInit': false,
					'wp': false,
					'wpActiveEditor': true,
					'VAMTAM_ADMIN': false,
					'VAMTAM_CUSTOMIZE_PREVIEW': false,
					'VAMTAM_FRONT': false,
					'VAMTAM_HIDDEN_WIDGETS': false,
					'vamtam_yepnope': false,
					'VAMTAMED_LANG': false,
					'VamtamTmceShortcodes': false,
					'Cookies': false,
					'imagesLoaded': false,
					'IntersectionObserver': false,
					'FLBuilder': false,
					'FLBuilderStrings': false,
					'FLBuilderSettingsConfig': false,
					'Masonry': false,
				},
			}
		},
		concat: {
			options: {
				separator: '\n',
			},
			dist: {
				src: [
					'<%= pkg.jsLocation %>lib.js',

					'<%= pkg.jsLocation %>menu.js',
					'<%= pkg.jsLocation %>general.js',
					'<%= pkg.jsLocation %>woocommerce.js',
					'<%= pkg.jsLocation %>wc-gallery.js',
					'<%= pkg.jsLocation %>custom-animations.js',
				],
				dest: '<%= pkg.jsLocation %>all.js',
				nonull: true,
			},
			admin: {
				src: [
					'<%= pkg.adminJsLocation %>vamtam-admin.js',
					'<%= pkg.adminJsLocation %>vamtam-tgmpa.js',
				],
				dest: '<%= pkg.adminJsLocation %>admin-all.js',
				nonull: true,
			},
		},
		watch: {
			js: {
				files: [
					'<%= concat.dist.src %>',
					'<%= concat.admin.src %>',
					'<%= uglify.customizer_crow.src %>',
					'<%= uglify.customizer_resppos.src %>',
					'<%= uglify.customizer_respboxfields.src %>',
					'<%= uglify.customizer_button.src %>',
					'<%= uglify.customizer_background.src %>',
					'<%= uglify.customizer_multicheck.src %>',
					'<%= uglify.customizer_typography.src %>',
					'<%= uglify.theme_elementor.src %>',
					'<%= pkg.jsLocation %>src/**',
					'<%= pkg.adminJsLocation %>customizer/**',
				],
				tasks: ['buildjs'],
			},
			fonts: {
				files: [
					'<%= basedir %>/vamtam/assets/fonts/theme-icons/*.ttf',
					'<%= basedir %>/vamtam/assets/fonts/icons/*.ttf',
					'<%= basedir %>/vamtam/plugins/revslider/public/assets/fonts/revicons/*.ttf',
				],
				tasks: ['ttf2woff2'],
			},
			'less-theme': {
				files: [ '<%= basedir %>/vamtam/assets/css/**/*.less' ],
				tasks: ['less:theme'],
			},
			'less-admin': {
				files: [ '<%= basedir %>/vamtam/admin/assets/css/**/*.less' ],
				tasks: ['less:admin'],
			},
			'less-customizer': {
				files: [ '<%= pkg.customizerLocation %>assets/less/**/*.less' ],
				tasks: ['less:customizer'],
			},
			livereload: {
				// Here we watch the files the sass task will compile to
				// These files are sent to the live reload server after sass compiles to them
				options: { livereload: true },
				files: [
					'<%= basedir %>/vamtam/assets/css/dist/**/*.css',
					'<%= pkg.customizerLocation %>assets/css/**/*.css',
					'<%= basedir %>/vamtam/admin/assets/css/**/*.css',
				],
			},
		},
		ttf2woff2: {
			'icomoon': {
				src: [ '<%= basedir %>/vamtam/assets/fonts/icons/*.ttf' ],
				dest: '<%= basedir %>/vamtam/assets/fonts/icons/',
			},
			'theme-icons': {
				src: [ '<%= basedir %>/vamtam/assets/fonts/theme-icons/*.ttf' ],
				dest: '<%= basedir %>/vamtam/assets/fonts/theme-icons/',
			},
			'revslider': {
				src: [ '<%= basedir %>/vamtam/plugins/revslider/public/assets/fonts/revicons/*.ttf' ],
				dest: '<%= basedir %>/vamtam/plugins/revslider/public/assets/fonts/revicons/',
			},
		},
		compress: {
			theme: {
				options: {
					archive: path.join( 'dist', theme_name + '.zip' ),
					mode: 'zip',
					pretty: true,
					level: 9,
				},
				files: [{
					expand: true,
					src: [
						'**/*',
						'!**/vamtam/assets/fonts/*/selection.json',
						'!**/vamtam/assets/fonts/*/*.svg',
					],
					cwd: 'build/'
				}]
			}
		},
		makepot: {
			theme: {
				options: {
					domainPath: '/languages/',
					exclude: [ 'vamtam/plugins/.*', 'documentation/.*', 'build/.*' ],
					mainFile: 'style.css',
					potFilename: theme_name + '.pot',
					type: 'wp-theme',
					updateTimestamp: true,
				}
			},
			elements: {
				options: {
					cwd: `vamtam/plugins/vamtam-elementor-integration-${theme_name.replace( /^vamtam-/, '' )}`,
					domainPath: '/languages/',
					exclude: [ 'documentation/.*', 'build/.*' ],
					mainFile: 'vamtam-elementor-integration.php',
					potFilename: 'vamtam-elementor-integration.pot',
					type: 'wp-plugin',
					updateTimestamp: true,
				}
			},
			productqa: {
				options: {
					cwd: `vamtam/plugins/vamtam-product-qa`,
					domainPath: '/languages/',
					exclude: [ 'documentation/.*', 'build/.*' ],
					mainFile: 'plugin.php',
					potFilename: 'vamtam-product-qa.pot',
					type: 'wp-plugin',
					updateTimestamp: true,
				}
			}
		},
		parallel: {
			dev: {
				options: {
					stream: true,
					grunt: true,
				},
				tasks: [ 'watch:js', 'watch:fonts', 'watch:less-theme', 'watch:less-admin', 'watch:less-customizer' ],
			},
			'dev-live': {
				options: {
					stream: true,
					grunt: true,
				},
				tasks: [ 'parallel:dev', 'watch:livereload' ],
			},
			composer: {
				options: {
					stream: true
				},
				tasks: [{
					cmd: 'composer',
					args: ['install']
				}]
			},
			'fetch-wp-devel': {
				options: {
					stream: true
				},
				tasks: [{
					cmd: 'svn',
					args: ['co', 'http://develop.svn.wordpress.org/trunk/', path.join('/tmp', 'wp-devel')]
				}]
			},
		},
		less: {
			options: {
				strictMath: true,
				strictUnits: true,
				plugins: [
					new ( require('less-plugin-clean-css') )( { level: 2 } ),
					new ( require('less-plugin-autoprefix') )( { browsers: [ 'last 1 version', '>1%', 'Firefox ESR', 'not dead' ] } ),
					require('less-plugin-glob'),
				],
				customFunctions: {
					icon: function( less, icon ) {
						return '"' + ( icon_map[ icon.value ] || 'missing icon' ) + '"';
					},
				},
			},
			customizer: {
				expand: true,
				cwd: '<%= pkg.customizerLocation %>assets/less/',
				src: [
					'**/*.less',
				],
				dest: '<%= pkg.customizerLocation %>assets/css/',
				ext: '.css',
			},
			admin: {
				options: {
					paths: [ '<%= basedir %>/vamtam/admin/assets/css' ],
				},
				expand: true,
				cwd: '<%= basedir %>/vamtam/admin/assets/css',
				src: [
					'vamtam-admin.less',
					'vamtam-admin-all.less',
					'customizer.less',
				],
				dest: '<%= basedir %>/vamtam/admin/assets/css',
				ext: '.css',
			},
			theme: {
				options: {
					paths: [ '<%= basedir %>/vamtam/assets/css' ],
					sourceMap: true,
					sourceMapURL: function( css_path ) {
						return path.basename( css_path) + '.map';
					},
					sourceMapRootpath: `/wp-content/themes/${theme_name}/`,
					sourceMapBasepath: '<%= basedir %>/',
				},
				expand: true,
				cwd: './vamtam/assets/css/src/',
				src: [
					// elementor
					'elementor/responsive/**/*.less',
					'elementor/elementor-all.less',
					'elementor/woocommerce/**/*.less',
					'elementor/woocommerce/**/responsive/*.less',
					'!elementor/woocommerce/general/**',

					// fallback
					'fallback/responsive/**/*.less',
					'fallback/widgets/**/*.less',
					'fallback/all.less',
					'fallback/editor.less',
					'fallback/header.less',
					'fallback/blog.less',
					'fallback/not-found.less',
					'fallback/woocommerce/*.less',
					'!deps/**',
					'!**/mixins.less',
				],
				dest: 'vamtam/assets/css/dist/',
				ext: '.css',
				extDot: 'last',
			}
		},
		clean: {
			build: 'build/',
			dist: 'dist/',
			'post-copy': {
				src: [
					'build/**/vamtam/plugins/**/*',
					'!build/**/vamtam/plugins/*.php',

					'!build/**/vamtam/plugins/vamtam-importers-e.zip',
					`!build/**/vamtam/plugins/vamtam-elementor-integration-${theme_name.replace( /^vamtam-/, '' )}.zip`,
					`!build/**/vamtam/plugins/vamtam-product-qa.zip`,

					'build/**/node_modules',
					'build/**/desktop.ini',
					'build/**/style_switcher',
					'build/**/secrets.json',

					'build/**/cache/empty',
				]
			}
		},
		copy: {
			theme: {
				src: [
					'**/*',
					'!**/node_modules/**/*',
				],
				dest: path.join('build', theme_name) + path.sep
			},
			'layerslider-samples': {
				expand: true,
				src: ['**'],
				cwd: 'samples/layerslider/',
				dest: 'vamtam/plugins/layerslider/sampleslider/'
			}
		},
		replace: {
			'style-switcher': {
				options: {
					patterns: [{
						match: /\/\/ @todo remove everything after and including this comment when packaging for sale[\s\S]*/,
						replacement: getBuildHash(),
					}]
				},
				files: [{
					src: [ path.join('build', theme_name, 'functions.php') ],
					dest: path.join('build', theme_name, 'functions.php'),
				}]
			}
		},
		'add-textdomain': {
			theme: [
				'**/*.php',
				'!vendor/**',
				'!vamtam/plugins/*/**',
				'!node_modules',
			]
		},
		phpcs: {
			application: {
				src: [
					'**/*.php',

					'!vamtam/plugins/vamtam-importers-e/**',

					// not outputted as html
					'!vamtam/plugins/vamtam-elements-*/modules/*/includes/frontend.css.php',
					'!vamtam/plugins/vamtam-elements-*/modules/*/includes/frontend.js.php',

					// not used in this theme
					'!vamtam/plugins/vamtam-push-menu/**',
					'!vamtam/plugins/vamtam-sermons/**',
					'!vamtam/plugins/vamtam-scrolling/**',
					'!vamtam/plugins/vamtam-love-it/**',

					'!vamtam/options/help/docs.php',

					'!style_switcher/**',

					// third-party code
					'!vamtam/plugins/layerslider/**',
					'!vamtam/plugins/revslider/**',
					'!vamtam/plugins/foodpress/**',
					'!vamtam/plugins/timetable/**',
					'!vamtam/plugins/vamtam-elements-*/extensions/fl-builder-*/**',
					'!utils/**',
					'!vendor/**',
					'!vamtam/classes/mobile-detect.php',
					'!vamtam/classes/class-tgm-plugin-activation.php',
					'!vamtam/admin/helpers/updates/class-envato-protected-api.php',
					'!node_modules/**',
					'!**/node_modules/**',
					'!documentation/**',
					'!vamtam/redux/extensions/advanced_customizer/**',
				],
			},
			options: {
				bin: 'phpcs',
				standard: 'vamtam',
				p: true,
				report: 'summary',
				// report: 'full',
			}
		},
		ucss: {
			local: {
				options: {
					// whitelist: [],
					// auth: null
				},
				pages: {
					crawl: 'http://construction.demo.local',
					include: []
				},
				css: ['http://construction.demo.local/wp-content/themes/construction/cache/all.css']
			}
		}
	};
};
