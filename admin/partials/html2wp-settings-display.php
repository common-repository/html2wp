<?php 
/**
 * @since      1.0.0
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/admin/partials
 */

if (isset($_POST["import_settings"])) {
	$pages 		 = $this->save_import_settings($_POST["import_settings"]);
}

$pages       = $this->get_wp_pages();
$templates   = $this->get_page_templates();
$users	     = $this->users_by_roles(["administrator","editor"]);
$get_options = extract($this->get_import_options());
$post_stat = [
	"publish" => "Published",
	"draft"   => "Draft",
	"private" => "Private",
	"pending" => "Pending Review"
];
$post_type = [
	"page" => "Page",
	"post"   => "Post"
];

$hide = $_type == "post"? "none":"block";
?>
<section class="wrap">
	<h2><b><?php _e("Settings","html-2-wp"); ?></b></h2>
	<div class="content_options">
		<form action="" method="POST">
			<table class="form-table">
					<tr>
						<th scope="row"><?php _e("Convert files as","html-2-wp"); ?></th>
						<td>
							<select name="import_settings[type]" id="convert2type">
								<?php foreach ($post_type as $key => $val) { 
									$sel = $_type == $key? "selected":"";
									?>
									<option <?php echo esc_attr($sel); ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($val); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr data-show="<?php echo esc_attr($hide); ?>" class="additional_settings">
						<th scope="row"><?php _e("Parent as","html-2-wp"); ?></th>
						<td>
							<select name="import_settings[parent]">
								<option value="0">None</option>
								<?php foreach ($pages as $key) {
									  $this->walker_items = [];
									  $this->page_walker($key,1,$_parent);
									  $walker =  $this->walker_items;
										foreach ($walker as $k) {
											echo esc_html($k);
										}
									?>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr  data-show="<?php echo esc_attr($hide) ?>" class="additional_settings">
						<th scope="row"><?php _e("Template as:","html-2-wp"); ?></th>
						<td>
							<select name="import_settings[template]">
								<option value="default">Default</option>
								<?php 
									foreach ($templates as $key => $value) { 
										$sel = $_template == $value? "selected":""; ?>
										<option <?php echo esc_attr($sel); ?> value="<?php echo esc_attr($value) ?>"><?php echo esc_html($key); ?></option>
								<?php	}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e("Status as","html-2-wp"); ?></th>
						<td>
							<select name="import_settings[status]">
								<?php foreach ($post_stat as $key => $val) { 
									$sel = $_status == $key? "selected":""; ?>
									<option <?php echo esc_attr($sel); ?>  value="<?php echo esc_attr($key) ?>"><?php echo esc_html($val) ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e("Author as","html-2-wp"); ?></th>
						<td>
							<select name="import_settings[author]">
								<?php foreach ($users as $key) { 
									$sel = $_author == $key->ID? "selected":""; ?>
									<option  <?php echo esc_attr($sel); ?> value="<?php echo esc_attr($key->ID); ?>"><?php echo esc_html($key->user_login) ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e("Import Content From","html-2-wp"); ?></th>
						<td class="import_content_by">
							<p>
								<?php 
									$check = $_content_by == false ?"all_file":$_content_by; 
									$tag_checked  = $check == "tag"?"checked":""; 
									$file_checked = $check == "all_file"?"checked":"";
									$inactive	  = $check == "all_file"?"inactive":"";
								?>
								<label>
									<input <?php echo esc_attr($tag_checked); ?> type="radio" name="import_settings[content_by]" value="tag">
									<?php _e("HTML Tag","html-2-wp"); ?>
								</label>
								<label>
									<input <?php echo esc_attr($file_checked); ?> type="radio" name="import_settings[content_by]" value="all_file">
									<?php _e("All Body","html-2-wp"); ?>
								</label>
							</p>
							<p class="content_by_tag <?php echo esc_attr($inactive); ?>">
								<
								<input class="input_tags_style" placeholder="div" type="text" value="<?php echo esc_html($_content_by_tag); ?>" name="import_settings[content_by_tag]">&nbsp;&nbsp;&nbsp;
								<input class="input_tags_style" placeholder="id" type="text" value="<?php echo esc_html($_content_by_att) ?>" name="import_settings[content_by_att]">
								=<span class="quotes">"</span>
								<input class="input_tags_style input_tags_attr_val" placeholder="cool_content" type="text" value="<?php echo esc_html($_content_by_val); ?>" name="import_settings[content_by_val]">
								<span class="quotes">"</span>
								>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e("Set Title From","html-2-wp"); ?></th>
						<td class="import_title_by">
							<p>
								<?php 
									$check = $_title_by == false ?"file_name":$_title_by; 
									$tag_checked  = $check == "tag"?"checked":""; 
									$file_checked = $check == "file_name"?"checked":"";
									$inactive	  = $check == "file_name"?"inactive":"";
								?>
								<label>
									<input <?php echo esc_html($tag_checked); ?> type="radio" name="import_settings[title_by]" value="tag">
									<?php _e("HTML Tag","html-2-wp"); ?>
								</label>
								<label>
									<input <?php echo esc_html($file_checked); ?> type="radio" name="import_settings[title_by]" value="file_name">
									<?php _e("File Name","html-2-wp"); ?>
								</label>
							</p>
							<p class="title_by_tag <?php echo esc_attr($inactive); ?>">
								<
								<input class="input_tags_style" placeholder="div" type="text" value="<?php echo esc_html($_title_by_tag); ?>" name="import_settings[title_by_tag]">&nbsp;&nbsp;&nbsp;
								<input class="input_tags_style" placeholder="id" type="text" value="<?php echo esc_html($_title_by_att); ?>" name="import_settings[title_by_att]">
								=<span class="quotes">"</span>
								<input class="input_tags_style input_tags_attr_val" placeholder="cool_title" type="text" value="<?php echo esc_html($_title_by_val); ?>" name="import_settings[title_by_val]">
								<span class="quotes">"</span>>
							</p>
						</td>
					</tr>
					<!-- <tr>
						<th scope="row">Import Images From external links</th>
						<td>
							<input <?php //echo $_import_ext_images == "on"?"checked":""; ?> type="checkbox" name="import_settings[import_external_images]">
							<span>if checked, then all image external links will be replaced to wp links</span>
						</td>
					</tr> -->
					<tr>
						<th scope="row">Replace <span><</span>a<span>></span> href links</th>
						<td>
							<div class="replace_href">
								<label for="">
									From <input class="input-form" name="import_settings[replace_a_href_from]" type="text" placeholder="https://example.com/..." value="<?php echo esc_html($_replace_a_href_from); ?>">
								</label>
								<label for="">
									To <input class="input-form" name="import_settings[replace_a_href_to]" type="text" placeholder="<?php echo esc_url(get_home_url()); ?>/..." value="<?php echo esc_html($_replace_a_href_to); ?>">
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">Replace <span><</span>link<span>></span> href links</th>
						<td>
							<div class="replace_href">
								<label for="">
									From <input class="input-form" name="import_settings[replace_css_href_from]" type="text" placeholder="https://example.com/..." value="<?php echo esc_html($_replace_css_href_from); ?>">
								</label>
								<label for="">
									To <input class="input-form" name="import_settings[replace_css_href_to]" type="text" placeholder="<?php echo esc_url(get_home_url()); ?>/..." value="<?php echo esc_html($_replace_css_href_to); ?>">
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">Replace <span><</span>img<span>></span> src links</th>
						<td>
							<div class="replace_src">
								<label>
									From <input class="input-form" name="import_settings[replace_img_src_from]" type="text" placeholder="https://example.com/..." value="<?php echo esc_html($_replace_img_src_from); ?>">
								</label>
								<label>
									To <input class="input-form" name="import_settings[replace_img_src_to]" type="text" placeholder="<?php echo esc_url(get_home_url()); ?>/..." value="<?php echo esc_html($_replace_img_src_to); ?>">
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">Replace <span><</span>script<span>></span> src links</th>
						<td>
							<div class="replace_src">
								<label>
									From <input class="input-form" name="import_settings[replace_script_src_from]" type="text" placeholder="https://example.com/..." value="<?php echo esc_html( $_replace_script_src_from); ?>">
								</label>
								<label>
									To <input class="input-form" name="import_settings[replace_script_src_to]" type="text" placeholder="<?php echo esc_url(get_home_url()); ?>/..." value="<?php echo esc_html($_replace_script_src_to); ?>">
								</label>
							</div>
						</td>
					</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
</section>