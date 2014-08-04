<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class pw_woocommerc_brans_WC_Admin_Tabs {

	public $tab; 
	public $options; 
	
	/**
	 * Constructor
	 */
	public function __construct() {

		$this->options = $this->pw_woocommerce_brands_plugin_options();
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'pw_woocommerce_brands_add_tab_woocommerce' ) );
		add_filter( 'woocommerce_page_settings', array( $this, 'pw_woocommerce_brands_add_page_setting_woocommerce' ) );
		add_action( 'woocommerce_update_options_pw_woocommerce_brands', array( $this, 'pw_woocommerce_brands_update_options' ) );
		add_action( 'woocommerce_admin_field_upload', array( $this, 'admin_fields_upload' ) );
		add_action( 'woocommerce_update_option_upload', array( $this, 'admin_update_option' ) );
		add_action( 'woocommerce_settings_tabs_pw_woocommerce_brands', array( $this, 'pw_woocommerce_brands_print_plugin_options' ) );

	}
	function pw_woocommerce_brands_add_tab_woocommerce($tabs){
		$tabs['pw_woocommerce_brands'] = __('Brands','woocommerce-brands'); // or whatever you fancy
		return $tabs;
	}
	
	
	/**
	 * Update plugin options.
	 * 
	 * @return void
	 * @since 1.0.0
	 */
	public function pw_woocommerce_brands_update_options() {
		foreach( $this->options as $option ) {
			woocommerce_update_options( $option );   
		}
	}
	
	/**
	 * Add the select for the Woocommerce Brands page in WooCommerce > Settings > Pages
	 * 
	 * @param array $settings
	 * @return array
	 * @since 1.0.0
	 */
	public function pw_woocommerce_brands_add_page_setting_woocommerce( $settings ) {
		unset( $settings[count( $settings ) - 1] );
		
		$settings[] = array(
			'name' => __( 'Wishlist Page', 'woocommerce-brands' ),
			'desc' 		=> __( 'Page contents: [pw_woocommerce_brands]', 'woocommerce-brands' ),
			'id' 		=> 'pw_woocommerce_brands_page_id',
			'type' 		=> 'single_select_page',
			'std' 		=> '',         // for woocommerce < 2.0
			'default' 	=> '',         // for woocommerce >= 2.0
			'class'		=> 'chosen_select_nostd',
			'css' 		=> 'min-width:300px;',
			'desc_tip'	=>  false,
		);
		
		$settings[] = array( 'type' => 'sectionend', 'id' => 'page_options');
		
		return $settings;
	}

	
	
	
	public function pw_woocommerce_brands_print_plugin_options() {

		?>
		<div class="subsubsub_section">
			<br class="clear" />
			<?php foreach( $this->options as $id => $tab ) : ?>
			<div class="section" id="pw_woocommerce_brands_<?php echo $id ?>">
				<?php woocommerce_admin_fields( $this->options[$id] ) ;?>
			</div>
			<?php endforeach;?>
		</div>
		<?php
	}
	
	private function pw_woocommerce_brands_plugin_options() {
		$options['general_settings'] = array(
			array( 'name' => __( 'General Settings', 'woocommerce-brands' ), 'type' => 'title', 'desc' => '', 'id' => 'pw_woocommerce_brands_general_settings' ),		
			array(
				'title' => __( 'Display From', 'woocommerce-brands' ),
				'id' 		=> 'pw_woocommerce_brands_categories',
				'default'	=> 'no',
				'type' 		=> 'radio',
				'desc_tip'	=>  __( 'This option is show Categories or Brands.', 'woocommerce-brands' ),
				'options'	=> array(
					'no' => __( 'Brands', 'woocommerce-brands' ),
					'yes' => __( 'Categories (Pro Version)', 'woocommerce-brands' )
				),
			),					

			array( 'type' => 'sectionend', 'id' => 'pw_woocommerce_brands_general_settings' )
		);
		
		$options['brands_settings'] = array(
			array( 'name' => __( 'Brand`s Text Setting', 'woocommerce-brands' ), 'type' => 'title', 'desc' => '', 'id' => 'pw_woocommerce_brands_image_settings' ),

			array(
				'name'      => __( '', 'woocommerce-brands' ),
				'desc'      => __( 'Display Brand`s Text In Single Producut', 'woocommerce-brands'), 
				'id'        => 'pw_woocommerce_brands_text_single',
				'std' 		=> 'yes',         // for woocommerce < 2.0
				'default' 	=> 'yes',         // for woocommerce >= 2.0
				'type'      => 'checkbox'
			),			

			array( 'type' => 'sectionend', 'id' => 'pw_woocommerce_brands_image_settings' ),
			
			array( 'type' => 'sectionend'),

			array(	'title' => __( 'Pro Version', 'woocommerce' ), 'type' => 'title', 
			'desc' => '<p style="color:red;">To Buy Pro Version Please <a href="http://codecanyon.net/item/woocommerce-brands/8039481?ref=proword?ref=proword" target="blank">Click Here</a></P>Pro Version Feature:<br/>
			<ul>
		<li><strong>Shortcodes</strong>
			<ol>
				<li>Display All Brands with A-Z Filter</li>
				<li>Display Vertical Carousel (Vertical Slider)</li>
				<li>Display Horizontal Carousel (Horizaontal Slider)</li>
				<li>Display All Brands in Text Mode</li>
				<li>Display All Brands in Image Mode</li>
			</ol>
		</li>
		<li><strong>Extra Button</strong>
			<ol>
				<li>Display Brands with A-Z Filter in Extra Button (Left/Right Silde)</li>
			</ol>
		</li>	
		<li><strong>Widgets</strong>
		</li>
		<li><strong>Setting Page with Advanced Options</strong>
		</li>
		<li><strong>More Options</strong>
		</li>
	</ul>
			'),
		);			
		return apply_filters( 'pw_woocommerce_brands_tab_options', $options );	
	}
	
	
	/**
	 * Create new Woocommerce admin field: slider
	 * 
	 * @access public
	 * @param array $value
	 * @return void 
	 * @since 1.0.0
	 */
	public function admin_fields_upload( $value ) {
			$upload_value = ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) ? 
								esc_attr( stripslashes( get_option($value['id'] ) ) ) :
								esc_attr( $value['std'] );
								
			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo $value['name']; ?></label>
				</th>
			</tr>			
			<?php
	}

	/**
	* Save the admin field: slider
	*
	* @access public
	* @param mixed $value
	* @return void
	* @since 1.0.0
	*/
	public function admin_update_option($value) {
		update_option( $value['id'], woocommerce_clean($_POST[$value['id']]) );
	}

	
}
new pw_woocommerc_brans_WC_Admin_Tabs();
?>