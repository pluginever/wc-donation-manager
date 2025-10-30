module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks matching the `grunt-*` pattern.
	require( 'load-grunt-tasks' )( grunt );

	// Show elapsed time.
	require( '@lodder/time-grunt' )( grunt );

	// Project configuration.
	grunt.initConfig(
		{
			package: grunt.file.readJSON( 'package.json' ),
			makepot: {
				target: {
					options: {
						domainPath: 'languages',
						exclude: [ 'packages/*', '.git/*', 'node_modules/*', 'tests/*' ],
						mainFile: '<%= package.name %>.php',
						potFilename: '<%= package.name %>.pot',
						potHeaders: {
							'report-msgid-bugs-to': '<%= package.homepage %>',
							'project-id-version': '<%= package.title %> <%= package.version %>',
							poedit: true,
							'x-poedit-keywordslist': true,
						},
						type: 'wp-plugin',
						updateTimestamp: false,
					},
				},
			},
		}
	);

	grunt.registerTask( 'i18n', [ 'makepot' ] );
	grunt.registerTask( 'build', [ 'i18n' ] );
};
