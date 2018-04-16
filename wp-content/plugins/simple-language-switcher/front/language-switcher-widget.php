<?php

class SimpleLanguageSwitcher_Front_Language_Switcher_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'simple-language-switcher', // Base ID
			'Simple Language Switcher', // Name
			array( 'description' => 'A simple language switcher that allows your users to switch between languges', ) // Args
		);
		add_action( 'widgets_init', array( $this, 'register_sls_widget' ) );
	}
	
	public function register_sls_widget() {
		register_widget( get_class($this) );
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		echo $args['before_widget'];
		SimpleLanguageSwitcher::getInstance()->getFront()->getGenerator()->output_switcher();
		echo $args['after_widget'];
	}

}

?>
