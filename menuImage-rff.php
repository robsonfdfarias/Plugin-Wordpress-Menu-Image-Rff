<?php
/*
Plugin Name: Menu Image RFF
Plugin URI:  http://exemplo.com
Description: Cria um menu com imagens
Version:     3.0
Author:      Robson Ferreira de Farias
Email: robsonfdfarias@gmail.com
Author URI:  http://infocomrobson.com.br
License:     GPL2
*/

 //se chamar diretamente e não pelo wordpress, aborta
 if(!defined('WPINC')){
    die();
 }

define('MI_RFF_CORE_INC', dirname(__FILE__).'/inc/');//caminho dos arquios php
define('MI_RFF_DIR_IMG', dirname(__FILE__).'/img/');
define('MI_RFF_URL_CSS', plugins_url('css/', __FILE__));// Caminho absoluto para o diretório do plugin
define('MI_RFF_URL_JS', plugins_url('js/', __FILE__));


 /***
  * Registrando o css (Backend)
  */
  function mi_rff_register_css(){
    wp_enqueue_style('mi-rff-css', MI_RFF_URL_CSS.'mi-rff.css', null, time(), 'all');
 }

 add_action('admin_enqueue_scripts', 'mi_rff_register_css');

 /***
  * Registrando o js (backend)
  */
function mi_rff_register_admin_js(){
    if(!did_action('wp_enqueue_media')){
        wp_enqueue_media();
    }
    wp_enqueue_script('mi-rff-js-admin', MI_RFF_URL_JS.'mi-rff-admin.js', 'jquery', time(), true);
 }
 add_action('admin_enqueue_scripts', 'mi_rff_register_admin_js');
 
 
 /***
  * Registrando o css (frontend)
  */
  function mi_rff_register_css_core(){
    wp_enqueue_style('mi-rff-css-core', MI_RFF_URL_CSS.'mi-rff-core.css', null, time(), 'all');
 }

 add_action('wp_enqueue_scripts', 'mi_rff_register_css_core');
 
 /***
  * Registrando o js (frontend)
  */
function mi_rff_register_core_js(){
    if(!did_action('wp_enqueue_media')){
        wp_enqueue_media();
    }
    wp_enqueue_script('mi-rff-js-core', MI_RFF_URL_JS.'mi-rff-core.js', 'jquery', time(), true);
 }

 add_action('wp_enqueue_scripts', 'mi_rff_register_core_js');


//permitindo o upload de SVG
// Permitir upload de arquivos SVG
function mi_rff_permitir_upload_svg($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }
  add_filter('upload_mimes', 'mi_rff_permitir_upload_svg');
  
  // Adicionar suporte para visualizar SVGs na biblioteca de mídia
  function mi_rff_adicionar_tamanho_svg($sizes) {
    $sizes['svg'] = 'SVG';
    return $sizes;
  }
  add_filter('image_size_names_choose', 'mi_rff_adicionar_tamanho_svg');
  


if(file_exists( MI_RFF_CORE_INC.'mi-rff-hook.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-hook.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-graphql.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-graphql.php' );
}
if(file_exists( dirname(__FILE__).'/menuImage-rff-core.php' )){
    require_once( dirname(__FILE__).'/menuImage-rff-core.php' );
}
//Instala a tabela na ativação do plugin e desinstala a tabela na desativação
register_activation_hook(__FILE__, 'menuImage_rff_install');
register_deactivation_hook(__FILE__, 'menuImage_rff_uninstall');
// Registrar tipos e campos no GraphQL
add_action( 'graphql_register_types', 'register_custom_table_location_in_graphql' );
add_action( 'graphql_register_types', 'register_custom_table_in_graphql' );

