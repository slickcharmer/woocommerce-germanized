<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_GZD_Admin_Legal_Checkboxes {

	/**
	 * Single instance of WooCommerce Germanized Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		add_filter( "woocommerce_gzd_legal_checkbox_terms_fields_before_titles", array(
			$this,
			'additional_terms_fields'
		), 10, 2 );
		add_filter( "woocommerce_gzd_legal_checkbox_age_verification_fields_before_titles", array(
			$this,
			'additional_age_verification_fields'
		), 10, 2 );
		add_filter( "woocommerce_gzd_legal_checkbox_service_fields_before_titles", array(
			$this,
			'additional_service_fields'
		), 10, 2 );
		add_filter( "woocommerce_gzd_legal_checkbox_download_fields_before_titles", array(
			$this,
			'additional_download_fields'
		), 10, 2 );
		add_filter( "woocommerce_gzd_legal_checkbox_parcel_delivery_fields_before_titles", array(
			$this,
			'additional_parcel_delivery_fields'
		), 10, 2 );
	}

	public function get_terms_policy_status_html() {
		$manager  = WC_GZD_Legal_Checkbox_Manager::instance();
		$checkbox = $manager->get_checkbox( 'terms' );

		if ( ! $checkbox ) {
			return;
		}

		$is_privacy_policy_inserted = false;

		$legal_text = $checkbox->get_label( true );

		if ( $legal_text && strpos( $legal_text, '{data_security_link}' ) !== false ) {
			$is_privacy_policy_inserted = true;
		}

		return '<p>' . ( wc_get_page_id( 'data_security' ) == - 1 ? '<span class="wc-gzd-status-text wc-gzd-text-red">' . __( 'Please choose a page as your privacy policy first.', 'woocommerce-germanized' ) . '</span>' : '<span class="wc-gzd-status-text wc-gzd-text-' . ( $is_privacy_policy_inserted ? 'green' : 'red' ) . '"> ' . ( $is_privacy_policy_inserted ? __( 'Found', 'woocommerce-germanized' ) : __( 'Not found within label.', 'woocommerce-germanized' ) ) . '</span> ' . ( ! $is_privacy_policy_inserted ? '<a class="button button-secondary" style="margin-left: 1em" href="' . admin_url( 'admin.php?page=wc-settings&tab=germanized&section=checkboxes&checkbox_id=terms#woocommerce_gzd_legal_checkboxes_settings_terms_label' ) . '">' . __( 'Adjust label', 'woocommerce-germanized' ) . '</a></p>' : '' ) );
	}

	public function additional_age_verification_fields( $fields, $checkbox ) {
		$fields = WC_GZD_Admin::instance()->insert_setting_after( $fields, $checkbox->get_form_field_id( 'is_enabled' ), array(
			array(
				'title'    => __( 'Global minimum age', 'woocommerce-germanized' ),
				'id'       => $checkbox->get_form_field_id( 'min_age' ),
				'type'     => 'select',
				'options'  => wc_gzd_get_age_verification_min_ages_select(),
				'desc_tip' => __( 'Choose a global minimum age necessary to buy your products. Can be overridden by product specific settings.', 'woocommerce-germanized' ),
			)
		) );

		return $fields;
	}

	public function additional_terms_fields( $fields, $checkbox ) {

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['id'] ) && $checkbox->get_form_field_id( 'label' ) === $field['id'] ) {
				$fields[ $key ]['desc'] = $field['desc'] . '<span class="gzd-small-desc">' . __( 'e.g. include your privacy policy: {data_security_page}Privacy Policy{/data_security_page}', 'woocommerce-germanized' ) . '</span>';
			}
		}

		$fields = WC_GZD_Admin::instance()->insert_setting_after( $fields, $checkbox->get_form_field_id( 'is_enabled' ), array(
			array(
				'title'    => __( 'Policy Status', 'woocommerce-germanized' ),
				'id'       => 'woocommerce_gzd_privacy_policy_status',
				'type'     => 'html',
				'desc_tip' => __( 'This option shows whether you have already embedded your privacy policy within your legal text.', 'woocommerce-germanized' ),
				'html'     => $this->get_terms_policy_status_html( $checkbox ),
			)
		) );

		return $fields;
	}

	public function additional_service_fields( $fields, $checkbox ) {
		$fields = array_merge( $fields, array(
			array(
				'title'    => __( 'Confirmation', 'woocommerce-germanized' ),
				'type'     => 'textarea',
				'id'       => $checkbox->get_form_field_id( 'confirmation' ),
				'css'      => 'width:100%; height: 65px;',
				'desc_tip' => __( 'This text will be appended to your order processing email if the order contains service products.', 'woocommerce-germanized' ),
				'desc'     => sprintf( __( 'To insert a link to your revocation page use the following placeholder: %s', 'woocommerce-germanized' ), '<code>{link}, {/link}</code>' ),
				'default'  => __( 'Furthermore you have expressly agreed to start the performance of the contract for services before expiry of the withdrawal period. I have noted to lose my {link}right of withdrawal{/link} with the beginning of the performance of the contract.', 'woocommerce-germanized' ),
			),
		) );

		return $fields;
	}

	public function additional_download_fields( $fields, $checkbox ) {

		$product_types = wc_get_product_types();

		$digital_type_options = array_merge( array(
			'downloadable' => __( 'Downloadable Product', 'woocommerce-germanized' ),
			'virtual'      => __( 'Virtual Product', 'woocommerce-germanized' ),
			'service'      => __( 'Service', 'woocommerce-germanized' )
		), $product_types );

		$fields = array_merge( $fields, array(
			array(
				'title'    => __( 'Confirmation', 'woocommerce-germanized' ),
				'type'     => 'textarea',
				'id'       => $checkbox->get_form_field_id( 'confirmation' ),
				'css'      => 'width:100%; height: 65px;',
				'desc_tip' => __( 'This text will be appended to your order processing email if the order contains digital products.', 'woocommerce-germanized' ),
				'desc'     => sprintf( __( 'To insert a link to your revocation page use the following placeholder: %s', 'woocommerce-germanized' ), '<code>{link}, {/link}</code>' ),
				'default'  => __( 'Furthermore you have expressly agreed to start the performance of the contract for digital items (e.g. downloads) before expiry of the withdrawal period. I have noted to lose my {link}right of withdrawal{/link} with the beginning of the performance of the contract.', 'woocommerce-germanized' ),
			),
			array(
				'title'    => __( 'Digital Product types', 'woocommerce-germanized' ),
				'desc'     => __( 'Select product types for which the loss of recission notice is shown. Product types like "simple product" may be redudant because they include virtual and downloadable products.', 'woocommerce-germanized' ),
				'desc_tip' => true,
				'id'       => $checkbox->get_form_field_id( 'types' ),
				'default'  => array( 'downloadable' ),
				'class'    => 'chosen_select',
				'options'  => $digital_type_options,
				'type'     => 'multiselect',
			),
		) );

		return $fields;
	}

	public function additional_parcel_delivery_fields( $fields, $checkbox ) {

		$shipping_methods_options = WC_GZD_Admin::instance()->get_shipping_method_instances_options();

		$fields = array_merge( $fields, array(
			array(
				'title'    => __( 'Show checkbox', 'woocommerce-germanized' ),
				'desc_tip' => __( 'Choose whether you like to always show the parcel delivery checkbox or do only show for certain shipping methods.', 'woocommerce-germanized' ),
				'id'       => $checkbox->get_form_field_id( 'show_special' ),
				'default'  => 'always',
				'class'    => 'chosen_select',
				'options'  => array(
					'shipping_methods' => __( 'For certain shipping methods.', 'woocommerce-germanized' ),
					'always'           => __( 'Always show.', 'woocommerce-germanized' ),
				),
				'type'     => 'select',
			),
			array(
				'title'    => __( 'Shipping Methods', 'woocommerce-germanized' ),
				'desc'     => __( 'Select shipping methods which are applicable for the Opt-In Checkbox.', 'woocommerce-germanized' ),
				'desc_tip' => true,
				'id'       => $checkbox->get_form_field_id( 'show_shipping_methods' ),
				'default'  => array(),
				'class'    => 'chosen_select',
				'options'  => $shipping_methods_options,
				'type'     => 'multiselect',
			),
		) );

		return $fields;
	}

	public function disable_include( $enable, $current_section ) {
		if ( 'checkboxes' === $current_section ) {
			return false;
		}

		return $enable;
	}
}

WC_GZD_Admin_Legal_Checkboxes::instance();