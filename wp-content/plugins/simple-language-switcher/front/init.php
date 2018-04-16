<?php

class SimpleLanguageSwitcher_Front_Init {
	private $generator;
	
	public function __construct() {
		
		require_once SLS_PATH . 'front/language-switcher-generator.php';
		$this->generator = new SimpleLanguageSwitcher_Front_Language_Switcher_Generator();
		
		require_once SLS_PATH . 'front/hreflang-generator.php';
		new SimpleLanguageSwitcher_Front_Hreflang_Generator();
		
		require_once SLS_PATH . 'front/language-switcher-widget.php';
		new SimpleLanguageSwitcher_Front_Language_Switcher_Widget();
		
	}
	
	public function getGenerator() {
		return $this->generator;
	}
	
}

?>
