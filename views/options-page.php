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

$subscribr_options = new mindshare_options_framework(
	array(
		 'project_name' => SUBSCRIBR_PLUGIN_NAME,
		 'menu'         => 'settings',
		 'page_title'   => sprintf(__('%s Settings', 'subscribr'), SUBSCRIBR_PLUGIN_NAME),
		 'menu_title'   => SUBSCRIBR_PLUGIN_NAME,
		 'capability'   => 'manage_options',
		 'option_group' => SUBSCRIBR_OPTIONS,
		 'id'           => SUBSCRIBR_PLUGIN_SLUG.'-settings',
		 'fields'       => array(),
	)
);

// setup Tab labels
$subscribr_options_labels = array(
	'Email Options',
	'Taxonomy Options',
	'General Options',
	//'Mail Scheduling',
	//'Third Party Integration'
);
// filter allows plugins to add new tabs
$subscribr_options_labels = array_merge($subscribr_options_labels, apply_filters('subscribr_option_title', array()));
$subscribr_tabs = array();
foreach($subscribr_options_labels as $label) {
	$subscribr_tabs[sanitize_title($label)] = __($label, 'subscribr');
}
$subscribr_tabs_keys = array_keys($subscribr_tabs);

// start the options page
$subscribr_options->OpenTabs_container('');

// start the left hand nav
$subscribr_options->TabsListing(
	array(
		 'links' => $subscribr_tabs
	)
);

/*
 * tab start
 */

$subscribr_options->OpenTab($subscribr_tabs_keys[0]);
$subscribr_options->Title($subscribr_tabs[$subscribr_tabs_keys[0]]);

$subscribr_options->addCheckbox(
	'enable_email_notifications',
	array(
		 'name' => __('Enable Email notifications', 'subscribr'),
		 'std'  => TRUE,
		 'desc'  => __('Globally enable or disable email notifications.', 'subscribr'),
	)
);

$subscribr_options->addText(
	'from_name',
	array(
		 'name' => __('From Name', 'subscribr'),
		 'std'  => get_bloginfo('name'),
		 'desc' => ''
	)
);

$subscribr_options->addText(
	'from_email',
	array(
		 'name' => __('From Email', 'subscribr'),
		 'std'  => get_option('admin_email'),
		 'desc' => ''
	)
);

$subscribr_options->addText(
	'email_subject_new_post',
	array(
		 'name' => __('New Post Email Subject', 'subscribr'),
		 'std'  => get_bloginfo('name').' '.__($this->get_option('notification_label'), 'subscribr'), // @todo
		 'desc' => ''
	)
);

$subscribr_options->addCode(
	'email_body_new_post',
	array(
		 'type'   => 'code',
		 'std'    => 'Feature not yet implemented', //@todo
		 'desc'   => __('This email template will be used for email notifications when new posts are published', 'subscribr'),
		 'name'   => 'New Post Email Template',
		 'syntax' => 'html',

	)
);

$subscribr_options->CloseTab();

/*
 * tab start
 */

$subscribr_options->OpenTab($subscribr_tabs_keys[1]);
$subscribr_options->Title($subscribr_tabs[$subscribr_tabs_keys[1]]);

$subscribr_options->addCheckbox(
	'enable_all_terms',
	array(
		 'name' => __('Enable All Terms', 'subscribr'),
		 'desc' => __('Turning this ON will enable all taxonomy terms, overriding the individual settings below.', 'subscribr')
	)
);

// term choices @todo add select all/none toggle to Mindshare Options Framework
$subscribr_options->addTaxonomy(
	'enabled_terms',
	array(
		 'taxonomy' => $this->get_default_taxonomies(),
		 'type'     => 'checkbox_list',
	),
	array(
		 'name' => __('Enabled Terms', 'subscribr'),
		 'desc' => __('Choose the terms you want to allow users to subscribe to from their profiles.', 'subscribr')
	),
	FALSE
);

$subscribr_options->CloseTab();

/*
 * tab start
 */

$subscribr_options->OpenTab($subscribr_tabs_keys[2]);
$subscribr_options->Title($subscribr_tabs[$subscribr_tabs_keys[2]]);

$subscribr_options->addCheckbox(
	'show_on_profile',
	array(
		 'name' => __('Show notification options on user profile', 'subscribr'),
		 'std'  => TRUE,
	)
);
$subscribr_options->addCheckbox(
	'show_on_register',
	array(
		 'name' => __('Show notification options on registration screen', 'subscribr'),
		 'std'  => FALSE,
	)
);


$subscribr_options->addText(
	'notification_label',
	array(
		 'name' => __('Notification Label', 'subscribr'),
		 'std'  => __('notification', 'subscribr'),
		 'desc' => __('Enter the terminology to use for singular "notifications". E.g. "alert", "subscription", "notification", etc.', 'subscribr')
	)
);

$subscribr_options->addText(
	'notification_label_plural',
	array(
		 'name' => __('Notification Label Plural', 'subscribr'),
		 'std'  => __('notifications', 'subscribr'),
		 'desc' => __('Enter the terminology to use for plural "notifications". E.g. "alerts", "subscriptions", "notifications", etc.', 'subscribr')
	)
);

$subscribr_options->CloseTab();

/*
 * tab start
 */
/*
$subscribr_options->OpenTab($subscribr_tabs_keys[3]);
$subscribr_options->Title($subscribr_tabs[$subscribr_tabs_keys[3]]);

$subscribr_options->addParagraph(
	'Feature not yet implemented.'
);

$subscribr_options->CloseTab();*/

// action to allow plugging in extra options
do_action('subscribr_option_add', $subscribr_options);

/*
 * Help Tabs
 */
$subscribr_options->HelpTab(
	array(
		 'id'      => 'subscribr-help-tab',
		 'title'   => sprintf(__('%s Documentation', 'subscribr'), SUBSCRIBR_PLUGIN_NAME),
		 'content' => sprintf(__('<p>%1$s documentation is available online at <a href="http://mindsharelabs.com/topics/%2$s/" target="_blank">http://mindsharelabs.com/topics/%2$s/</a></p>', 'subscribr'), SUBSCRIBR_PLUGIN_NAME, SUBSCRIBR_PLUGIN_SLUG)
	)
);
$subscribr_options->HelpTab(
	array(
		 'id'      => 'subscribr-support-tab',
		 'title'   => __('Support Forum', 'subscribr'),
		 'content' => sprintf(__('<p>Get support on the WordPress.org forums: <a href="http://wordpress.org/support/plugin/%1$s" target="_blank">http://wordpress.org/support/plugin/%1$s</a></p><p>To get premium one-on-one support, contact us: <a href="http://mind.sh/are/contact/" target="_blank">http://mind.sh/are/contact/</a></p>', 'subscribr'), SUBSCRIBR_PLUGIN_SLUG)
	)
);
$secure_tab_content = sprintf(__('<p>Get the Mindshare Team to secure and protect your WordPress site for $9.95/month: <a href="http://mind.sh/are/wordpress-security-and-backup-service/check/?url=%1$s&amp;active=0&amp;sale=1&amp;d=%2$s" target="_blank">http://mind.sh/are/wordpress-security-and-backup-service/</a></p>', 'subscribr'), get_bloginfo("url"), str_replace(array(
																																																																																													 "http://",
																																																																																													 "https://"
																																																																																												), "", get_home_url()));

$subscribr_options->HelpTab(
	array(
		 'id'      => 'subscribr-security-tab',
		 'title'   => __('Protect Your Site', 'subscribr'),
		 'content' => $secure_tab_content
	)
);

