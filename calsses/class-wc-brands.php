<?php

/**
 * WC_Brands class.
 */
class pw_woocommerc_brans_Wc_Brands {
	var $template_url;
	var $plugin_path;
	public function __construct() {
		$this->template_url = apply_filters( 'woocommerce_template_url', 'woocommerce/' );	 	
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_action( 'woocommerce_product_meta_end', array( $this,'pw_woocommerc_show_brand' )) ;
		
		add_action('restrict_manage_posts',array( $this,'restrict_listings_by_properties'));
		add_filter('parse_query', array( $this,'convert_id_to_term_in_query'));
	}

	/**
	 * show_brand function.
	 *
	 * @access public
	 * @return void
	 */
	 public function pw_woocommerc_show_brand() {
		global $post;

		if ( is_singular( 'product' ) ) {
			
				$get_terms="product_brand";
			
			if(get_option('pw_woocommerce_brands_show_categories')=="no"){
				$taxonomy = get_taxonomy( $get_terms); 
				$labels   = $taxonomy->labels;
				$tax=(get_option('pw_woocommerce_brands_text')=="" ? "Brand":get_option('pw_woocommerce_brands_text'));
				echo '<br/>';
				if(get_option('pw_woocommerce_brands_text_single')=="yes")
					echo $this->pw_woocommerc_get_brands( $post->ID, ', ', ' <span class="posted_in">' . $tax . ': ', '</span>' );
				else
					echo $tax .':';
				if(get_option('pw_woocommerce_brands_image_single')=="yes")
				{
					$brands = wp_get_post_terms( $post->ID, $get_terms, array( "fields" => "ids" ) );
					if($brands)
					{
						$thumbnail=get_woocommerce_term_meta($brands[0],'thumbnail_id', true);	
						if($thumbnail)
							$image = wp_get_attachment_thumb_url( $thumbnail );
						else
						{
							if(get_option('pw_woocommerce_brands_default_image'))
								$image=wp_get_attachment_thumb_url(get_option('pw_woocommerce_brands_default_image'));
							else
								$image = WP_PLUGIN_URL.'/woo-brands/img/default.png';							
						}													
						echo '<img src="'.$image.'"  alt="'. $labels->name.'" />';				
					}
				}						
			}
		}
	}
	/**
	 * get_brands function.
	 *
	 * @access public
	 * @param int $post_id (default: 0)
	 * @param string $sep (default: ')
	 * @param mixed '
	 * @param string $before (default: '')
	 * @param string $after (default: '')
	 * @return void
	 */
	 public function pw_woocommerc_get_brands( $post_id = 0, $sep = ', ', $before = '', $after = '' ) {
		global $post;

		if ( $post_id )
			$post_id = $post->ID;
			
		return get_the_term_list( $post_id, 'product_brand', $before, $sep, $after );
	}
	
	/**
	 * Get the plugin path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}	

	/**
	 * template_loader
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. woocommerce looks for theme
	 * overides in /theme/woocommerce/ by default
	 *
	 * For beginners, it also looks for a woocommerce.php template first. If the user adds
	 * this to the theme (containing a woocommerce() inside) this will be used for all
	 * woocommerce templates.
	 */
	public function template_loader( $template ) {

		$find = array( 'woocommerce.php' );
		$file = '';
		if ( is_tax( 'product_brand' ) ) {
			$term = get_queried_object();
			$file 		= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= $this->template_url . $file;
		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		}
		return $template;
	}

	///////////////ADD FILTER TO ADMIN LIST////////////////////
	public function restrict_listings_by_properties() {
	  global $typenow;
	  global $wp_query;
	  if ($typenow=='product') {
	   $taxonomy = 'product_brand';
	   $business_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
		'show_option_all' =>  __("Show All a {$business_taxonomy->label}"),
		'taxonomy'        =>  $taxonomy,
		'name'            =>  'product_brand',
		'orderby'         =>  'name',
		'selected'        =>  (isset( $wp_query->query['product_brand']) ? $wp_query->query['product_brand'] : ''),
		'hierarchical'    =>  true,
		'depth'           =>  3,
		'show_count'      =>  true, // Show # listings in parens
		'hide_empty'      =>  true, // Don't show businesses w/o listings
	   ));
	  }
	 }
		 
	public function convert_id_to_term_in_query($query) {
		global $pagenow;
		$post_type = 'product'; // change HERE
		$taxonomy = 'product_brand'; // change HERE
		$q_vars = &$query->query_vars;
		if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
			$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
			$q_vars[$taxonomy] = $term->slug;
		}
	}


}
new pw_woocommerc_brans_Wc_Brands();
?>