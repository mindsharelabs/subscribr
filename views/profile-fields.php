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

echo apply_filters('es_profile_title', '<h3 class="'.ES_PLUGIN_SLUG.'">'.__('Subscribe to Updates via Email', 'email-subscribe').'</h3>');
echo apply_filters('es_profile_table_open', '<table class="form-table '.ES_PLUGIN_SLUG.'">');
?>
	<tbody>
	<tr id="email-subscribe" class="form-field field field_type-checkbox">
		<th valign="top" scope="row">
			<label for="subscribed-terms"><?php echo apply_filters('es_profile_subscribed_terms_label', __('Your subscriptions', 'email-subscribe')); ?></label></th>
		<td>
			<select class="chosen-select" multiple="">
				<option value=""></option>
				<optgroup label="NFC EAST">
					<option>Dallas Cowboys</option>
					<option>New York Giants</option>
					<option>Philadelphia Eagles</option>
					<option>Washington Redskins</option>
				</optgroup>
				<optgroup label="NFC NORTH">
					<option>Chicago Bears</option>
					<option>Detroit Lions</option>
					<option>Green Bay Packers</option>
					<option>Minnesota Vikings</option>
				</optgroup>

			</select>

			<p class="description"><?php echo apply_filters('es_profile_subscribed_terms_description', __('Choose from the terms above to enable notifications via email when new posts are published.', 'email-subscribe')); ?></p>
		</td>
	</tr>
	</tbody>

<?php
echo apply_filters('es_profile_table_close', '</table>');
