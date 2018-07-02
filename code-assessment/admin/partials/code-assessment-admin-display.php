<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://wordpressguru.net
 * @since      1.0.0
 *
 * @package    Code_Assessment
 * @subpackage Code_Assessment/admin/partials
 */
?>

<h1>Code Assessment Settings</h1>
<form method="post" action="options.php">
	<?php settings_fields( 'code-assessment-settings' ); ?>
	<?php do_settings_sections( 'code-assessment-settings' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Number of posts displayed?</th>
            <td><input type="number" name="number_of_posts" value="<?php echo get_option( 'number_of_posts' ); ?>"/></td>
        </tr>
    </table>
	<?php submit_button(); ?>
</form>
