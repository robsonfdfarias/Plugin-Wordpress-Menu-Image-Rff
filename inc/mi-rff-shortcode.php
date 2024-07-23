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
        echo '<div>';
        foreach($dados as $dado){
            echo '<div><a href="'.$dados->urlLink.'"><img src="'.$dados->urlImg.'" alt="'.$dados->altText.'"></a></div>';
        }
        echo '</div>';
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