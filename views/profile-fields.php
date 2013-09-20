<?php
/**
 * profile-fields.php
 *
 * The fields to be displayed on user profiles and/or on the register page.
 *
 *
 * @created   9/17/13 3:24 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

// determine what taxonomies are enabled for email notification, if any
$enabled_taxonomies = $this->get_enabled_taxonomies();
if(!is_array($enabled_taxonomies)) {
	// no terms are enabled, exit now
	return;
}

$subscribed_terms = get_user_meta(get_current_user_id(), 'subscribr-terms', TRUE);
$subscribr_pause = get_user_meta(get_current_user_id(), 'subscribr-pause', TRUE);
$subscribr_unsubscribe = get_user_meta(get_current_user_id(), 'subscribr-unsubscribe', TRUE);
$notification_label_plural = $this->get_option('notification_label_plural');

echo apply_filters('subscribr_profile_title', '<h3 class="'.SUBSCRIBR_PLUGIN_SLUG.'">'.__('Update ', 'subscribr').$notification_label_plural.__(' via email', 'subscribr').'</h3>');
echo apply_filters('subscribr_profile_table_open', '<table class="form-table '.SUBSCRIBR_PLUGIN_SLUG.'">');
?>

	<tbody>
	<tr id="subscribr" class="form-field field field_type-checkbox">
		<th valign="top" scope="row">
			<label for="subscribr-terms"><?php echo apply_filters('subscribr_profile_subscribed_terms_label', __('Your ', 'subscribr').$notification_label_plural); ?></label></th>
		<td>
			<div id="subscribr-terms-container">
				<select name="subscribr-terms[]" id="subscribr-terms" class="chosen-select" multiple="multiple">
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
			<p class="description"><?php echo apply_filters('subscribr_profile_subscribed_terms_description', __('Choose from the terms above to enable email ', 'subscribr') .$notification_label_plural. __('when new posts are published.', 'subscribr')); ?></p>
		</td>
	</tr>
	<tr class="hidden-on-singup">
		<th scope="row"><?php _e('Pause ', 'subscribr'); echo $notification_label_plural; ?></th>
		<td>
			<label for="subscribr-pause">
				<input name="subscribr-pause" type="checkbox" id="subscribr-pause" value="1" <?php checked($subscribr_pause, 1); ?>> <?php _e('Temporarily stop all ', 'subscribr'); echo $notification_label_plural; ?>
			</label>
		</td>
	</tr>
	<tr class="hidden-on-singup">
		<th scope="row"><?php _e('Unsubscribe from all ', 'subscribr'); echo $notification_label_plural; ?></th>
		<td>
			<label for="subscribr-unsubscribe">
				<input name="subscribr-unsubscribe" type="checkbox" id="subscribr-unsubscribe" value="1" <?php checked($subscribr_unsubscribe, 1); ?>> <?php _e('Remove preferences and stop all ', 'subscribr'); echo $notification_label_plural.'.'; ?>
			</label>
		</td>
	</tr>
	</tbody>
<?php echo apply_filters('subscribr_profile_table_close', '</table>');
