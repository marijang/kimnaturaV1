<?php

class SimpleLanguageSwitcher_Front_Language_Switcher_Generator {
	private $strategy;
	private $is_enabled;
	
	public function __construct() {
		add_action( 'simple_language_switcher', array( $this, 'output_switcher' ) );
		
		$options = get_option( 'sls_plugin_options' );
		$this->is_enabled = (int) $options['enable-sls'];
		$regex_enabled = (int) $options['enable-regex'];
		$strategy = $options['style'];
		$separator = $options['separator'];
		require SLS_PATH . 'front/strategy/display-strategy.php';
		switch( $strategy ) {
			case SimpleLanguageSwitcher::STYLE_LANG_ISO:
				require SLS_PATH . 'front/strategy/display-language-iso.php';
				$this->strategy = new SimpleLanguageSwitcher_Front_Display_Language_Iso($separator, $regex_enabled);
				break;
			case SimpleLanguageSwitcher::STYLE_LANG_NAME:
				require SLS_PATH . 'front/strategy/display-language-name.php';
				$this->strategy = new SimpleLanguageSwitcher_Front_Display_Language_Name($separator, $regex_enabled);
				break;
			case SimpleLanguageSwitcher::STYLE_LANG_FLAG:
				require SLS_PATH . 'front/strategy/display-language-flag.php';
				$this->strategy = new SimpleLanguageSwitcher_Front_Display_Language_Flag($separator, $regex_enabled);
				break;
		}
	}
	
	public function output_switcher() {
		if(!$this->is_enabled) return;
		while( file_exists( SLS_PATH . 'my-langs.php.lock' ) ) {}
		$filename = SLS_PATH . 'my-langs.php';
		if( file_exists( $filename ) ) {
			$arr_langs = include $filename;
			$this->strategy->generate_switcher($arr_langs);
		}
	}
	
}

?>
