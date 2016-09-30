<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Snap_Affiliate
 * @subpackage Snap_Affiliate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Snap_Affiliate
 * @subpackage Snap_Affiliate/public
 * @author     Your Name <email@example.com>
 */
class Snap_Affiliate_Public {

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
	 * @param      string    $snap_affiliate       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $snap_affiliate, $version ) {

		$this->plugin_name = $snap_affiliate;
		$this->version = $version;
		$this->prefix = 'saff_';
		
		global $wpdb;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/snap-affiliate-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/snap-affiliate-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function activate_cron(){
		$orgapikey = get_option('saff_cronkey');
		if(isset($_GET['sdaffcron']) && $_GET['sdaffcron'] == $orgapikey) {
			$this->update_products();
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
	
	private function get_sd_caturl($catnameval){
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
	
	private function update_products(){
		$args = array (	
				'post_type' => 'saffcampaign',
				'posts_per_page' => -1
			);
		
		$my_query = new WP_Query($args); 
		if ($my_query->have_posts()) {
			while ($my_query->have_posts()) : $my_query->the_post(); 
				$cpostid = get_the_ID();
				$maxproducts = get_post_meta($cpostid, $this->prefix . 'noproducts',true);
				$productsdata = $this->saff_import_front($cpostid,'saff_import');
				$productsdata = json_decode($productsdata);
				$catoptions = array();
//				var_dump($productsdata->{'products'});
				$ic = 0;
				foreach($productsdata->{'products'} as $prodata){
					if($ic < $maxproducts){
						$this->saff_import_data_front(json_encode($prodata),$cpostid,'saff_import_data');
					}
					$ic++;
				}
				//echo $productsdata;
			endwhile;
		}
	}
	
	private function saff_import_front($cpostid,$caction){
		$postid = $cpostid;
		$action = $caction;
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
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);			
			curl_close($ch);			
			return $response;
		}
	}
	
	private function saff_import_data_front($prodata,$campaignid,$action){
		$myJsonString = $prodata;	
		$array= array();	
		$array=json_decode($myJsonString, true);
		extract($array);
		$sku = $id;
		$Brand = "<strong>Brand: </strong>".$Brand."<br />";
		$Quantity = "<strong>Quantity: </strong>".$Quantity."<br />";
		$Product_Features = "<strong>Product Features: </strong>".$Product_Features."<br />";
		$Others = "<strong>Others: </strong>".$Others."<br />";
		
		$saff_avail_option = get_post_meta($campaignid, 'saff_avail_option',true);
		
		$procatarr = array();
		$procats = get_the_terms( $campaignid, 'product_cat' ); 		
		foreach ( $procats as $procatv ) {
			$procatarr[] = $procatv->term_id;
		}
		if($action == 'saff_import_data'){
			global $wpdb;
			$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );	
			if ( $product_id ) {
				$post_id = $product_id;
				$my_post = array(
					'post_content' => $description.$Brand.$Quantity.$Product_Features.$Others,
					'post_title' => $title,
					'ID' =>	$post_id
				);
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
			
			if($saff_avail_option == 'remove'){
				if($availability == 'out of stock'){
					wp_delete_post($post_id,true);					
				}
			}
			

		}
		
	}

}
