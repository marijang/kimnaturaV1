<?php

namespace wp_gdpr\model;

use SessionHandler;
use wp_gdpr\lib\Gdpr_Customtables;
use wp_gdpr\lib\Gdpr_Container;
use wp_gdpr\lib\Gdpr_Options_Helper;
use wp_gdpr\lib\Session_Handler;

class Request_Form extends Form_Validation_Model {

	/**
	 * Request_Form constructor.
	 */
	public function __construct( $list_of_inputs ) {
		//here add functions to sanitize every input
		add_filter( 'gdpr_sanitize_email', array( $this, 'sanitize_email' ), 10 );
		add_filter( 'wp_mail_from', array( $this, 'set_mail_from' ), 10 );
		add_filter( 'wp_mail_from_name', array( $this, 'set_mail_from_name' ), 10 );

		parent::__construct( $list_of_inputs );
	}

	/**
	 * @param $input_value
	 *
	 * @return string
	 *
	 * this filter is triggered post_request function in form_validation_model
	 */
	public function sanitize_email( $input_value ) {
		return sanitize_email( $input_value );
	}

	public function set_mail_from( $original_email_address ) {
		return Gdpr_Options_Helper::get_dpo_email();
	}

	public function set_mail_from_name( $original_email_from ) {
		return get_bloginfo( 'name', true );
	}

	/**
	 * @param $list_of_inputs
	 *
	 * save request info in custom table
	 */
	public function after_successful_validation( $list_of_inputs ) {
		//save in database
		global $wpdb;

		$table_name        = $wpdb->prefix . Gdpr_Customtables::REQUESTS_TABLE_NAME;
		$single_address    = sanitize_email( $_REQUEST['email'] );
		$time_of_insertion = current_time( 'mysql' );
		$language          = $_REQUEST['gdpr_translation'];

		$key = 'gdpr_sended_request';
		if ( $this->check_token( $key, $single_address ) ) {
			//TODO security prevention
		} else {
			$this->set_token( $key, $single_address );
		}

		$wpdb->insert(
			$table_name,
			array(
				'email'     => $single_address,
				'status'    => 1,
				'timestamp' => $time_of_insertion,
				'language'  => $language,
			)
		);

		$dpo_email = Gdpr_Options_Helper::get_dpo_email();
		$this->send_email( $dpo_email, $time_of_insertion, $language );
		$this->send_email( $single_address, $time_of_insertion, $language );
		$this->redirect_to_page_gdpr_personal_data();
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return bool
	 * Check if token exist already in session.
	 */
	public function check_token( $key, $value ) {
		return Session_Handler::compare_with_saved_in_session( $key, $value );
	}

	public function set_token( $key, $value ) {
		Session_Handler::save_in_session( $key, $value );
	}

	/**
	 * @param $single_address
	 * @param $time_of_insertion
	 */
	public function send_email( $single_address, $time_of_insertion, $language ) {
		$to         = $single_address;
		$site_name  = get_bloginfo( 'name', true );
		$subject    = '[' . $site_name . '] ' . __( 'Your data request', 'wp_gdpr' );
		$controller = Gdpr_Container::make( 'wp_gdpr\controller\Controller_Menu_Page' );
		$content    = $controller->get_email_content( $single_address, $time_of_insertion, $language );
		$headers    = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $content, $headers );
	}

	public function redirect_to_page_gdpr_personal_data() {
		$url = self::get_personal_data_page_url( '?thank_you' );

		wp_redirect( $url );
		exit;
	}

	/**
	 * @return string
	 */
	public static function get_personal_data_page_url( $extra_parameter ) {
		$id = self::get_page_id();

		if ( false !== $id ) {
			$url = get_permalink( $id ) . $extra_parameter;
		} else {
			$url = get_home_url();
		}

		return $url;
	}

	public static function get_page_id() {
		global $wpdb;

		$query = "Select ID
					From {$wpdb->posts}
					Where 
					post_content like '%[REQ_CRED_FORM]%'
                  	and post_type = 'page'";

		$page_id = $wpdb->get_results( $query, ARRAY_N );

		if ( is_array( $page_id ) && isset( $page_id[0][0] ) ) {
			return $page_id[0][0];
		} else {
			return false;
		}
	}

	public function add_administrator_to_receivers( $to ) {
		$admin_email = get_option( 'admin_email', true );
		if ( $admin_email ) {
			return $to . ',' . $admin_email;
		} else {
			return $to;
		}
	}

	/**
	 *  do nothing when validation fail
	 */
	public function after_failure_validation( $list_of_inputs ) {
		//do nothing
	}

}
