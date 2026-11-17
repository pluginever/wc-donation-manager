export type Campaign = {
	id: number;
	name: string;
	cause: string;
	status: string;
	goal_amount: number;
	raised_amount: number;
	end_date: string;
	goal_behavior: string;
	products: Array< { id: number; name: string; price: number } >;
	date_created: string;
	date_modified: string;
};

export type CampaignOption = { value: string; label: string };

export type CampaignOptions = {
	statuses: CampaignOption[];
	behaviors: CampaignOption[];
};

export type OnAction = (
	action: string,
	ids?: Array< string | number >
) => void | Promise< void >;
