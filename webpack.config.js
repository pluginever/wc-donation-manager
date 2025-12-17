/**
 * External dependencies
 */
const baseConfig = require( '@byteever/scripts/config/webpack.config' );

module.exports = {
	...baseConfig,
	entry: {
		...baseConfig.entry,
		'css/admin': './assets/src/css/admin-common.scss',
		'js/admin': './assets/src/js/admin-common.js',
		'css/frontend': './assets/src/css/frontend-common.scss',
		'js/frontend': './assets/src/js/frontend-common.js'
	},
};
