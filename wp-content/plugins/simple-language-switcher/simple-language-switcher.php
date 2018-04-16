<?php
/*
Plugin Name: Simple Language Switcher
Version: 1.1
Plugin URI: http://www.mendoweb.be/blog/wordpress-plugin-simple-language-switcher/
Description: A simple and lightweight language switcher.
Author: Mathieu Decaffmeyer
Author URI: http://www.mendoweb.be/blog/
License: GPLv3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if( !class_exists( 'SimpleLanguageSwitcher' ) ) {

define( 'SLS_URL', plugin_dir_url( __FILE__ ) );
define( 'SLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'SLS_FILE', __FILE__ );

if( !function_exists( 'simple_language_switcher' ) ) {
	function simple_language_switcher() {
		do_action( 'simple_language_switcher' );
	}
}

if( !function_exists( 'log_me' ) ) {
	function log_me($message) {
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log(print_r($message, true));
			} else {
				error_log($message);
			}
		}
	}
}

class SimpleLanguageSwitcher {
	const STYLE_LANG_ISO = 1;
	const STYLE_LANG_NAME = 2;
	const STYLE_LANG_FLAG = 3;
	
	private static $instance;
	private $admin;
	private $front;
	private $loaded = false;
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		register_activation_hook( SLS_FILE, array( $this, 'activate_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
	
	public function init() {
		if ( is_admin() ) {
			require SLS_PATH . 'admin/init.php';
			$this->admin = new SimpleLanguageSwitcher_Admin_Init();
		}
		require SLS_PATH . 'front/init.php';
		$this->front = new SimpleLanguageSwitcher_Front_Init();
		$this->loaded = true;
	}
	
	public function activate_plugin() {
		if( $this->loaded ) {
			/* 
				Plugin is trying to activate while it has already been activated previously. 
				This may happen in a multisite environment when a plugin has been actived for one or more sites but also for the whole network.
			*/
			trigger_error('Either activate the plugin per site or on the whole network, but not both ways!', E_USER_ERROR);
		}
		$this->init();
		if ( is_admin() ) {
			$this->admin->process_plugin_activation();
		}
	}
	
	public function getFront() {
		return $this->front;
	}
}

SimpleLanguageSwitcher::getInstance();

}

?>
