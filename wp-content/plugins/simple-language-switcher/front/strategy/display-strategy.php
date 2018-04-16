<?php

abstract class SimpleLanguageSwitcher_Front_Display_Strategy {
	private $separator;
	private $regex_enabled;
	
	public function __construct($separator, $regex_enabled) {
		$this->regex_enabled = $regex_enabled;
		$this->separator = str_replace(' ', '&nbsp;', $separator);
	}
	
	public function print_link($text, $url = NULL) {
		if( empty( $url ) ) {
			echo '<span id="current-lang" class="lang">' . $text . '</span>';
		}
		else {
			echo '<span class="lang"><a href="' . $url . '">' . $text . '</a></span>';
		}
	}
	
	public function print_switcher($arr_langs, $key) {
		$actual_link = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$matching_lang = NULL;
		if( $this->regex_enabled ) {
			$default_lang = NULL;
			foreach ( $arr_langs as $data ) {
				if( empty( $data['lang-regex'] ) ) {
					if( !empty( $default_lang ) ) {
						return;
					}
					$default_lang = $data['lang-iso'];
					continue;
				}
				preg_match($data['lang-regex'], $actual_link, $matches);
				if( !empty( $matches ) ) {
					if( !empty( $matching_lang ) ) {
						return;
					}
					$matching_lang = $data['lang-iso'];
				}
			}
			if( empty( $matching_lang ) ) {
				if( empty( $default_lang ) ) {
					return;
				}
				$matching_lang = $default_lang;
			}
		}
		
		$prefix = '';
		foreach ( $arr_langs as $data ) {
			echo $prefix;
			if( $data['lang-iso'] === $matching_lang ) {
				$this->print_link( $data[$key] );
			}
			else {
				$url = $data['lang-home-url'];
				if( is_singular() ) {
					while( file_exists( SLS_PATH . 'links.php.lock' ) ) {}
					$filename = SLS_PATH . 'links.php';
					if( file_exists( $filename ) ) {
						$arr_links = include $filename;
						$post_id = get_the_ID();
						if( array_key_exists( $post_id, $arr_links ) ) {
							$post_id_other_lang = $arr_links[ $post_id ];
							if( !empty( $post_id_other_lang ) ) {
								if( array_key_exists( $data['lang-iso'], $post_id_other_lang ) ) {
									$permalink = get_permalink( $post_id_other_lang[ $data['lang-iso'] ] );
									if( !empty( $permalink ) ) {
										$url = $permalink;
									}
								}
							}
						}
					}
				}
				$this->print_link( $data[$key], $url );
			}
			$prefix = $this->separator;
		}
	}
	
}

?>
