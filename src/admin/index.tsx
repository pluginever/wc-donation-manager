/**
 * External dependencies
 */
import { Router, type Route } from '@byteever/router';
import { useRoutes } from '@byteever/ui';

/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import { app } from './app';
import Root from './root';
import './style.scss';

function Admin() {
	return (
		<Router routes={ useRoutes< Route >( 'wcdm' ) }>
			<Root />
		</Router>
	);
}

domReady( () => app.render( 'wcdm-admin', <Admin /> ) );
