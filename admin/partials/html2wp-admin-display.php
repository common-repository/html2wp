<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/admin/partials
 */
$errors = $this->wp_error->errors;
if (!empty($errors) && isset($errors["upload"])) {
	foreach ($errors["upload"] as $key) { ?>
		<div class="error notice">
	        <p><?php echo esc_html($key); ?></p>
	    </div>
		<?php
	}
}else{ 
	if($this->success_message("import_success")):?>
		<div class="success notice is-dismissible">
		    <p><?php echo $this->success_message("import_success"); ?></p>
		</div>
<?php endif; }  ?>

<section class="wrap">
	<h2><?php _e("HTML To WP Importer","html-2-wp"); ?></h2>
	<!-- <p>From where you would like to import files?</p> -->
	<!-- <p>
		<label for="local_importing">
			From Your Computer
			<input type="radio" id="local_importing" checked  name="import_html">
		</label>
		<label for="server_importing">
			From Your Server
			<input type="radio" id="server_importing" name="import_html">
		</label>
	</p> -->

	<div class="import_options">
		<form action="" method="post" enctype="multipart/form-data">
			<p class="local_importing">
				<label>
					<?php _e("Choose your html files/file for import","html-2-wp"); ?> <br><br>
					<input type="file" multiple name="local_importing[]">
				</label>
			</p>
			<p class="server_importing h2p_hide">
				<?php _e("Path From Your Server for Import","html-2-wp"); ?> 
				<input type="text" class="regular-text" name="server_importing_path" value="" data-val="C:/xampp/htdocs/wordpress/html-files-to-import">
			</p>
			<button class="button">Submit</button>
		</form>
	</div>

</section>