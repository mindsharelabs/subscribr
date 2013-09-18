<?php
/**
 * default-email-template.php
 *
 * @created   9/17/13 3:26 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */
$permalink = get_permalink($post_id);
$posttitle = get_the_title($post_id);
global $post;
$content = get_post($post_id)->post_content;
$excerpt = wp_trim_words($content, $num_words = 55, $more = NULL);
$postdate = get_post($post_id)->post_date;

$permalink .= "?utm_source=blogtoemail&utm_medium=email&utm_campaign=blogtoemail".date("mdy"); // append analytics tracking parameters here

$template["htmlBody"] = "compose HTML email content here";

$contacts = "put a query here to pull an array of contacts who want the email";
?>
