<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class pw_brans_WC_Admin_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'woocommerce_register_taxonomy', array( $this, 'create_taxonomies' ) );
		add_action( "delete_term", array( $this, 'delete_term' ), 5 );

		/* Add form */
		add_action( 'product_brand_add_form_fields', array( $this, 'add_brands_fields' ) );
		add_action( 'product_brand_edit_form_fields', array( $this, 'edit_brands_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save_brands_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_brands_fields' ), 10, 3 );

		/* Add columns */
		add_filter( 'manage_edit-product_brand_columns', array( $this, 'brands_columns' ) );
		add_filter( 'manage_product_brand_custom_column', array( $this, 'brands_column' ), 10, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		/* create radiobox */
		add_action( 'admin_menu',array( $this,  'pw_woocommerc_brands_remove_meta_box'));
		add_action( 'add_meta_boxes',array( $this,  'pw_woocommerc_brands_add_meta_box'));
	}

	public function pw_woocommerc_brands_remove_meta_box(){
	   remove_meta_box('product_branddiv', 'product', 'normal');
	}


	 public function pw_woocommerc_brands_add_meta_box() {
		 add_meta_box( 'mytaxonomy_id', 'Brands',array( $this,'pw_woocommerc_brands_metabox'),'product' ,'side','core');
	 }

	//Callback to set up the metabox
	public function pw_woocommerc_brands_metabox( $post ) {
		//Get taxonomy and terms
		$taxonomy = 'product_brand';
	 
		//Set up the taxonomy object and get terms
		$tax = get_taxonomy($taxonomy);
		$terms = get_terms($taxonomy,array('hide_empty' => 0));
	 
		//Name of the form
		$name = 'tax_input[' . $taxonomy . ']';
	 
		//Get current and popular terms
		$popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
		$postterms = get_the_terms( $post->ID,$taxonomy );
		$current = ($postterms ? array_pop($postterms) : false);
		$current = ($current ? $current->term_id : 0);
		?>
	 
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
	 
			<!-- Display tabs-->
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used','woocommerce-brands' ); ?></a></li>
			</ul>
	 
			<!-- Display taxonomy terms -->
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
					<?php   foreach($terms as $term){
						$id = $taxonomy.'-'.$term->term_id;
						echo "<li id='$id'><label class='selectit'>";
						echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
					   echo "</label></li>";
					}?>
			   </ul>
			</div>
	 
			<!-- Display popular taxonomy terms -->
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php   foreach($popular as $term){
						$id = 'popular-'.$taxonomy.'-'.$term->term_id;
						echo "<li id='$id'><label class='selectit'>";
						echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
						echo "</label></li>";
					}?>
			   </ul>
		   </div>
	 
		</div>
		<?php
	}

// create two taxonomies, genres and writers for the post type "product"
	public function create_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$shop_page_id = woocommerce_get_page_id( 'shop' );

		$base_slug = $shop_page_id > 0 && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';

		$category_base = get_option('woocommerce_prepend_shop_page_to_urls') == "yes" ? trailingslashit( $base_slug ) : '';

		$cap = version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ? 'manage_woocommerce_products' : 'edit_products';		
		$labels = array(
			'name'              => __( 'Brands', 'woocommerce-brands' ),
			'singular_name'     => __( 'Brands', 'woocommerce-brands' ),
			'search_items'      => __( 'Search Genres', 'woocommerce-brands' ),
			'all_items'         => __( 'All Brands', 'woocommerce-brands' ),
			'parent_item'       => __( 'Parent Brands', 'woocommerce-brands'),
			'parent_item_colon' => __( 'Parent Brands:', 'woocommerce-brands' ),
			'edit_item'         => __( 'Edit Brands', 'woocommerce-brands'),
			'update_item'       => __( 'Update Brands', 'woocommerce-brands'),
			'add_new_item'      => __( 'Add New Brands', 'woocommerce-brands'),
			'new_item_name'     => __( 'New Brands Name', 'woocommerce-brands'),
			'menu_name'         => 'Brand',
		);
	
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui' 				=> true,
			'show_in_nav_menus' 	=> true,
			'capabilities'			=> array(
				'manage_terms' 		=> $cap,
				'edit_terms' 		=> $cap,
				'delete_terms' 		=> $cap,
				'assign_terms' 		=> $cap
			),
			'rewrite' 				=> array( 'slug' => $category_base . __( 'brand', 'woocommerce-brands' ), 'with_front' => false, 'hierarchical' => true )
		);
		register_taxonomy( 'product_brand', array('product'), apply_filters( 'register_taxonomy_product_brand',$args ));	
	}  

	public function delete_term( $term_id ) {

		$term_id = (int) $term_id;

		if ( ! $term_id )
			return;

		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->woocommerce_termmeta} WHERE `woocommerce_term_id` = " . $term_id );
	}

	public function admin_scripts() {
			wp_enqueue_media();
	}
	
	public function add_brands_fields() {
			$image="";
		?>
		<div class="">
			<label for="display_type"><?php _e( 'Featured', 'woocommerce-brands' ); ?></label>
            <input type="checkbox" name="featured" />
		</div>
		<?php
	}

	public function edit_brands_fields( $term, $taxonomy ) {
		$display_type	= get_woocommerce_term_meta( $term->term_id, 'featured', true );
		$image 			= '';
		$thumbnail_id 	= absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
		if ( $thumbnail_id )
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		else
		{
			$image = wc_placeholder_img_src();	
		}
		?>
		<tr class="">
			<th scope="row" valign="top"><label><?php _e( 'Featured', 'woocommerce-brands' ); ?></label></th>
			<td>
	  			 <input type="checkbox" name="featured" <?php checked( $display_type, 1 ); ?>/>
			</td>
		</tr>
		<?php
	}


	public function save_brands_fields( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['featured'] ) ){

			update_woocommerce_term_meta( $term_id, 'featured', 1);
		}
		else{	
			update_woocommerce_term_meta( $term_id, 'featured', 0);
		}
		delete_transient( 'wc_term_counts' );
	}

	public function product_cat_description() {
		echo wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'woocommerce-brands' ) );
	}

	public function shipping_class_description() {
		echo wpautop( __( 'Shipping classes can be used to group products of similar type. These groups can then be used by certain shipping methods to provide different rates to different products.', 'woocommerce-brands' ) );
	}

	public function brands_columns( $columns ) {
			
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['name'] =__('Name','woocommerce-brands');
		$new_columns['featured'] = __( 'featured', 'woocommerce-brands' );
		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
		
	}

	public function brands_column( $columns, $column, $id ) {
		if($column=="featured"){
			$display_type	= get_woocommerce_term_meta( $id, 'featured', true );
			if($display_type=="1")
				$columns.= 'yes';
			else		
				$columns.= 'no';
			}

		return $columns;
	}
}
new pw_brans_WC_Admin_Taxonomies();
?>