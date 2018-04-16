<?php

/**
 * The settings of the plugin.
 *
 * @link       https://profiles.wordpress.org/marifamir/
 * @since      1.0.0
 *
 * @package    wpml-string-translation-importer
 * @subpackage wpml-string-translation-importer/admin
 */

/**
 * Class Wpml_String_Translation_Importer_Admin_Settings
 *
 */
class Wpml_String_Translation_Importer_Admin_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * This function introduces the menu in the WPML plugin settings.
	 */
	public function setup_plugin_options_menu() {

		add_submenu_page(
			'sitepress-multilingual-cms/menu/languages.php',
			'String Translation Importer',                    // The title to be displayed in the browser window for this page.
			'String Translation Importer',                    // The text to be displayed for this menu item
			'manage_options',                    // Which type of users can see this menu item
			'wpml-string-translation-importer',            // The unique ID - that is, the slug - for this menu item
			array(
				$this,
				'render_settings_page_content'
			)
		);

	}

	/**
	 * Renders a simple page for uploading the csv section
	 */
	public function render_settings_page_content() {

		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">
			<?php
			$this->dispatch();
			?>
		</div><!-- /.wrap -->
		<?php
	}

	/**
	 * Dispatch the different steps
	 */
	public function dispatch() {

		if ( empty ( $_GET['step'] ) ) {
			$step = 0;
		} else {
			$step = (int) $_GET['step'];
		}

		switch ( $step ) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				set_time_limit( 0 );
				$result = $this->import();
				if ( is_wp_error( $result ) ) {
					echo $result->get_error_message();
				}
				break;
		}

	}

	/**
	 * Main Plugin interface
	 */
	public function greet() {
		echo '<h1>' . __( 'WPML String Translation Importer', 'wpml-string-translation-importer' ) . '</h1>';
		echo '<p>' . __( 'Choose a CSV (.csv) file to upload, then click Upload file and import.', 'wpml-string-translation-importer' ) . '</p>';
		echo '<p>' . __( 'Requirements:', 'wpml-string-translation-importer' ) . '</p>';
		echo '<ol>';
		echo '<li>' . sprintf( __( 'You must use field delimiter as "%s"', 'wpml-string-translation-importer' ), Wpml_String_Translation_Helper::DELIMITER ) . '</li>';
		echo '<li>' . __( 'You must quote all text cells.', 'wpml-string-translation-importer' ) . '</li>';
		echo '<li>' . __( 'CSV must have these 4 fields.   word,translation,context,code', 'wpml-string-translation-importer' ) . '</li>';
		echo '</ol>';
		wp_import_upload_form( add_query_arg( 'step', 1 ) );

	}

	/**
	 * Process the csvs import functionality
	 */
	public function import() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wpml-string-translation-importer' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';

			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wpml-string-translation-importer' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'wpml-string-translation-importer' ), esc_html( $file['file'] ) );
			echo '</p>';

			return false;
		}

		$this->id   = (int) $file['id'];
		$this->file = get_attached_file( $this->id );
		$result     = $this->translate_strings();
		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	/**
	 * Translation the strings to the language
	 */

	public function translate_strings() {

		$h = new Wpml_String_Translation_Helper;

		$handle = $h->fopen( $this->file, 'r' );
		if ( $handle == false ) {
			echo '<p><strong>' . __( 'Failed to open file.', 'wpml-string-translation-importer' ) . '</strong></p>';
			wp_import_cleanup( $this->id );

			return false;
		}

		$is_first = true;

		echo '<ul>';

		while ( ( $data = $h->fgetcsv( $handle ) ) !== false ) {
			if ( $is_first ) {
				$h->parse_columns( $this, $data );
				$is_first = false;
			} else {
				echo '<li>';

				$word        = trim( $h->get_data( $this, $data, 'word' ) );
				$translation = trim( $h->get_data( $this, $data, 'translation' ) );

				$context = trim( $h->get_data( $this, $data, 'context' ) );
				$code    = trim( $h->get_data( $this, $data, 'code' ) );

				if ( ! empty( $word ) && ! empty( $translation ) ) {

					$word        = mb_convert_encoding( $word, mb_detect_encoding( $word ) );
					$translation = mb_convert_encoding( $translation, mb_detect_encoding( $translation ) );

					$translated_string_id = $this->add_string_translation( $word, $translation, $context, $code );
					if ( $translated_string_id ) {
						echo esc_html( sprintf( __( 'Processing "%s" done.', 'wpml-string-translation-importer' ), $word ) );
					}
				}
			}

			echo '</li>';
			wp_cache_flush();
		}

		echo '</ul>';
		$h->fclose( $handle );
		wp_import_cleanup( $this->id );
		echo '<h3>' . __( 'All Done.', 'wpml-string-translation-importer' ) . '</h3>';
	}

	/**
	 * Update WPML Strings
	 *
	 */
	public function add_string_translation( $word, $translation, $context, $code ) {

		$string_id = icl_get_string_id( $word, $context );
		if ( $string_id ) {
			$translated_string_id = icl_add_string_translation( $string_id, $code, $translation, 10 );

			return $translated_string_id;
		}

		return false;
	}

}