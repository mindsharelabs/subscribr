<?php
/**
 * default-email-template.php
 *
 * Modify this to suit your needs by copying it into your active
 * theme folder into a subdirectory named `subscribr`.
 *
 * @created   9/17/13 3:26 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */
// @todo set proper post vars
$msg = " ";
$msg .= "A new post is available on ".get_bloginfo('name').":";
$msg .= " ";
$msg .= get_the_title($post_id).' ('.get_post($post_id)->post_date.')';
$msg .= " ";
$msg .= wp_trim_words(get_post($post_id)->post_content, $num_words = 55, $more = NULL);
$msg .= " ";
$msg .= "Permalink: ".get_permalink($post_id);
$msg .= " ";
$msg .= "---------------------------------------";
$msg .= " ";
$msg .= "You received this email because you asked to be notified when new updates are published.";
$msg .= " ";
$msg .= "Manage your ".$subscribr->get_option('notification_label_plural')." or unsubscribe here: {PROFILELINK}";
$msg .= " ";
$msg .= "---------------------------------------";
$msg .= " ";
$msg .= "- The ".get_bloginfo('name')." Team";
$msg .= " ";

