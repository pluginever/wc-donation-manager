/**
 * The data the PHP side localizes onto the admin bundle.
 */
interface WCDMAdminVars {
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
	licensed: boolean;
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
}

interface Window {
	wcdm_admin_vars: WCDMAdminVars;
}

declare module '*.scss';
declare module '*.css';
