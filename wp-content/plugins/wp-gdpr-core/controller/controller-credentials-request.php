<?php

namespace wp_gdpr\controller;

use wp_gdpr\lib\Gdpr_Container;
use wp_gdpr\lib\Gdpr_Language;

class Controller_Credentials_Request {

	/**
	 * Controller_Credentials_Request constructor.
	 */
	public function __construct( $name ) {
		$this->add_form_shortcode( $name );

	}

	/**
	 * add shortcode to show form ( allows to send request for users )
	 *
	 * @param $name
	 */
	public function add_form_shortcode( $name ) {
		add_shortcode( $name, array( $this, 'get_form_html' ) );
	}

	/**
	 * @return string
	 */
	public function get_form_html() {
	    // get gdpr string
        $controller_menu_page = new \wp_gdpr\controller\Controller_Menu_Page();
        $privacy_policy_strings = $controller_menu_page->get_privacy_policy_strings();

        // get language
        $pieces = new Gdpr_Language();
        $pieces = $pieces->get_language();

		ob_start();
		include_once GDPR_DIR . 'view/front/form.php';
		return ob_get_clean();
	}


}
