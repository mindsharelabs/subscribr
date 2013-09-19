<?php
/**
 * profile-fields.php
 *
 * @created   9/17/13 3:24 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

// determine what taxonomies are enabled for email subscription, if any
$enabled_taxonomies = $this->get_enabled_taxonomies();
if(!is_array($enabled_taxonomies)) {
	// no terms are enabled, exit now
	return;
}

$subscribed_terms = get_user_meta(get_current_user_id(), 'subscribed-terms', TRUE);
$email_subscribe_pause = get_user_meta(get_current_user_id(), 'email-subscribe-pause', TRUE);
$unsubscribe_all = get_user_meta(get_current_user_id(), 'unsubscribe-all', TRUE);

echo apply_filters('es_profile_title', '<h3 class="'.ES_PLUGIN_SLUG.'">'.__('Subscribe to Updates via Email', 'email-subscribe').'</h3>');
echo apply_filters('es_profile_table_open', '<table class="form-table '.ES_PLUGIN_SLUG.'">');
?>

	<tbody>
	<tr id="email-subscribe" class="form-field field field_type-checkbox">
		<th valign="top" scope="row">
			<label for="subscribed-terms"><?php echo apply_filters('es_profile_subscribed_terms_label', __('Your Notifications', 'email-subscribe')); ?></label></th>
		<td>
			<div style="max-width:500px">
				<select name="subscribed-terms[]" id="subscribed-terms" class="chosen-select" multiple="multiple">
					<option value=""></option>
					<?php foreach($enabled_taxonomies as $taxonomy) : ?>
						<?php $terms = get_terms($taxonomy, array('hide_empty' => FALSE)); ?>
						<optgroup label="<?php $taxonomy_object = get_taxonomy($taxonomy);
						echo $taxonomy_object->labels->name; ?>">
							<?php foreach($terms as $term) : ?>
								<option <?php if($subscribed_terms && in_array($term->slug, $subscribed_terms)) : echo 'selected'; endif; ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
							<?php endforeach; // end term loop ?>
						</optgroup>
					<?php endforeach; // end taxonomy loop ?>
				</select>
			</div>
			<p class="description"><?php echo apply_filters('es_profile_subscribed_terms_description', __('Choose from the terms above to enable email notifications when new posts are published.', 'email-subscribe')); ?></p>
		</td>
	</tr>
	<tr class="hidden-on-singup">
		<th scope="row"><?php _e('Pause Notifications', 'email-subscribe'); ?></th>
		<td>
			<label for="email-subscribe-pause">
				<input name="email-subscribe-pause" type="checkbox" id="email-subscribe-pause" value="1" <?php checked($email_subscribe_pause, 1); ?>> <?php _e('Temporarily stop all notifications.', 'email-subscribe'); ?>
			</label>
		</td>
	</tr>
	<tr class="hidden-on-singup">
		<th scope="row"><?php _e('Unsubscribe from All', 'email-subscribe'); ?></th>
		<td>
			<label for="unsubscribe-all">
				<input name="unsubscribe-all" type="checkbox" id="unsubscribe-all" value="1" <?php checked($unsubscribe_all, 1); ?>> <?php _e('Remove all preferences and stop notifications.', 'email-subscribe'); ?>
			</label>
		</td>
	</tr>
	</tbody>
<?php echo apply_filters('es_profile_table_close', '</table>');
