<?php

if ( !is_admin() ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}
/*
if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
*/

class SimpleLanguageSwitcher_OptionsPage {
	
	function __construct() {
		add_action( 'update_option_sls_plugin_langs', array( $this, 'update_sls_plugin_langs' ), 10, 2 );
		add_action( 'update_option_sls_plugin_linking', array( $this, 'update_sls_plugin_linking' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_plugin_settings_menu' ) );
		add_action( 'admin_footer', array( $this, 'js_sls' ) );
		add_action( 'wp_ajax_remove_lang', array( $this, 'callback_remove_lang' ) );
		add_action( 'wp_ajax_remove_link', array( $this, 'callback_remove_link' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}
	
	function init_options() {
		$arr_langs = $this->get_array_langs();
		if( empty( $arr_langs ) ) {
			$this->write_file_langs( array() );
			$options = update_option( 'sls_plugin_langs', array() );
		}
		else {
			$arr_langs = get_option( 'sls_plugin_langs_backup' );
			if( !empty( $arr_langs ) ) $this->write_file_langs( $arr_langs );
		}
		
		$arr_links = $this->get_array_links();
		if( empty( $arr_links ) ) {
			$this->write_file_links( array() );
			$options = update_option( 'sls_plugin_linking', array() );
		}
		else {
			$arr_links = get_option( 'sls_plugin_linking_backup' );
			if( !empty( $arr_links ) ) $this->write_file_links( $arr_links );
		}
		
		$options = get_option( 'sls_plugin_options' );
		if( $options === FALSE ) {
			$options = array();
			$options['enable-regex'] = 0;
			$options['style'] = SimpleLanguageSwitcher::STYLE_LANG_ISO;
			$options['separator'] = '  |  ';
		}
		$options['enable-sls'] = 1;
		update_option( 'sls_plugin_options', $options );
	}
	
	function js_sls() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
	$('a.remove-lang').click(function() {
		$(this).hide();
		var data = {
			action: 'remove_lang',
			myLang: $(this).parent().children('.lang-iso').html()
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			window.location.reload();
		});
		return false;
	});
	$('a.remove-link').click(function() {
		$(this).hide();
		var data = {
			action: 'remove_link',
			linkPostId: $(this).parent().children('.link-post-id').html()
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			window.location.reload();
		});
		return false;
	});
});
</script>
<?php
	}
	
	function callback_remove_lang() {
		$lang = trim( $_POST['myLang'] );
		
		$arr_langs = $this->get_array_langs();
		
		unset( $arr_langs[$lang] );
		$this->write_file_langs( $arr_langs );
	}
	
	function callback_remove_link() {
		$postId = trim( $_POST['linkPostId'] );
		
		$arr_links = $this->get_array_links();
		
		unset( $arr_links[$postId] );
		$this->write_file_links( $arr_links );
	}
	
	function add_plugin_settings_menu() {
		// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
		add_options_page( 'Simple Language Switcher Plugin', 'Simple Language Switcher', 'manage_options', 'sls', array($this, 'create_plugin_settings_page') );
	}
	
	function create_plugin_settings_page() {
	?>
<div class="wrap">
	<?php screen_icon(); ?>
    <h2>Settings</h2>
    
    <form method="post" action="options.php">
    <?php
		// This prints out all hidden setting fields
		// settings_fields( $option_group )
		settings_fields( 'sls-settings-group-main' );
		// do_settings_sections( $page )
		do_settings_sections( 'sls-main' );
    ?>
    <?php submit_button('Save Changes'); ?>
    </form>
    
    <form method="post" action="options.php">
    <?php
		// This prints out all hidden setting fields
		// settings_fields( $option_group )
		settings_fields( 'sls-settings-group-langs' );
		// do_settings_sections( $page )
		do_settings_sections( 'sls-langs' );
    ?>
    <?php submit_button('Add language'); ?>
    </form>
    
    <h3>List Languages</h3>
    <p>Your website's languages</p>
    <?php
		$arr_langs = $this->get_array_langs();
		if( empty( $arr_langs ) ) {
	?>
    <p style="margin-left:50px;font-style:italic">No languages found</p>
    <?php
		}
		else {
			foreach ( $arr_langs as $data ) {
	?>
            <div class="lang-line">
                [<span class="lang-iso"><?php echo $data['lang-iso']; ?></span>]
                <span class="lang-name"><?php echo $data['lang-name']; ?></span> 
                <a href="#" class="remove-lang">remove</a>
            </div>
    <?php
			}
		}
	?>
    <br />
    
    <form method="post" action="options.php">
    <?php
		// This prints out all hidden setting fields
		// settings_fields( $option_group )
		settings_fields( 'sls-settings-group-linking' );
		// do_settings_sections( $page )
		do_settings_sections( 'sls-linking' );
    ?>
    <?php submit_button('Interconnect posts'); ?>
    </form>
    
    <h3>List Posts Linking</h3>
    <p></p>
    <?php
		$arr_links = $this->get_array_links();
		if( empty( $arr_links ) ) {
	?>
    <p style="margin-left:50px;font-style:italic">No links found</p>
    <?php
		}
		else {
			foreach ( $arr_links as $post_id1 => $arr_alternate_links ) {
	?>
            <div class="link-line">
                [<span class="link-post-id"><?php echo $post_id1; ?></span>] <?php echo get_permalink( $post_id1 ); ?> => 
                <?php
					foreach ( $arr_alternate_links as $iso => $post_id2 ) {
						if($post_id2 === $post_id1) continue;
				?>
	                <span><?php echo $iso; ?> [<?php echo $post_id2; ?>] <?php echo get_permalink( $post_id2 ); ?></span> 
                <?php
					}
				?>
                <a href="#" class="remove-link">remove</a>
            </div>
    <?php
			}
		}
	?>
    <br />
</div>
	<?php
	}
	
	function register_settings() {
		
		add_settings_section(
			'main-settings-section',
			'Main Settings',
			array($this, 'print_settings_section_info'),
			'sls-main'
		);
		
		add_settings_field(
			'enable-sls', 
			'Enable language switcher', 
			array($this, 'create_input_enable_simple_language_switcher'), 
			'sls-main', 
			'main-settings-section'
		);
		
		add_settings_field(
			'style', 
			'Display style', 
			array($this, 'create_select_style'), 
			'sls-main', 
			'main-settings-section'
		);
		
		add_settings_field(
			'separator', 
			'Separator between languages', 
			array($this, 'create_input_separator'), 
			'sls-main', 
			'main-settings-section'
		);
		
		add_settings_field(
			'enable-regex', 
			'Enable regex to detect language in URL', 
			array($this, 'create_input_enable_regex'), 
			'sls-main', 
			'main-settings-section'
		);
		
		register_setting( 'sls-settings-group-main', 'sls_plugin_options', array( $this, 'plugin_options_validate' ) );
		
		add_settings_section(
			'langs-section',
			'Languages',
			array($this, 'print_langs_section_info'),
			'sls-langs'
		);
		
		add_settings_field(
			'lang-iso', 
			'Language ISO 639-1 code', 
			array($this, 'create_select_add_lang'), 
			'sls-langs', 
			'langs-section'
		);
		
		add_settings_field(
			'lang-name', 
			'Language name', 
			array($this, 'create_input_lang_name'), 
			'sls-langs', 
			'langs-section'
		);
		
		add_settings_field(
			'lang-home-url', 
			'Language Home URL', 
			array($this, 'create_input_lang_home_url'), 
			'sls-langs', 
			'langs-section'
		);
		
		add_settings_field(
			'lang-regex', 
			'(Optional) Regex to detect language in URL', 
			array($this, 'create_input_lang_regex'), 
			'sls-langs', 
			'langs-section'
		);
		
		register_setting( 'sls-settings-group-langs', 'sls_plugin_langs', array( $this, 'plugin_langs_validate' ) );
		
		add_settings_section(
			'linking-section',
			'Posts Linking',
			array($this, 'print_linking_section_info'),
			'sls-linking'
		);
		
        $arr_langs = $this->get_array_langs();
		if( !empty( $arr_langs ) ) {
			foreach ( $arr_langs as $data ) {
				add_settings_field(
					$data['lang-iso'], 
					ucwords( $data['lang-name'] ), 
					array($this, 'create_input_link_posts'), 
					'sls-linking', 
					'linking-section',
					array('lang-iso' => $data['lang-iso'])
				);
			}
		}
		
		register_setting( 'sls-settings-group-linking', 'sls_plugin_linking', array( $this, 'plugin_linking_validate' ) );
	}
	
	function print_settings_section_info() {
		echo '<p>General settings.</p>';
	}
	
	function print_langs_section_info() {
		echo '<p>Add a language.</p>';
	}
	
	function print_style_section_info() {
		echo '<p>Style your language switcher.</p>';
	}
	
	function print_linking_section_info() {
		echo '<p>Interconnect your posts in different languages. Enter the URL or post ID.</p>';
	}
	
	function create_input_enable_simple_language_switcher() {
		$options = get_option('sls_plugin_options');
        ?><input style="display:none;" type="checkbox" name="sls_plugin_options[enable-sls]" value="0" checked/>
          <input type="checkbox" name="sls_plugin_options[enable-sls]" value="1" <?php echo 
			(empty( $options['enable-sls'] ) ? '' : ' checked'); ?> /><?php
	}
	
	function create_input_enable_regex() {
		$options = get_option('sls_plugin_options');
        ?><input style="display:none;" type="checkbox" name="sls_plugin_options[enable-regex]" value="0" checked/>
          <input type="checkbox" name="sls_plugin_options[enable-regex]" value="1" <?php echo 
			(empty( $options['enable-regex'] ) ? '' : ' checked'); ?> /><?php
	}
	
	function create_select_style() {
		$options = get_option('sls_plugin_options');
		$currentStyle = (int) $options['style'];
        ?><select name="sls_plugin_options[style]" size="1">
        	<option value="<?php echo SimpleLanguageSwitcher::STYLE_LANG_ISO; ?>"<?php echo ($currentStyle === SimpleLanguageSwitcher::STYLE_LANG_ISO ? ' selected' : ''); 
				?>>Language ISO 639-1 codes (e.g. en, fr, ru)</option>
        	<option value="<?php echo SimpleLanguageSwitcher::STYLE_LANG_NAME; ?>"<?php echo ($currentStyle === SimpleLanguageSwitcher::STYLE_LANG_NAME ? ' selected' : ''); 
				?>>Language names (e.g. English, Français, Русский)</option>
        	<option value="<?php echo SimpleLanguageSwitcher::STYLE_LANG_FLAG; ?>"<?php echo ($currentStyle === SimpleLanguageSwitcher::STYLE_LANG_FLAG ? ' selected' : ''); 
				?>>Flags</option>
		</select><?php
	}
	
	function create_input_separator() {
		$options = get_option('sls_plugin_options');
        ?><input type="text" name="sls_plugin_options[separator]" value="<?php echo $options['separator']; ?>" /><?php
	}
	
	function create_select_add_lang() {
		$options = get_option('sls_plugin_langs');
        ?><select name="sls_plugin_langs[lang-iso]" size="1">
        	<?php 
				$filename = SLS_PATH . 'lang/lang.php';
				$langs = include $filename;
				ksort($langs);
				foreach($langs as $iso => $langNames) {
			?>
        	<option value="<?php echo $iso; ?>"><?php echo $iso; ?> (<?php echo $langNames['nativeName']; ?>)</option>
        	<?php } ?>
		</select><?php
	}
	
	function create_input_lang_name() {
		$options = get_option('sls_plugin_langs');
        ?><input type="text" name="sls_plugin_langs[lang-name]" /><?php
	}
	
	function create_input_lang_home_url() {
		$options = get_option('sls_plugin_langs');
        ?><input type="text" name="sls_plugin_langs[lang-home-url]" /><?php
	}
	
	function create_input_lang_regex() {
		$options = get_option('sls_plugin_langs');
        ?><input type="text" name="sls_plugin_langs[lang-regex]" /><?php
	}
	
	function create_input_link_posts($args) {
		$options = get_option('sls_plugin_linking');
        ?><input type="text" name="sls_plugin_linking[<?php echo $args['lang-iso']; ?>]" /><?php
	}
	
	function plugin_options_validate($arr_input) {
		$options = get_option('sls_plugin_options');
		$options['enable-sls'] = (int) trim( $arr_input['enable-sls'] );
		$options['enable-regex'] = (int) trim( $arr_input['enable-regex'] );
		$options['style'] = trim( $arr_input['style'] );
		$options['separator'] = $arr_input['separator'];
		return $options;
	}
	
	function plugin_langs_validate($arr_input) {
		$valid = true;
		if( empty( $arr_input['lang-iso'] ) ) $valid = false;
		if( empty( $arr_input['lang-name'] ) ) $valid = false;
		if( empty( $arr_input['lang-home-url'] ) ) $valid = false;
		if( !$valid ) return array();
		return $arr_input;
	}
	
	function plugin_linking_validate($arr_input) {
		return $arr_input;
	}
	
	function get_array_langs() {
		return $this->get_array_file( SLS_PATH . 'my-langs.php' );
	}
	
	function get_array_links() {
		return $this->get_array_file( SLS_PATH . 'links.php' );
	}
	
	function get_array_file($filename) {
		if( !file_exists( $filename ) ) {
			$this->write_file( array(), $filename );
		}
		$arr = include $filename;
		return $arr;
	}
	
	function write_file_langs($arr_langs) {
		$this->write_file( $arr_langs, SLS_PATH . 'my-langs.php', 'write_file_langs_data' );
		update_option( 'sls_plugin_langs_backup', $arr_langs );
	}
	
	function write_file_langs_data($arr_langs) {
		$content = '';
		foreach ( $arr_langs as $data ) {
			$content .= "\n\t'" . $data['lang-iso'] . "' => array( 'lang-iso' => '" . 
				$data['lang-iso'] . "', 'lang-name' => '" . 
				$data['lang-name'] . "', 'lang-home-url' => '" . 
				$data['lang-home-url'] . "', 'lang-regex' => '" . 
				$data['lang-regex'] . "' ),";
		}
		return $content;
	}
	
	function write_file_links($arr_links) {
		$this->write_file( $arr_links, SLS_PATH . 'links.php', 'write_file_links_data' );
		update_option( 'sls_plugin_linking_backup', $arr_links );
	}
	
	function write_file_links_data($arr_links) {
		$content = '';
		foreach ( $arr_links as $post_id1 => $alternate_posts ) {
			$content .= "\n\t" . $post_id1 . " => array(";
			foreach( $alternate_posts as $iso => $post_id2 ) {
				$content .= "'" . $iso . "' => " . $post_id2 . ",";
			}
			$content .= "),";
		}
		return $content;
	}
	
	function write_file($arr_data, $filename, $function_write_line = NULL) {
		$content = '<?php return array(';
		if( !empty( $function_write_line ) ) {
			$content .= call_user_func( array( $this, $function_write_line ), $arr_data );
		}
		$content .= "\n" . '); ?>';
		
		$filename_lock = $filename . '.lock';
		if( !file_exists( $filename_lock ) ) {
			$fh = fopen( $filename_lock, 'w' );
			fclose( $fh );
		}
		try {
			file_put_contents( $filename, $content );
		} catch( Exception $exc ) {}
		unlink( $filename_lock );
	}
	
	function update_sls_plugin_langs($old_value, $value) {
		if( empty( $value ) ) return;
		
		$arr_langs = $this->get_array_langs();
		
		$arr_langs[$value['lang-iso']] = $value;
		
		$this->write_file_langs( $arr_langs );
		
		update_option( 'sls_plugin_langs', array() );
	}
	
	function update_sls_plugin_linking($old_value, $value) {
		if( empty( $value ) ) return;
		
		$n = 0;
		foreach( $value as $iso => $post_id_or_url ) {
			if( empty( $post_id_or_url ) ) continue;
			
			if( !is_numeric( $post_id_or_url ) ) {
				$post_id = url_to_postid( $post_id_or_url );
				if( empty( $post_id ) ) return;
				$value[$iso] = $post_id;
			}
			++$n;
		}
		if( $n < 2 ) return;
		
		$value = array_filter( $value );
		
		$value = array_flip( $value );
		
		$arr_links = $this->get_array_links();
		
		foreach( $value as $post_id1 => $iso1 ) {
			$arr_alternate_links = array();
			foreach( $value as $post_id2 => $iso2 ) {
				$arr_alternate_links[$iso2] = $post_id2;
			}
			$arr_links[$post_id1] = $arr_alternate_links;
		}
		
		$this->write_file_links( $arr_links );
		
		update_option( 'sls_plugin_linking', array() );
	}
	
}

?>
