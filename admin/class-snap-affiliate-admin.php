<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Snap_Affiliate
 * @subpackage Snap_Affiliate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Snap_Affiliate
 * @subpackage Snap_Affiliate/admin
 * @author     Your Name <email@example.com>
 */
class Snap_Affiliate_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $snap_affiliate    The ID of this plugin.
	 */
	private $snap_affiliate;

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
	 * @param      string    $snap_affiliate       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $snap_affiliate, $version ) {

		$this->plugin_name = $snap_affiliate;
		$this->version = $version;
		
		$this->prefix = 'saff_';
		$sdcatlist = $this->get_sd_categories();
		
		add_filter("plugin_action_links", array($this, 'renderPluginMenu'),10,2);
		
		$Brand ='';
		$brand ='';
		$Quantity ='';
		$Product_Features ='';
		$Others ='';
		$wp_error ='';
		$postre = '';
		$newcaturl;
		
		$avail_option = array();
		$avail_option['Remove unavailable product'] = 'remove';
        $avail_option['Change product stock status to "out of stock" for unavailable products.'] = 'change';
		
		$noproducts = array();
		$noproducts['10'] = '10';
		$noproducts['50'] = '50';
		$noproducts['100'] = '100';
		$noproducts['150'] = '150';
		$noproducts['200'] = '200';
		$noproducts['250'] = '250';
		$noproducts['300'] = '300';
		$noproducts['350'] = '350';
		$noproducts['400'] = '400';
		$noproducts['450'] = '450';
		$noproducts['500'] = '500';

		$this->meta_box = array(
			'id' => 'my-campaign-details',
			'title' => 'Campaign Details',
			'page' => 'saffcampaign',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(				
				array(
					'name' => 'Category',
					'id' => $this->prefix . 'sdcatlist',
					'type' => 'select',
					'options' => $sdcatlist
				),
				array(
					'name' => 'Number of Products to Import',
					'id' => $this->prefix . 'noproducts',
					'type' => 'select',
					'options' => $noproducts
				),
				
				array(
					'name' => 'Product Availability',
					'id' => $this->prefix . 'avail_option',
					'type' => 'select',
					'options' => $avail_option
				)
			)
		);	
	
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Snap_Affiliate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Snap_Affiliate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/snap-affiliate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Snap_Affiliate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Snap_Affiliate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/snap-affiliate-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function add_snap_affiliate_options(){
		add_options_page('Snap Affiliate Settings', 'Snap Affiliate', 'manage_options', 'snap-affiliate', array ( $this, 'snap_affiliate_options'));
	}
	
	function snap_affiliate_options(){
		if(get_option('saff_cronkey') !=''){
			$crkey = get_option('saff_cronkey');
		}else{
			$crkey = substr(sha1(rand()), 0, 5);
		}
	?>
		<div class="wrap">
			<h2>Snap Affiliate Settings</h2>
			<form method="post" action="options.php">
				<?php wp_nonce_field('update-options') ?>
                <p><strong>Snapdeal Feed URL:</strong><br />
					<input type="text" name="saff_feedurl" size="45" value="<?php echo get_option('saff_feedurl'); ?>" />
				</p>    
				<p><strong>Snapdeal Affiliate Id:</strong><br />
					<input type="text" name="saff_affid" size="45" value="<?php echo get_option('saff_affid'); ?>" />
				</p>
                <p><strong>Snapdeal Token Id:</strong><br />
					<input type="text" name="saff_afftokenid" size="45" value="<?php echo get_option('saff_afftokenid'); ?>" />
				</p> 
                <p><strong>Currency Symbol:</strong><br />
					<input type="text" name="saff_cursym" size="45" value="<?php echo get_option('saff_cursym'); ?>" />
				</p>    
                <p><strong>Button Text (Instead of Add to Cart):</strong><br />
					<input type="text" name="saff_btntxt" size="45" value="<?php echo get_option('saff_btntxt'); ?>" />
				</p>   
                <p><strong>Unique Cron Key:</strong><br />
					<input type="text" name="saff_cronkey" size="45" value="<?php echo $crkey; ?>" />
				</p>       
                <em>Your Unique Cron Key above may be a alphanumeric string. To update your Products Data everyday, Create a Cron Job in your Hosting Control Panel to run this URL.<?php echo get_bloginfo('wpurl'); ?>/?sdaffcron=YOUR_UNIQUE_CRON_KEY</em>
				<p><input type="submit" name="Submit" class="button button-primary button-large" value="Save Settings" /></p>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="saff_feedurl,saff_affid,saff_afftokenid,saff_btntxt,saff_cronkey,saff_cursym" />
			</form>
		</div>
        <?php
	}	
	

	public function renderPluginMenu($links, $plugin_file) {
		$thisFile = basename(__FILE__);
        if (basename($plugin_file) == 'snap-affiliate.php') {
           $l = '<a href="' . admin_url( 'options-general.php?page=snap-affiliate' ) . '">Settings</a>';
            array_unshift($links, $l);
        }
        return $links;
		

	}


	public function snap_affiliate_campaign_post_type() {
		$labels = array(
			'name'                => _x( 'Campaigns', 'Post Type General Name', 'text_domain' ),
			'singular_name'       => _x( 'Campaign', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'           => __( 'Snap Affiliate', 'text_domain' ),
			'parent_item_colon'   => __( 'Parent Campaign:', 'text_domain' ),
			'all_items'           => __( 'All Campaign', 'text_domain' ),
			'view_item'           => __( 'View Campaign', 'text_domain' ),
			'add_new_item'        => __( 'Add New Campaign', 'text_domain' ),
			'add_new'             => __( 'New Campaign', 'text_domain' ),
			'edit_item'           => __( 'Edit Campaign', 'text_domain' ),
			'update_item'         => __( 'Update Campaign', 'text_domain' ),
			'search_items'        => __( 'Search Campaigns', 'text_domain' ),
			'not_found'           => __( 'No Campaigns found', 'text_domain' ),
			'not_found_in_trash'  => __( 'No Campaigns found in Trash', 'text_domain' ),
		);
	
		$args = array(
			'label'               => __( 'Campaigns', 'text_domain' ),
			'description'         => __( 'Campaign posts', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array('title'),
			'taxonomies'          => array( 'product_cat'),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'rewrite' 			  => array( 'slug' => 'campaign' ),
			'menu_position'       => 80,
			'menu_icon'       	  => plugin_dir_url( __FILE__ )  . '/images/menu-icon.png',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'saffcampaign', $args );
	}	
	
	// Add meta box
	public function saff_campaign_add_box() {
		add_meta_box($this->meta_box['id'], $this->meta_box['title'], array( $this, 'saff_campaign_show_box' ), $this->meta_box['page'], $this->meta_box['context'], $this->meta_box['priority']);
	}
	
	// Callback function to show fields in meta box
	public function saff_campaign_show_box() {
		global $post,$postre;
	
		// Use nonce for verification
		echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	
		echo '<table class="form-table">';
	
		foreach ($this->meta_box['fields'] as $field) {
			// get current post meta data
			$meta = get_post_meta($post->ID, $field['id'], true);
	
			echo '<tr>',
					'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
					'<td>';
			switch ($field['type']) {
				case 'text':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
					break;
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $option => $optionval) {
						echo '<option value="'.$optionval.'" ', $meta == $optionval ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>';
					break;
				case 'radio':
					foreach ($field['options'] as $option) {
						echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
					}
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					break;
			}
			echo     '</td><td>',
				'</td></tr>';
		}
	
		echo '</table>';
		if (isset($_REQUEST['post'])) {
			$postre = isset($_REQUEST['post']) ? $_REQUEST['post'] : $_REQUEST['post'];
		}
		echo '<em>Note: More the number of Products, more the time to import.</em><br /><br /><em>';
		if(get_post_meta($postre,'last_import_on',true) != ''){
			echo 'Last Import on '.date('l jS \of F Y h:i:s A',get_post_meta($postre,'last_import_on',true)).'</em>';		
		}
		if(isset($postre)){
			echo '<br /><br /><input type="hidden" id="postidval" value="'.$postre.'" />
			<a href="#" id="runimport" class="button button-primary button-large">Run Import</a> 
			<div style="clear:both; height:20px;"></div>
			<div style="text-align:center;">
				<img src="'.plugin_dir_url( __FILE__ )  . '/images/loading.gif'.'" alt="" id="loader" style="display:none;"/>
				<em id="loadermsg" style="display:none;"><br />Please be patient, Your products are being Imported. Do not Close your browser or navigate, until you get an alert with the message "All Products Updated!"<br /><br /></em>
				<a href="#" id="showdetails" style="display:none;">Show Import Log</a>
			</div>		
			<div id="importresults" style="display:none;"></div>
		';
		}else{
			echo '<em>Please Select the above options and "Publish" the Campaign to run Import</em>';
		}
	}
	
	// Save data from meta box
	function saff_campaign_save_data($post_id ) {
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
	
		// check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
	
		foreach ($this->meta_box['fields'] as $field) {
			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
	
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		}
	}
	
	function get_http_response_code($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}
	
	function Generate_Featured_Image( $image_url, $post_id  ){
		global $wpdb;
		if($this->get_http_response_code($image_url) != '404'){
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents($image_url);
			$filename = basename($image_url);
	
			if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
			else                                    $file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents($file, $image_data);
		
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2= set_post_thumbnail( $post_id, $attach_id );

		}
	}	

	public function get_sd_categories(){
		$feedurl = get_option('saff_feedurl');
		if($feedurl != ''){
			$json = file_get_contents($feedurl);
			$obj = json_decode($json);
			$catoptions = array();
			foreach($obj->{'apiGroups'}->{'Affiliate'}->{'listingsAvailable'} as $catname => $caturl){
				$catmodname = str_replace('_', ' ' ,$catname);
				$catoptions[$catmodname] = $catname;
			}
			return $catoptions;
		}
	}
	
	private function get_sd_caturl($catnameval){
		global $newcaturl;
		$feedurl = get_option('saff_feedurl');
		if($feedurl != ''){
			$json = file_get_contents($feedurl);
			$obj = json_decode($json);
			$catoptions = array();
			foreach($obj->{'apiGroups'}->{'Affiliate'}->{'listingsAvailable'} as $catname => $caturl){
				if($catname == $catnameval){
					$newcaturl = $caturl->listingVersions->v1->get;
				}
			}
			return $newcaturl;
		}
	}
	
	private function saff_custom_currency_symbol( $currency_symbol, $currency ) {
		global $post;
		if($post->post_type == 'product' && get_post_meta($post->ID,'_snap_affiliate_product',true) == 'yes' && get_option('saff_cursym') != ''){
			$currency_symbol = get_option('saff_cursym');
		}else{
			$currency_symbol = get_option('saff_cursym').'t';
		}
		$currency_symbol = get_option('saff_cursym').'t';
		return $currency_symbol;
	}
	
	public function saff_import_callback(){
		$postid = $_POST['postid'];
		$action = $_POST['action'];
		//echo $postid;
		$saff_affid = get_option('saff_affid');
		$saff_afftokenid = get_option('saff_afftokenid');
		if($action == 'saff_import'){
			$catnameval = get_post_meta($postid, $this->prefix . 'sdcatlist',true);
			$caturl = $this->get_sd_caturl($catnameval);
			$ch = curl_init();
			update_post_meta($postid, 'last_import_on', time(), true);
			curl_setopt($ch, CURLOPT_URL, $caturl);
			curl_setopt(
				$ch, CURLOPT_HTTPHEADER,
				array(
					'Snapdeal-Affiliate-Id:'.$saff_affid.'',
					'Snapdeal-Token-Id:'.$saff_afftokenid.'',
					'Accept:application/json'
				)
			);
			
			$response = curl_exec($ch);			
			curl_close($ch);			
		}
		die();
	}
	
	public function saff_import_data_callback(){
		$myJsonString = stripslashes($_POST['myJsonString']);	
		$array= array();	
		$array=json_decode($myJsonString, true);
		//var_dump($array);

		global $Brand, $brand, $Quantity, $Product_Features, $Others,$wp_error;
		extract($array);
		$sku = $id;
		if($Brand !=''){
			$Brand = "<strong>Brand: </strong>".$Brand."<br />";
		}
		if($brand !=''){
			$Brand = "<strong>Brand: </strong>".$brand."<br />";
		}
		if($Quantity !=''){
			$Quantity = "<strong>Quantity: </strong>".$Quantity."<br />";
		}
		if($Product_Features !=''){
			$Product_Features = "<strong>Product Features: </strong>".$Product_Features."<br />";
		}
		if($Others !=''){
			$Others = "<strong>Others: </strong>".$Others."<br />";
		}
		
		$saff_avail_option = get_post_meta($campaignid, 'saff_avail_option',true);
		
		$procatarr = array();
		$procats = get_the_terms( $campaignid, 'product_cat' ); 		
		foreach ( $procats as $procatv ) {
			$procatarr[] = $procatv->term_id;
		}
		if($_POST['action'] == 'saff_import_data'){
			global $wpdb;
			$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );	
			if ( $product_id ) {
				$post_id = $product_id;
				$my_post = array(
					'post_content' => $description.$Brand.$Quantity.$Product_Features.$Others,
					'post_title' => $title,
					'ID' =>	$post_id
				);
				update_post_meta( $post_id, '_snap_affiliate_product', 'yes' );
				wp_update_post( $my_post );
			}else{
				$post = array(
					'post_content' => $description.$Brand.$Quantity.$Product_Features.$Others,
					'post_status' => "publish",
					'post_title' => $title,
					'post_parent' => '',
					'post_type' => "product",			
				);
				//Create post
				$post_id = wp_insert_post( $post, $wp_error );
			}
			
			if($availability != "out of stock"){
				$this->Generate_Featured_Image($imageLink,$post_id);				
			}
			
			wp_set_object_terms( $post_id, $procatarr, 'product_cat' );		
			wp_set_object_terms( $post_id, 'external', 'product_type' );			
	
			update_post_meta( $post_id, '_product_url', $link );
			update_post_meta( $post_id, '_button_text', get_option('saff_btntxt') );
			update_post_meta( $post_id, '_snap_affiliate_product', 'yes' );
			
			update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status', str_replace(" ","",$availability));
			update_post_meta( $post_id, '_regular_price', $mrp);
			update_post_meta( $post_id, '_price', $mrp);
			
			if($offerPrice){
				update_post_meta( $post_id, '_sale_price', $offerPrice );
				update_post_meta( $post_id, '_price', $offerPrice);
			}
			update_post_meta( $post_id, '_featured', "no" );
			update_post_meta($post_id, '_sku', $id);
			update_post_meta( $post_id, '_product_attributes', array());
			update_post_meta( $post_id, '_manage_stock', "no" );
			update_post_meta( $post_id, '_stock', "" );
			
			if ( $product_id ) {	
				if($saff_avail_option == 'remove'){
					if($availability == 'out of stock'){
						wp_delete_post($post_id,true);
						echo '<p>"'.$title.'" Deleted as it is out of stock.</p>';
					}
					else{
						echo '<p>"'.$title.'" Updated Successfully!</p>';
					}
				}else{
					echo '<p>"'.$title.'" Updated Successfully!</p>';
				}
				
			}else{
				if($saff_avail_option == 'remove'){
					if($availability == 'out of stock'){
						wp_delete_post($post_id,true);
						echo '<p>"'.$title.'" Deleted or Not Added as it is out of stock.</p>';
					}else{
						echo '<p>"'.$title.'" Added Successfully!</p>';
					}
				}else{
					echo '<p>"'.$title.'" Added Successfully!</p>';
				}
			}
			

		}
		
		die();
	}	
}


