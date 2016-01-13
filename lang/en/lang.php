<?php
return [
	'details' => [
		'title' => 'Landing Pages',
		'description' => 'Multidomain support plugin for October',
		'description2' => 'Manage Multidomain domain to url binds',
		'problem' => 'Multidomain plugin tables not found, force reinstall plugin.',
	],

	'controller' => [
		'settings' => 'Settings',
		'confirm' => 'Are you sure?',
		'new-bind' => 'New bind',
		'delete' => 'Delete',
		'clear-cache' => 'Clear cache',
		'create-bind' => 'Create Bind',
		'edit-bind' => 'Edit Bind',
		'create' => 'Create',
		'create-and-close' => 'Create and Close',
		'cancel' => 'Cancel',
		'return' => 'Return to settings list',
		'creating' => 'Creating Setting...',
		'or' => 'or',
		'save' => 'Save',
		'saving' => 'Saving ...',
		'save-and-close' => 'Save and close',
		'save-delete' => 'Delete save'
	],

	'domain' => [
		'label' => 'Domain to bind url to',
		'comment' => 'Must be a full url, ie.: http://octobercms.com',
		'page-label' => 'Page Url',
		'page-comment' => 'Select url for this domain',
		'type-label' => 'Page Type',
		'type-comment' => 'Whether custom cms page or static page',
		'protect-label' => 'Protect backend on this domain',
		'protect-comment' => 'comment: Check if you want to prohibit backend entry from that domain, throws HTTP 401, uses /error page'
	],

	'flash' => [
		'cache-clear' => 'Multidomain cache cleared.',
		'db-error' => 'Multidomain plugin tables not found, force reinstall plugin.',
	],

	'tables' => [
		'domain' => 'Domain',
		'page' => 'Page Url',
		'type' => 'Page type',
		'protected' => 'Protected?'
	]
];
