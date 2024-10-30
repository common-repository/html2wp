<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/admin
 * @author     Ars
 */
class HTML2WP_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $html2wp    The ID of this plugin.
	 */
	private $html2wp;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $html2wp       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $html2wp, $version ) {

		$this->html2wp         = $html2wp;
		$this->version         = $version;
		$this->slugs 		   = [];
		$this->uploads_basedir = wp_upload_dir()["basedir"]."/html2wp";
		$this->wp_error 	   = new WP_Error();
		$this->walker_items    = [];
		$this->timeout_message = [];



		if (!empty($_FILES) && isset($_FILES["local_importing"])) {
			$this->upload_html_files();
		}


		// db
		global $wpdb;
		$this->db 	  = $wpdb;
		$this->prefix = $wpdb->prefix;
		$this->table_converteds = $this->prefix."html2wp_converteds";


	}


	/**
	 * html files Ajax
	 *
	 * @since    1.0.0
	 */
	public function wp_ajax_html_actions_call(){
		if (isset($_POST["type"])) {
			if ($_POST["type"] == "remove_html") {
				$cid  = sanitize_text_field(isset($_POST["cid"])?$_POST["cid"]:"");
				$path = sanitize_text_field(isset($_POST["path"])?$_POST["path"]:"");
				$path = $path!= ""? base64_decode($path):"";
				$st   = unlink($path);
				$this->remove_converted($cid);
				echo $st;die;
			}else if($_POST["type"] == "html_convert"){
				$get_options = extract($this->get_import_options());
				$name = sanitize_text_field(isset($_POST["name"])?$_POST["name"]:"");
				$path = sanitize_text_field(isset($_POST["path"])?$_POST["path"]:"");
				$path = $path!= ""? base64_decode($path):"";
				$get  = file_get_contents($path);
				$doc  = new DOMDocument;
				libxml_use_internal_errors(true);
				$doc->loadHTML($get);
				libxml_use_internal_errors(false);

				// js script
				// $js_script = $doc->getElementsByTagName('head')->item(0)->getElementsByTagName("script");
				// $js_script_all = [];
				// if ( $js_script && 0< $js_script->length ) {
				// 	for ($i=0; $i < $js_script->length; $i++) { 
				// 	    $item = $js_script->item($i);
				// 		$js_script_all[] = $doc->savehtml($item);
				// 	}
				// }

				// // css links
				// $css_links = $doc->getElementsByTagName('head')->item(0)->getElementsByTagName("link");
				// $css_links_all = [];
				// if ( $css_links && 0<$css_links->length ) {
				// 	for ($i=0; $i < $css_links->length; $i++) { 
				// 	    $item 	   = $css_links->item($i);
				// 	    if ($item->getAttribute("rel") == "stylesheet") {
				// 		    $css_links_all[]  = $doc->savehtml($item);
				// 	    }
				// 	}
				// }

				// // styles
				// $style = $doc->getElementsByTagName('head')->item(0)->getElementsByTagName("style");
				// $style_all = [];
				// if ( $style && 0<$style->length ) {
				// 	for ($i=0; $i < $style->length; $i++) { 
				// 	    $item 	   = $style->item($i);
				// 	    $style_all[] = $doc->savehtml($item);
				// 	}
				// }

				// href and src
				if ($_replace_a_href_from != "" && $_replace_a_href_to != "") {
					$this->replace_href_and_src($doc,"a","href",$_replace_a_href_from,$_replace_a_href_to);
				}
				if ($_replace_css_href_from != "" && $_replace_css_href_to != "") {
					$this->replace_href_and_src($doc,"link","href",$_replace_css_href_from,$_replace_css_href_to);
				}
				if ($_replace_img_src_from != "" && $_replace_img_src_to != "") {
					$this->replace_href_and_src($doc,"img","src",$_replace_img_src_from,$_replace_img_src_to);
				}
				if($_replace_script_src_from != "" && $_replace_script_src_to != ""){
					$this->replace_href_and_src($doc,"script","src",$_replace_img_src_from,$_replace_img_src_to);
				}

				// Select by title
				$elements = $doc->getElementsByTagName($_title_by_tag);
				if ($_title_by == "tag" && $elements->length > 0) {
					for ($i = 0; $i < $elements->length; $i++) {
						$attr = $elements->item($i)->getAttribute($_title_by_att);
						if ($attr == $_title_by_val) {
							$name = $elements->item($i)->textContent;
						}
					}
				}


				// Select by content
				$body_all = [];
				$elements = $doc->getElementsByTagName($_content_by_tag);
				if ($_content_by == "tag" && $elements->length > 0) {
					for ($i = 0; $i < $elements->length; $i++) {
						$attr = $elements->item($i)->getAttribute($_content_by_att);
						if ($attr == $_content_by_val) {
							$body 	     = $elements->item($i);
					    	$body_all[]  = $doc->savehtml($body);
						}
					}
				}else{ ///body
					$body = $doc->getElementsByTagName('body')->item(0)->getElementsByTagName("*");
					if ( $body && 0<$body->length ) {
					    $body 	     = $body->item(0);
					    $body_all[]  = $doc->savehtml($body);
					}
				}

				$return = $this->html2wp_insert($body_all,$name);
				wp_send_json($return);die;
			}else if($_POST["type"] == "html_reconvert"){
				$post_id = sanitize_text_field($_POST["post_id"]);
				$cid 	 = sanitize_text_field($_POST["c_id"]);
				$this->remove_converted($cid);
				delete_post_meta($post_id,"_wp_page_template");
				$st = wp_delete_post($post_id,true);
				return $st;die();
			}
		}
	}

	/**
	 * replace href and src urls
	 *
	 * @since    1.0.0
	 */
	public function replace_href_and_src($doc,$el,$att,$_replace__from,$_replace__to){

		$links = $doc->getElementsByTagName('body')->item(0)->getElementsByTagName($el);
		if ( $links && 0<$links->length ) {
			for ($i=0; $i < $links->length; $i++) {
			    $link 	   	   = $links->item($i)->getAttribute($att);
			    $new_link      = str_replace($_replace__from,$_replace__to,$link);
			    $links->item($i)->setAttribute($att, $new_link);
			    $item 	   	   = $links->item($i);
			    $doc->savehtml($item);
			}
		}
	}


	/**
	 * Domain without subdomain
	 * @since    1.0.0
	 */
	public function get_domain($url) {
		$array = explode(".", $url);
    	return (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : "").".".$array[count($array) - 1];
	}

	/**
	 * Download image and upload to media library
	 * @since    1.0.0
	 */
	public function downloadAndUpload($url) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
		$imageurl = $url;
		$response = wp_remote_get( $url );
		$status_code = wp_remote_retrieve_response_code($response);
		if( $status_code != 200) {
		   return 0;
		}
		$contents    = wp_remote_retrieve_body( $response );

		$imagetype = pathinfo(
		    parse_url($imageurl, PHP_URL_PATH), 
		    PATHINFO_EXTENSION
		);

		$uniq_name = date('dmY').''.(int) microtime(true); 
		$filename = $uniq_name.'.'.$imagetype;

		$uploaddir = wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;

		$savefile = fopen($uploadfile, 'w');
		if (!$savefile) {
			return 0;
		}
		fwrite($savefile, $contents);
		fclose($savefile);

		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title' => $filename,
		    'post_content' => '',
		    'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $uploadfile );
		$imagenew  = get_post( $attach_id );
		$fullsizepath = get_attached_file( $imagenew->ID );
		$attach_data  = @wp_generate_attachment_metadata( $attach_id, $fullsizepath );

		// wp_update_attachment_metadata( $attach_id, $attach_data ); 

		return  wp_get_attachment_url($attach_id);
	  
	}

	/**
	 * url is external
	 *
	 * @since    1.0.0
	 */
	public function isexternal($url) {
	  $domain = $this->get_domain($_SERVER['HTTP_HOST']);
	  $components = parse_url($url);
	  if ( empty($components['host']) ) return false;  // we will treat url like '/relative.php' as relative
	  if ( strcasecmp($components['host'], $domain) === 0 ) return false; // url host looks exactly like the local host
	  return strrpos(strtolower($components['host']), '.'.$domain) !== strlen($components['host']) - strlen('.'.$domain); // check if the url host is a subdomain
	}

	/**
	 * html list variables
	 *
	 * @since    1.0.0
	 */
	public function get_wp_pages(){
		$sql = $this->db->prepare("SELECT ID,post_title,post_parent FROM {$this->db->posts} WHERE post_type=%s AND post_status=%s",["page","publish"]);
		$pages = $this->db->get_results($sql,OBJECT_K);
		$sort  = $pages;
		$remove = [];
		foreach ($pages as $key => $val) {
			if (!property_exists($val, "lvl")) {
				$val->lvl = 1;
			}
			if ($val->post_parent != 0) {	
				$remove[] = $key;
				if (array_key_exists($val->post_parent, $sort)){
					if (property_exists($sort[$val->post_parent], "lvl")) {
						$sort[$val->post_parent]->lvl ++;
						$val->lvl += $sort[$val->post_parent]->lvl;
					}
					$filter[$val->ID] = $val->lvl;
					$sort[$val->post_parent]->child = $val;
				}
			}
		}
		foreach ($remove as $key) {
			unset($sort[$key]);
		}
		return $sort;
	}

	/**
	 * wp get page tempaltes
	 *
	 * @since    1.0.0
	 */
	public function get_page_templates(){
		$arr = [];
		if(function_exists("get_page_templates")){
			$arr = get_page_templates();
		}
		return $arr;
	}

	/**
	 * wp users by roles
	 *
	 * @since    1.0.0
	 */
	public function users_by_roles($roles){
		$data  = get_users( ["role__in"=>$roles] );
		$users = [];
		foreach ($data as $key) {
			$users[] = $key->data;
		}
		return $users;
	}

	/**
	 * save import settings
	 *
	 * @since    1.0.0
	 */
	public function save_import_settings($data){
		foreach ($data as $key => $value) {
			$value = trim(str_replace(["'",'"'], ["",""], $value));
			update_option($this->html2wp."_".$key, $value, false);
		}
	}

	/**
	 * get import settings
	 *
	 * @since    1.0.0
	 */
	public function get_import_options(){
		$_type     = get_option($this->html2wp."_type");
		$_parent   = get_option($this->html2wp."_parent");
		$_template = get_option($this->html2wp."_template");
		$_status   = get_option($this->html2wp."_status");
		$_author   = get_option($this->html2wp."_author");
		$_replace_a_href_from     = get_option($this->html2wp."_replace_a_href_from");
		$_replace_a_href_to       = get_option($this->html2wp."_replace_a_href_to");
		$_replace_css_href_from     = get_option($this->html2wp."_replace_css_href_from");
		$_replace_css_href_to       = get_option($this->html2wp."_replace_css_href_to");
		$_replace_img_src_from	= get_option($this->html2wp."_replace_img_src_from");
		$_replace_img_src_to	= get_option($this->html2wp."_replace_img_src_to");
		$_replace_script_src_from = get_option($this->html2wp."_replace_script_src_from");
		$_replace_script_src_to	  = get_option($this->html2wp."_replace_script_src_to");
		$_content_by		= get_option($this->html2wp."_content_by");
		$_content_by_tag	= get_option($this->html2wp."_content_by_tag");
		$_content_by_att	= get_option($this->html2wp."_content_by_att");
		$_content_by_val	= get_option($this->html2wp."_content_by_val");
		$_title_by			= get_option($this->html2wp."_title_by");
		$_title_by_tag		= get_option($this->html2wp."_title_by_tag");
		$_title_by_att		= get_option($this->html2wp."_title_by_att");
		$_title_by_val		= get_option($this->html2wp."_title_by_val");
		return [
			"_type" 	=> $_type?$_type:"page",
			"_parent"   => $_parent?$_parent:0,
			"_template" => $_template?$_template:"default",
			"_status"   => $_status?$_status:"publish",
			"_author"   => $_author?$_author:get_current_user_id(),
			"_replace_a_href_from"   => $_replace_a_href_from,
			"_replace_a_href_to" 	 => $_replace_a_href_to,
			"_replace_css_href_from" => $_replace_css_href_from,
			"_replace_css_href_to" 	 => $_replace_css_href_to,
			"_replace_img_src_from"  => $_replace_img_src_from,
			"_replace_img_src_to" 	 => $_replace_img_src_to,
			"_replace_script_src_from" => $_replace_script_src_from,
			"_replace_script_src_to"   => $_replace_script_src_to,
			"_content_by" 		 => $_content_by,
			"_content_by_tag" 	 => $_content_by_tag,
			"_content_by_att" 	 => $_content_by_att,
			"_content_by_val" 	 => $_content_by_val,
			"_title_by" 		 => $_title_by,
			"_title_by_tag" 	 => $_title_by_tag,
			"_title_by_att" 	 => $_title_by_att,
			"_title_by_val" 	 => $_title_by_val
		];
	}

	/**
	 * wp page walker
	 *
	 * @since    1.0.0
	 */
	public function page_walker($pages,$lvl,$sel){
		$space ="";
		for ($i=1; $i < $lvl ; $i++) { 
			$space .= "&mdash;";
		}
		$select = $sel == $pages->ID? "selected":"";
		$this->walker_items[] =  '<option '.$select.' data-lvl="'.$lvl.'" value="'.$pages->ID.'">'.$space.$pages->post_title.'</option>';
		if (property_exists($pages, "child")) {
			$this->page_walker($pages->child,++$lvl,$sel);
		}
	}

	/**
	 * html list variables
	 *
	 * @since    1.0.0
	 */
	public function html_list_variables($converteds){
		$vars = [
		 	"wp_slug"  => "",
		 	"btn_name"  => "Convert",
		 	"btn_class" => "convert_html_files",
		 	"cid"		=> 0,
		 	"post_id"	=> 0,
		 	"btn_title" => "Convert To WP Page"
		];
		if (!empty($converteds)) {
		 	$vars["wp_slug"]   = get_permalink($converteds->post_id);
		 	$vars["btn_name"]  = "Reconvert";
		 	$vars["btn_class"] = "html_converted";
		 	$vars["cid"]  	   =  $converteds->id;
		 	$vars["post_id"]   =  $converteds->post_id;
		 	$vars["btn_title"] = "Remove WP Page";
		}
		return $vars;
	}



	/**
	 * remove converted data
	 *
	 * @since    1.0.0
	 */
	public function remove_converted($id){
		$row = $this->db->delete($this->table_converteds,["id"=>$id]);
		return $row;
	}

	/**
	 * get converted data
	 *
	 * @since    1.0.0
	 */
	public function get_converteds($by,$val){
		$row = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table_converteds} WHERE {$by}=%s",[$val]));
		return $row;
	}

	/**
	 * show success message with timeout
	 *
	 * @since    1.0.0
	 */
	public function success_message($key){
		if (array_key_exists($key, $this->timeout_message) && $this->timeout_message[$key] >= time()) {
			return "Successfully Imported";
		}
		return false;
	}

	/**
	 * Upload html files
	 *
	 * @since    1.0.0
	 */
	public function upload_html_files(){
		$type = $_FILES["local_importing"]["type"][0] == "text/html";
		if ($type) {
			foreach ($_FILES["local_importing"]["error"] as $key => $err) {
				if ($err == UPLOAD_ERR_OK) {
					$tmp_name = sanitize_text_field($_FILES["local_importing"]["tmp_name"][$key]);
					$fullname = sanitize_text_field(basename($_FILES["local_importing"]["name"][$key]));
					$info 	  = pathinfo($fullname);
					$name     = $info["filename"];
					$ext      = ".".$info["extension"];
					if (!file_exists($this->uploads_basedir)) {
						$st = mkdir($this->uploads_basedir);
						if (!$st) {
							$this->wp_error->add('upload','Your server dont allow to create folder:(');
						}
					}
					$ind  = 1;
					$part = '';
					while (file_exists($this->uploads_basedir."/".$name.$part.$ext)){
						$ind++;
						$part = "-".$ind;
					}
					$status = move_uploaded_file($tmp_name, $this->uploads_basedir."/".$name.$part.$ext);
					if (!$status) {
						$this->wp_error->add('upload','Failed when trying to upload :(');
					}else{
						$time = 5;
						$timeout = $time + time();
						$this->timeout_message["import_success"] = $timeout;
					}
					
				}
			}
		}else{
			$this->wp_error->add('upload','File type is not html :(');
		}
	}

	/**
	 * Inserting html to wp
	 *
	 * @since    1.0.0
	 */
	public function html2wp_insert($content,$title){
		$content = implode("", $content);
		$get_options = extract($this->get_import_options());
		$data = array(
	        'comment_status' => 'close',
	        'ping_status'    => $_status,
	        'post_author'    => $_author,
	        'post_title'     => ucwords($title),
	        'post_name'      => strtolower(str_replace(' ', '-', trim($title))),
	        'post_status'    => 'publish',
	        'post_content'   => $content,
	        'post_type'      => $_type,
	        'post_parent'    => "0"
	    );

		$data['post_parent'] = $_type == "page"? $_parent:0;

		$page_id = wp_insert_post($data);
	    if ($page_id) {
	    	if ($_type == "page") {
	    		update_post_meta($page_id,"_wp_page_template",$_template);
	    	}
			$this->db->insert($this->table_converteds, array(
			    'post_id' 	  => $page_id,
			    'html_name'   => $title
			));
			$slug = get_permalink($page_id);
		    return ["post_id"=>$page_id,"insert_id"=>$this->db->insert_id,"slug"=>$slug];
	    }
	    return false;

	}


	public function get_html_files(){
		 $files = glob($this->uploads_basedir."/*.html");
		 $arr   = [];
		 if (!empty($files)) {
		 	foreach ($files as $key) {
		 		$content   = file_get_contents($key);
		 		if ($content && $content != "") {
			 		$arr[] = [
			 			"name"    => basename($key,".html"),
			 			"path"    => $key,
			 			"content" => $content
			 		];
		 		}
		 	}
		 }
		 return $arr;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->html2wp, plugin_dir_url( __FILE__ ) . 'css/html2wp-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->html2wp, plugin_dir_url( __FILE__ ) . 'js/html2wp-admin.js', array( 'jquery' ), $this->version, false );
	}


	/**
	 * Add body class to admin
	 */
	public function add_admin_body_classes($classes) {
        $classes  .= ' html2wp';
        return $classes;
	}

	/**
	 * Register a custom menu page.
	 */
	public function html_import_page() {
	    add_menu_page(
	        "HTML2WP Import",
	        "HTML2WP Import",
	        'manage_options',
	        'html2wp-import',
	        [$this,'html_import_page_call'],
	        "dashicons-html",
	        6
	    );
	    add_submenu_page(
	    	"html2wp-import",
	    	"HTML List",
	    	"HTML List",
	    	"manage_options",
	    	"html2wp-list",
	    	[$this,"html_list_page_call"]
	    );
	    add_submenu_page(
	    	"html2wp-import",
	    	"Import Settings",
	    	"Settings",
	    	"manage_options",
	    	"html2wp-settings",
	    	[$this,"html_settings_page_call"]
	    );
	}
	public function html_import_page_call(){
		include "partials/html2wp-admin-display.php";
	}
	/**
	 * Import list page
	 */
	public function html_list_page_call(){
		include "partials/html2wp-list-display.php";
	}

	/**
	 * Import settings page
	 */
	public function html_settings_page_call(){
		include "partials/html2wp-settings-display.php";
	}


}
