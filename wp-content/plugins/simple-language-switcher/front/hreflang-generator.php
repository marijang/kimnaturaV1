<?php

class SimpleLanguageSwitcher_Front_Hreflang_Generator {
	private $is_enabled;
	
	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_hreflang' ) );
		
		$options = get_option( 'sls_plugin_options' );
		$this->is_enabled = (int) $options['enable-sls'];
	}
	
	public function output_hreflang() {
		if(!$this->is_enabled) return;
		
		if( is_singular() ) {
			while( file_exists( SLS_PATH . 'links.php.lock' ) ) {}
			$filename = SLS_PATH . 'links.php';
			if( file_exists( $filename ) ) {
				$arr_links = include $filename;
				$post_id = get_the_ID();
				if( array_key_exists( $post_id, $arr_links ) ) {
					$post_id_other_langs = $arr_links[ $post_id ];
					if( !empty( $post_id_other_langs ) && count( $post_id_other_langs ) > 1 ) {
						foreach( $post_id_other_langs as $iso => $post_id_other_lang ) {
							$permalink = get_permalink( $post_id_other_lang );
							if( !empty( $permalink ) ) {
								echo '<link rel="alternate" href="' . $permalink . '" hreflang="' . $iso . '" />' . "\n";
							}
						}
					}
				}
			}
		}
	}
	
}

?>
