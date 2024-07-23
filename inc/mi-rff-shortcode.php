<?php

/**
 * Este arquivo contém os shortcodes
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }

 if(file_exists( MI_RFF_CORE_INC.'mi-rff-functions.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-functions.php' );
}
function mi_rff_menuImage($atts){
    $dados = menuImage_rff_recuperar_dados();
    if($dados){
        $content = '';
        $content .= '<div class="mi-rff-container">';
        foreach($dados as $dado){
            $content .= '<img src="'.$dado->urlImg.'" alt="'.$dado->altText.'" title="'.$dado->altText.'" class="imgMenu" onclick="window.location.href=\''.$dado->urlLink.'\'">';
        }
        $content .= '</div>';
        return $content;
    }else{
        return 'Sem menu';
    }
}

/**
 * registro de todos os shortcodes
 */
function mi_rff_register_shortcodes(){
    //shortcodes registrados
    add_shortcode('miRffMenuImage', 'mi_rff_menuImage');
}

add_action('init', 'mi_rff_register_shortcodes');