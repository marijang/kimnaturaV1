<?php

/**
 * @link              https://profiles.wordpress.org/marifamir/
 * @since             1.0.0
 * @package           wpml_string_translation_importer
 *
 * Plugin Name:       WPML String Translation Importer
 * Plugin URI:        https://github.com/arifamir/wpml-string-translation-importer
 * Description:       WPML String Translation Importer is used to import wpml string translations to update their translations.
 * Version:           1.0.0
 * Author:            Muhammad Arif Amir
 * Author URI:        https://profiles.wordpress.org/marifamir/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpml-string-translation-importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpml-string-translation-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpml_string_translation_importer() {

	$plugin = new Wpml_String_Translation_Importer();
	$plugin->run();

}

run_wpml_string_translation_importer();
