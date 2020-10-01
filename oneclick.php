<?php

/*
Plugin Name: OneClickBay
Plugin URI: https://github.com/BKarpow/wp-plugin-OneClickBay
Description: Плагін додає можливість купівлі в один клік в woocommerce.
Version: 1.0
Author: XemerOne <xymerone@gmail.com>
Author URI: http://karpow.site
License:  GPL2
*/

define('ROOT_ONE_CLICK', plugin_dir_path(__FILE__));
define('CORE_ONE_CLICK', ROOT_ONE_CLICK. 'core' . DIRECTORY_SEPARATOR);
define('CSS_ONE_CLICK', plugin_dir_url(__FILE__) . 'css/');
define('JS_ONE_CLICK', plugin_dir_url(__FILE__) . 'js/');

require_once CORE_ONE_CLICK . 'OneClick.php';



$OneClick = new OneClick();
function ok_send(){
    return OneClick::send();
}


require_once CORE_ONE_CLICK . 'ajax.php';
//Підключення стилів та скриптів


add_action('wp_enqueue_scripts', [$OneClick, 'includeCssJs']);
add_action( 'woocommerce_single_product_summary', [$OneClick, 'render_block']);

$OneClick->load_page('options');

