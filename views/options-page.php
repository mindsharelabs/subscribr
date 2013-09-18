<?php
/**
 * options-page.php
 * 
 * @created 9/17/13 4:11 PM
 * @author Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link http://www.mindsharelabs.com/documentation/
 * 
 */

$es_options = new mindshare_options_framework(
	array(
		 'project_name' => ES_PLUGIN_NAME,
		 'menu'         => 'settings',
		 'page_title'   => ES_PLUGIN_NAME.' '.__('Settings', 'email-subscribe'),
		 'menu_title'   => ES_PLUGIN_NAME,
		 'capability'   => 'manage_options',
		 'option_group' => ES_OPTIONS,
		 'id'           => ES_PLUGIN_SLUG.'-settings',
		 'fields'       => array(),
	)
);

$es_options->OpenTabs_container('');

$es_options_label = 'General Options';
$es_options_label2 = 'Taxonomy Options';

$es_options->TabsListing(
	array(
		 'links' =>
		 array(
			 sanitize_title($es_options_label)  => __($es_options_label, 'email-subscribe'),
			 sanitize_title($es_options_label2)  => __($es_options_label2, 'email-subscribe'),
		 )
	)
);

/*
 * tab start
 */

$es_options->OpenTab(sanitize_title($es_options_label));
$es_options->Title($es_options_label);

$es_options->addText(
	'from_name',
	array(
		 'name' => 'From Name',
		 'std'  => get_bloginfo('name'),
		 'desc' => ''
	)
);


$es_options->addText(
	'from_email',
	array(
		 'name' => 'From Email',
		 'std'  => get_option('admin_email'),
		 'desc' => ''
	)
);
$es_options->CloseTab();

/*
 * tab start
 */

$es_options->OpenTab(sanitize_title($es_options_label2));
$es_options->Title($es_options_label2);


// taxonomy choices @todo add select all/none toggle to Mindshare Options Framewwork
$taxonomies = get_taxonomies();
$disabled_taxonomies = array('nav_menu', 'post_format', 'link_category');
$taxonomies = array_diff($taxonomies, $disabled_taxonomies);
$es_options->addTaxonomy(
	'taxonomies',
	array(
		 'taxonomy' => $taxonomies,
		 'type'  => 'checkbox_list',
	),
	array(
		 'name' => 'Enabled Terms',
		 'desc' => 'Choose the terms you wish to allow users to subscribe on their profile.'
	),
	FALSE
);

$es_options->CloseTab();

/*
 * Help Tabs
 */
$es_options->HelpTab(
	array(
		 'id'      => 'es-help-tab',
		 'title'   => ES_PLUGIN_NAME.' Documentation',
		 'content' => '<p>'.ES_PLUGIN_NAME.' documentation is available online at <a href="http://mindsharelabs.com/topics/'.ES_PLUGIN_SLUG.'/" target="_blank">http://mindsharelabs.com/topics/'.ES_PLUGIN_SLUG.'/</a></p>'
	)
);
$es_options->HelpTab(
	array(
		 'id'      => 'es-support-tab',
		 'title'   => 'Support Forum',
		 'content' => '<p>Get support on the WordPress.org forums: <a href="http://wordpress.org/support/plugin/'.ES_PLUGIN_SLUG.'" target="_blank">http://wordpress.org/support/plugin/'.ES_PLUGIN_SLUG.'</a></p><p>To get premium one-on-one support, contact us: <a href="http://mind.sh/are/contact/" target="_blank">http://mind.sh/are/contact/</a></p>'
	)
);
$secure_tab_content = "<p>Get the Mindshare Team to secure and protect your WordPress site for $9.95/month: <a href='http://mind.sh/are/wordpress-security-and-backup-service/check/?url=".get_bloginfo("url")."&amp;active=0&amp;sale=1&amp;d=".str_replace(
		array(
			 "http://",
			 "https://"
		),
		"",
		get_home_url())."' target='_blank'>http://mind.sh/are/wordpress-security-and-backup-service/</a></p>";
$es_options->HelpTab(
	array(
		 'id'      => 'es-security-tab',
		 'title'   => 'Protect Your Site',
		 'content' => $secure_tab_content
	)
);
