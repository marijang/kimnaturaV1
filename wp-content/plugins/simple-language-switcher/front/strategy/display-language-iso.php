<?php

class SimpleLanguageSwitcher_Front_Display_Language_Iso extends SimpleLanguageSwitcher_Front_Display_Strategy {
	
	public function __construct($separator, $regex_enabled) {
		parent::__construct($separator, $regex_enabled);
	}
	
	public function generate_switcher($arr_langs) {
		$this->print_switcher($arr_langs, 'lang-iso');
	}
	
}

?>
