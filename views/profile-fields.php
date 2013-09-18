<?php
/**
 * profile-fields.php
 * 
 * @created 9/17/13 3:24 PM
 * @author Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link http://www.mindsharelabs.com/documentation/
 * 
 */
?>
<h3><?php _e("Extra profile information", "email-subscribe"); ?></h3>
<table class="form-table">
	<tr>
		<th><label for="address"><?php _e("Address", "email-subscribe"); ?></label></th>
		<td>
			<input type="text" name="address" id="address" value="<?php echo esc_attr(get_the_author_meta('address', $user->ID)); ?>" class="regular-text" /><br />
			<span class="description"><?php _e("Please enter your address.", "email-subscribe"); ?></span>
		</td>
	</tr>
</table>
