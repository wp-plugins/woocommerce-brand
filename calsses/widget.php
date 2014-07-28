<?php 
class pw_brands_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'pw_brands_Widget', // Base ID
			__('WooCommerce Brands', 'woocommerce-brands'), // Name
			array( 'description' => __( 'Display a list of your Brands on your site.', 'woocommerce-brands' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
			$get_terms="product_brand";
					
		$categories = get_terms( 'product_brand', 'orderby=name&hide_empty=0' );
			
			if ( ! empty( $categories ) ) {
				
				if($instance['show']=="dropdown")
				{
					
					wp_enqueue_script('woob-dropdown-script');

					?>
					<script type='text/javascript'>
                    /* <![CDATA[ */                    
						function onbrandsChange(value) {
							if(value=="")
								return false;
							window.location= "<?php echo home_url(); ?>/?<?php echo $get_terms;?>="+value;
						}
						
						jQuery(document).ready(function() {
//							jQuery("#payments").msDropdown({visibleRows:4});
							jQuery(".tech").msDropdown();	
			//				jQuery( '#carouselhor' ).elastislide(
			//					{
			//					 minItems : parseInt(jQuery( '#carouselhor' ).attr('title')),
			//					}
			//				);
						});
						/* ]]> */
                     </script>                    
					<?php
					echo '<select name="tech" class="tech" onchange="onbrandsChange(this.value)" >';
						 echo '<option value="">'. __('Please Select','woocommerce-brands').'</option>';	
					 foreach( (array) $categories as $term ) { 
					  $display_type = get_woocommerce_term_meta( $term->term_id, 'featured', true );
					  $count="";
					  if($instance['post_counts']==1)
					   $count='( '. esc_html( $term->count ) .' )  ';
							 
					  if($instance['featured']==1 && $display_type==1)
					  {
					  
					   echo'<option value="'.esc_html( $term->slug ).'" '.selected( esc_html ( get_query_var( 'product_brand' ) ) , esc_html( $term->slug ) , 1 ).'>'.esc_html( $term->name ).$count.'</option>';
					  }
					  elseif($instance['featured']==0)
					  {
					   echo '<option value="'.esc_html( $term->slug ).'" '.selected( esc_html ( get_query_var( 'product_brand' ) ) , esc_html( $term->slug ) , 1 ).'>'.esc_html( $term->name ).$count.'</option>';
					  }
					 }
					 echo '</select>';	
				}
			}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woocommerce-brands'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
            
		<p><label for="<?php echo $this->get_field_id('show'); ?>"><?php _e('Display Show:','woocommerce-brands'); ?></label>
            <select class='widefat' id="<?php echo $this->get_field_id('show'); ?>"
                    name="<?php echo $this->get_field_name('show'); ?>" type="text">
              <option value='dropdown' <?php selected( @$instance['show'] , "dropdown",1); ?>>
                Display DropDown
              </option>
              <option value='a-z' <?php selected( @$instance['show'] , "a-z",1); ?>>
                Display A-Z (Pro Version)
              </option>
            </select>
        </p>

		<p><input id="rss-show-summary" name="<?php echo $this->get_field_name('featured'); ?>" type="checkbox" value="1" <?php checked( @$instance['featured'], 1 ); ?> />
		<label for="rss-show-summary"><?php echo _e('Display Only featured?','woocommerce-brands'); ?></label></p>		
		<p><input id="rss-show-summary" name="<?php echo $this->get_field_name('post_counts'); ?>" type="checkbox" value="1" <?php checked( @$instance['post_counts'], 1 ); ?> />
		<label for="rss-show-summary"><?php echo _e('Show post counts','woocommerce-brands'); ?></label></p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['show'] = $new_instance['show'];		
		$instance['featured']     = isset($new_instance['featured'] ) ? (int) $new_instance['featured'] : 0;
		$instance['post_counts']     = isset($new_instance['post_counts'] ) ? (int) $new_instance['post_counts'] : 0;				
		return $instance;
	}
}

register_widget( 'pw_brands_Widget' );


class pw_brands_carousel_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'pw_brands_carousel_Widget', // Base ID
			__('WooCommerce Brands Carousel(Pro Version))', 'woocommerce-brands'), // Name
			array( 'description' => __( 'Display a list of your Brands on your site.', 'woocommerce-brands' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['carousel_title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'carousel_title' => '' ) );
		?>
		<p><label for="<?php echo $this->get_field_id('carousel_title'); ?>"><?php _e('Title:','woocommerce-brands'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('carousel_title'); ?>" name="<?php echo $this->get_field_name('carousel_title'); ?>" value="<?php if (isset ( $instance['carousel_title'])) {echo esc_attr( $instance['carousel_title'] );} ?>" /></p>
            
		<p><label for="<?php echo $this->get_field_id('carousel_type'); ?>"><?php _e('Carousel Type:','woocommerce-brands'); ?></label>
            <select class='widefat' id="<?php echo $this->get_field_id('carousel_type'); ?>"
                    name="<?php echo $this->get_field_name('carousel_type'); ?>" >
              <option value='hor-carousel' <?php selected( @$instance['carousel_type'] , "hor-carousel",1); ?>>
                Horizontal Carousel(Pro Version)
              </option>
              <option value='ver-carousel' <?php selected( @$instance['carousel_type'] , "ver-carousel",1); ?>>
                Vertical Carousel(Pro Version)
              </option>
            </select>
        </p>
		
        <p><label for="<?php echo $this->get_field_id('carousel_align'); ?>"><?php _e('Carousel Align:','woocommerce-brands'); ?></label>
            <select class='widefat' id="<?php echo $this->get_field_id('carousel_align'); ?>"
                    name="<?php echo $this->get_field_name('carousel_align'); ?>" >
              <option value='left' <?php selected( @$instance['carousel_align'] , "left",1); ?>>
                Left
              </option>
              <option value='center' <?php selected( @$instance['carousel_align'] , "center",1); ?>>
                Center
              </option>
              <option value='right' <?php selected( @$instance['carousel_align'] , "right",1); ?>>
                Right
              </option>
            </select>
        </p>
        
		<p><label for="rss-show-summary"><?php echo _e('Count of Items','woocommerce-brands'); ?></label>
        <input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_count_item'); ?>" type="number" value="<?php echo @$instance['carousel_count_item']; ?>" />
		</p>
        
        
        <p><label for="rss-show-summary"><?php echo _e('Item Per View','woocommerce-brands'); ?></label>
        <input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_per_view'); ?>" type="number" value="<?php echo @$instance['carousel_per_view']; ?>"/>
		</p>
       
        <p><input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_show_title'); ?>" type="checkbox" value="yes" <?php checked( @$instance['carousel_show_title'], "yes" ); ?> />
		<label for="rss-show-summary"><?php echo _e('Show Title?','woocommerce-brands'); ?></label></p>

		<p><input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_featured'); ?>" type="checkbox" value="yes" <?php checked( @$instance['carousel_featured'], "yes" ); ?> />
		<label for="rss-show-summary"><?php echo _e('Display Only featured?','woocommerce-brands'); ?></label></p>
             
		<p><input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_show_count'); ?>" type="checkbox" value="yes" <?php checked( @$instance['carousel_show_count'], "yes" ); ?> />
		<label for="rss-show-summary"><?php echo _e('Show post counts','woocommerce-brands'); ?></label></p>
		<?php 
	}
}

register_widget( 'pw_brands_carousel_Widget' );

?>