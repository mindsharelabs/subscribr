<?php
/**
 * options-page.php
 *
 * @created   9/17/13 4:11 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

$es_options = new mindshare_options_framework(
	array(
		 'project_name' => ES_PLUGIN_NAME,
		 'menu'         => 'settings',
		 'page_title'   => sprintf(__('%s Settings', 'email-subscribe'), ES_PLUGIN_NAME),
		 'menu_title'   => ES_PLUGIN_NAME,
		 'capability'   => 'manage_options',
		 'option_group' => ES_OPTIONS,
		 'id'           => ES_PLUGIN_SLUG.'-settings',
		 'fields'       => array(),
	)
);

// setup Tab labels
$es_options_labels = array(
	'Email Options',
	'Taxonomy Options',
	'General Options',
	//'Mail Scheduling',
	//'Third Party Integration'
);
// filter allows plugins to add new tabs
$es_options_labels = array_merge($es_options_labels, apply_filters('es_option_title', array()));
$es_tabs = array();
foreach($es_options_labels as $label) {
	$es_tabs[sanitize_title($label)] = __($label, 'email-subscribe');
}
$es_tabs_keys = array_keys($es_tabs);

// start the options page
$es_options->OpenTabs_container('');

// start the left hand nav
$es_options->TabsListing(
	array(
		 'links' => $es_tabs
	)
);

/*
 * tab start
 */

$es_options->OpenTab($es_tabs_keys[0]);
$es_options->Title($es_tabs[$es_tabs_keys[0]]);

$es_options->addText(
	'from_name',
	array(
		 'name' => __('From Name', 'email-subscribe'),
		 'std'  => get_bloginfo('name'),
		 'desc' => ''
	)
);

$es_options->addText(
	'from_email',
	array(
		 'name' => __('From Email', 'email-subscribe'),
		 'std'  => get_option('admin_email'),
		 'desc' => ''
	)
);
$es_options->CloseTab();

/*
 * tab start
 */

$es_options->OpenTab($es_tabs_keys[1]);
$es_options->Title($es_tabs[$es_tabs_keys[1]]);

$es_options->addCheckbox(
	'enable_all_terms',
	array(
		 'name' => __('Enable All Terms', 'email-subscribe'),
		 'desc' => __('Turning this ON will enable all taxonomy terms, overriding the individual settings below.', 'email-subscribe')
	)
);

// term choices @todo add select all/none toggle to Mindshare Options Framework
$es_options->addTaxonomy(
	'enabled_terms',
	array(
		 'taxonomy' => $this->get_default_taxonomies(),
		 'type'     => 'checkbox_list',
	),
	array(
		 'name' => __('Enabled Terms', 'email-subscribe'),
		 'desc' => __('Choose the terms you want to allow users to subscribe to from their profiles.', 'email-subscribe')
	),
	FALSE
);

$es_options->CloseTab();

/*
 * tab start
 */

$es_options->OpenTab($es_tabs_keys[2]);
$es_options->Title($es_tabs[$es_tabs_keys[2]]);

$es_options->addCheckbox(
	'show_on_profile',
	array(
		 'name' => __('Show subscription options on user profile', 'email-subscribe'),
		 'std'  => TRUE,
	)
);
$es_options->addCheckbox(
	'show_on_register',
	array(
		 'name' => __('Show subscription options on registration screen', 'email-subscribe'),
		 'std'  => FALSE,
	)
);

$es_options->CloseTab();

/*
 * tab start
 */
/*
$es_options->OpenTab($es_tabs_keys[3]);
$es_options->Title($es_tabs[$es_tabs_keys[3]]);

$es_options->addParagraph(
	'Feature not yet implemented.'
);

$es_options->CloseTab();*/

// action to allow plugging in extra options
do_action('es_option_add', $es_options);

/*
 * Help Tabs
 */
$es_options->HelpTab(
	array(
		 'id'      => 'es-help-tab',
		 'title'   => sprintf(__('%s Documentation', 'email-subscribe'), ES_PLUGIN_NAME),
		 'content' => sprintf(__('<p>%1$s documentation is available online at <a href="http://mindsharelabs.com/topics/%2$s/" target="_blank">http://mindsharelabs.com/topics/%2$s/</a></p>', 'email-subscribe'), ES_PLUGIN_NAME, ES_PLUGIN_SLUG)
	)
);
$es_options->HelpTab(
	array(
		 'id'      => 'es-support-tab',
		 'title'   => __('Support Forum', 'email-subscribe'),
		 'content' => sprintf(__('<p>Get support on the WordPress.org forums: <a href="http://wordpress.org/support/plugin/%1$s" target="_blank">http://wordpress.org/support/plugin/%1$s</a></p><p>To get premium one-on-one support, contact us: <a href="http://mind.sh/are/contact/" target="_blank">http://mind.sh/are/contact/</a></p>', 'email-subscribe'), ES_PLUGIN_SLUG)
	)
);
$secure_tab_content = sprintf(__('<p>Get the Mindshare Team to secure and protect your WordPress site for $9.95/month: <a href="http://mind.sh/are/wordpress-security-and-backup-service/check/?url=%1$s&amp;active=0&amp;sale=1&amp;d=%2$s" target="_blank">http://mind.sh/are/wordpress-security-and-backup-service/</a></p>', 'email-subscribe'), get_bloginfo("url"), str_replace(array(
																																																																																														   "http://",
																																																																																														   "https://"
																																																																																													  ), "", get_home_url()));

$es_options->HelpTab(
	array(
		 'id'      => 'es-security-tab',
		 'title'   => __('Protect Your Site', 'email-subscribe'),
		 'content' => $secure_tab_content
	)
);

