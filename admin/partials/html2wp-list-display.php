<?php 
/**
 * @since      1.0.0
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/admin/partials
 */
 $htmls 	 = $this->get_html_files();

?>
<section class="wrap">
	<h2><?php _e("Convert HTML To WP Pages","html-2-wp"); ?></h2><br>
	<table class="html_list_table">
		<tr>
			<th><?php _e("Name","html-2-wp"); ?></th>
			<th><?php _e("WP Page Slug","html-2-wp"); ?></th>
			<th><?php _e("Action","html-2-wp"); ?></th>
		</tr>
		<?php
		if (!empty($htmls)) {
			foreach ($htmls as $key) {
				$path = base64_encode($key['path']);
				$name = $key["name"];
				$converteds  = $this->get_converteds("html_name",$name);
				extract($this->html_list_variables($converteds));
				?>
				<tr>
					<td><?php echo esc_html($name); ?></td>
					<td class="page_slug"><a href="<?php echo esc_url($wp_slug); ?>"><?php echo esc_url($wp_slug); ?></a></td>
					<td>
						<div
							data-pid='<?php echo esc_html($post_id); ?>'  
							data-cid='<?php echo esc_html($cid); ?>' 
							data-name='<?php echo esc_html($name); ?>' 
							data-path='<?php echo esc_html($path); ?>'>
							<button title="<?php echo esc_html($btn_title); ?>" class="btn_todo <?php echo esc_html($btn_class); ?>"><?php echo esc_html($btn_name) ?></button>&nbsp;
							<button class="btn_remove remove_html_files" title="Remove HTML file">Remove</button>
						</div>
					</td>
				</tr>
			<?php }
		}else{ ?>
			<tr>
				<td colspan="3"><?php _e("There is no HTML files","html-2-wp"); ?></td>
			</tr>
		<?php }
		 ?>
	</table>
</section>	