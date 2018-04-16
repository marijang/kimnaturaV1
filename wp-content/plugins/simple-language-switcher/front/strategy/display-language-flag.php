<?php

class SimpleLanguageSwitcher_Front_Display_Language_Flag extends SimpleLanguageSwitcher_Front_Display_Strategy {
	
	public function __construct($separator, $regex_enabled) {
		parent::__construct($separator, $regex_enabled);
	}
	
	public function generate_switcher($arr_langs) {
		$this->print_switcher($arr_langs, 'lang-iso');
	}
	
	public function print_link($text, $url = NULL) {
		$filename = SLS_URL . 'flags/' . $text . '.png';
		if( empty( $url ) ) {
			echo '<span id="current-lang" class="lang"><img src="' . $filename . '" alt="' . $text . '" /></span>';
		}
		else {
			echo '<span class="lang"><a href="' . $url . '"><img src="' . $filename . '" alt="' . $text . '" /></a></span>';
		}
	}
	
}

?>
