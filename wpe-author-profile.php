<?php
/**
 * Plugin Name:     WPE Author Profile
 * Plugin URI:      http://wpexpertos.net
 * Description:     Perfil de author personalizado.
 * Author:          Deryck Oñate Espinel
 * Author URI:      http://deryckoe.me
 * Text Domain:     wpe-author-profile
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WPE_Author_Profile
 */

/**
 * Ruta absoluta a carpeta de plugin
 */

define( 'WPEAP_PATH', plugin_dir_path(__FILE__) );

/**
 * Ruta absoluta a URL de plugin
 */

define( 'WPEAP_URL', plugin_dir_url(__FILE__) );

/**
 * Inicializamos el plugin, cargando las clases, instanciandolas e inicializandolas.
 */

function wpe_author_profile_init() {

	require_once 'includes/class-settings.php';

	$settings = new \WPE\Author\Settings();
	$settings->init();

	require_once 'includes/class-rewrite_rules.php';

	$rewrite_rules = new \WPE\Author\Rewrite_Rules( $settings );
	$rewrite_rules->init();

	require_once 'includes/class-profile.php';

	$author_profile = new \WPE\Author\Profile();
	$author_profile->init();

}

/**
 * Inicializa siempre el plugin utilizando la acción plugins_loaded.
 */

add_action('plugins_loaded', 'wpe_author_profile_init' );