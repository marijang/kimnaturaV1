<?php

class SimpleLanguageSwitcher_Admin_Init {
	private $options_page;
	
	public function __construct() {
		
		require_once SLS_PATH . 'admin/options-page.php';
		$this->options_page = new SimpleLanguageSwitcher_OptionsPage();
	}
	
	public function process_plugin_activation() {
		$this->options_page->init_options();
	}
	
}

?>
