const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			'css/wcdm-admin': './src/css/admin-common.scss',
			'js/wcdm-admin': './src/js/admin-common.js',
			'css/wcdm-frontend': './src/css/frontend-common.scss',
			'js/wcdm-frontend': './src/js/frontend-common.js',
		},
		output: {
			...defaultConfig.output,
			filename: '[name].js',
			path: __dirname + '/assets/',
		},
		module: {
			rules: [
				...defaultConfig.module.rules,
				{
					test: /\.svg$/,
					issuer: /\.(j|t)sx?$/,
					use: ['@svgr/webpack', 'url-loader'],
					type: 'javascript/auto',
				},
				{
					test: /\.svg$/,
					issuer: /\.(sc|sa|c)ss$/,
					type: 'asset/inline',
				},
				{
					test: /\.(bmp|png|jpe?g|gif)$/i,
					type: 'asset/resource',
					generator: {
						filename: 'images/[name].[hash:8][ext]',
					},
				},
			],
		},
		plugins: [
			...defaultConfig.plugins,
			// Copy images to the build folder.
			new CopyWebpackPlugin({
				patterns: [
					{
						from: path.resolve(__dirname, 'src/images'),
						to: path.resolve(__dirname, 'assets/images'),
					}
				]
			}),

			new RemoveEmptyScriptsPlugin({
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
				remove: /\.(js)$/,
			}),
		],
	},
];
