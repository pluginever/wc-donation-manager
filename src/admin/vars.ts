/**
 * The data the PHP side localizes onto the admin bundle.
 */
/**
 * External dependencies
 */
import { toBoolean } from '@byteever/ui';

export type WCDMAdminVars = {
	/** Menu slug of the admin page the app mounts on. */
	slug: string;
	/** Plugin version. */
	version: string;
	/** REST API configuration. */
	rest: {
		url: string;
		nonce: string;
		namespace: string;
	};
	/** Admin URL. */
	adminUrl: string;
	/** Plugin assets URL. */
	assetsUrl: string;
	/** Whether the Pro add-on is active. */
	isPro: boolean;
	/** Whether the Pro add-on's license is valid; false when Pro is inactive. */
	licensed?: boolean;
	/** Pro landing page URL. */
	upgradeUrl: string;
	/** Documentation URL. */
	docsUrl: string;
	user: {
		id: number;
		name: string;
		email: string;
		capabilities: Record< string, boolean >;
	};
	locale: string;
	/** Store currency code and symbol. */
	currency: string;
	currencySymbol: string;
};

const raw: WCDMAdminVars = window.wcdm_admin_vars;

// `wp_localize_script` casts every scalar to a string, so `isPro` and
// `licensed` arrive as "true"/"false"; normalize them once, here, so the
// rest of the app reads real booleans.
export const vars: WCDMAdminVars = Object.freeze( {
	...raw,
	isPro: toBoolean( raw.isPro ),
	licensed: toBoolean( raw.licensed ),
} );

/** The Pro add-on is running and its license is valid. */
export const isLicensed = vars.isPro && vars.licensed;
