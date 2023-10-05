const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			'css/wcdm-admin': './assets/src/css/admin-common.scss',
			'js/wcdm-admin': './assets/src/js/admin-common.js',
			// 'css/frontend-common': './assets/src/js/frontend-common.scss',
			// 'js/frontend-common': './assets/src/js/frontend-common.js',
		},
		output: {
			...defaultConfig.output,
			filename: '[name].js',
			path: __dirname + '/assets/dist/',
		},
		plugins: [
			...defaultConfig.plugins,
			new RemoveEmptyScriptsPlugin({
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
				remove: /\.(js)$/,
			}),
		],
	},
];
