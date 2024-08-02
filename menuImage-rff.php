<?php
/*
Plugin Name: Menu Image RFF
Plugin URI:  http://exemplo.com
Description: Cria um menu com imagens
Version:     1.0
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
 add_action('admin_enqueue_scripts', 'mi_rff_register_core_js');

/***
  * includes 
  */
if(file_exists( MI_RFF_CORE_INC.'mi-rff-shortcode.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-shortcode.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-functions.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-functions.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-hook.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-hook.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-functions-class.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-functions-class.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-upload-image.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-upload-image.php' );
}

$connection_mi_rff = new MiRffConection();
$upload_mi_rff = new MiRffUpload();

//Instala a tabela na ativação do plugin e desinstala a tabela na desativação
register_activation_hook(__FILE__, 'menuImage_rff_install');
register_deactivation_hook(__FILE__, 'menuImage_rff_uninstall');
// Registrar tipos e campos no GraphQL
add_action( 'graphql_register_types', 'register_custom_table_in_graphql' );


//////////////////////////////////////////////////
//Adicionar Menu na Área Administrativa
add_action('admin_menu', 'menuImage_rff_add_admin_menu');
function menuImage_rff_add_admin_menu() {
    add_menu_page(
        'Menu image', // Título da página
        'Menu image', // Título do menu
        'manage_options', // Capacidade
        'menu-image', // Slug
        'menuImage_rff_admin_page', // Função
        'dashicons-welcome-widgets-menus', // Ícone
        6 // Posição
    );
}

function menuImage_rff_admin_page() {
    global $connection_mi_rff;
    global $upload_mi_rff;
    ?>
    <div class="wrap">
        <h1>Configuração do Menu Imagem RFF </h1>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="text" name="nome" placeholder="Digite o nome" value="">
            <!-- <input type="text" name="urlImg" placeholder="Digite a url da imagem" value=""> -->
            <input type="file" name="urlImg" id="urlImg" accept="image/*">
            <input type="text" name="urlLink" placeholder="Digite a url do link" value="">
            <input type="text" name="altText" placeholder="Digite o texto que deve aparecer ao passar o mouse por cima da imagem" value="">
            <input type="submit" class="mi-rff-bt-submit" id="Enviar" name="Enviar" value="Enviar">
        </form>
    </div>
    <?php
    if(isset($_POST['Editar']) && isset($_POST['id']) && isset($_POST['nome']) && isset($_POST['urlImg']) && isset($_POST['urlLink']) && isset($_POST['altText'])){
        if($_POST['id']!='' && $_POST['nome']!='' && $_POST['urlImg']!='' && $_POST['urlLink']!='' && $_POST['altText']!=''){
            $connection_mi_rff->menuImage_rff_editar_dados($_POST['id'], $_POST['nome'], $_POST['urlImg'], $_POST['urlLink'], $_POST['altText']);
            echo '<div class="notice notice-success is-dismissible"><p>Dados alterados com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Todos os campos precisam ser preenchidos!</p></div>';
        }
    }else if (isset($_POST['Enviar']) && isset($_POST['nome']) && !empty($_FILES['urlImg']['name']) && isset($_POST['urlLink']) && isset($_POST['altText'])) {
        if($_POST['nome']!='' && $_POST['urlLink']!='' && $_POST['altText']!=''){
            $nome = sanitize_text_field($_POST['nome']);
            $urlImg = $_FILES['urlImg'];
            $urlLink = sanitize_text_field($_POST['urlLink']);
            $altText = sanitize_text_field($_POST['altText']);
            // printf($urlImg);
            $image = $upload_mi_rff->uploadImage($urlImg);
            // echo '//-----------------------------------//<br>';
            // echo $image;
            // echo '<br>........................................';
            // menuImage_rff_gravar_dados($nome, $urlImg, $urlLink, $altText);
            $connection_mi_rff->menuImage_rff_gravar_dados($nome, $image, $urlLink, $altText);
            echo '<div class="notice notice-success is-dismissible"><p>Dados gravados com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Todos os campos precisam ser preenchidos!</p></div>';
        }
    }else if(isset($_POST['Excluir']) && isset($_POST['id'])){
        if($_POST['id']!=''){
            $connection_mi_rff->menuImage_rff_excluir_dados($_POST['id'], $_POST['urlImg']);
            echo '<div class="notice notice-success is-dismissible"><p>Registro excluído com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Não foi possível excluir o registro!</p></div>';
        }
    }
    //mostra os dados gravados
    $dados = $connection_mi_rff->menuImage_rff_recuperar_dados();
    if ($dados) {
        // echo '<img src="'.$dados[0]->urlImg.'" width="100">';
        echo '<h2>Dados Gravados</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Nome</th><th>Url Image</th><th>Url Link</th><th>Texto alternativo</th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        foreach ($dados as $dado) {
            echo '<tr>';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<td><input type="hidden" value="'.esc_html($dado->id).'" name="id" id="id" />' . esc_html($dado->id) . '</td>';
            echo '<td><input type="text" value="' . esc_html($dado->nome) . '" name="nome" id="nome" placeholder="Digite o nome" /></td>';
            // echo '<td>' . esc_html($dado->urlImg) . '</td>';
            echo '<td>' . '<img src="'.$dado->urlImg.'" class="mi-rff-img-admin"><input type="hidden" name="urlImg" id="urlImg" value="'.$dado->urlImg.'" /></td>';
            echo '<td><input type="text" value="' . esc_html($dado->urlLink) . '" name="urlLink" id="urlLink" placeholder="Digite a url do link" /></td>';
            echo '<td><input type="text" value="' . esc_html($dado->altText) . '" name="altText" id="altText" placeholder="Digite o texto alternativo" /></td>';
            echo '<td><input type="submit" class="mi-rff-bt-submit" id="Editar" name="Editar" value="Editar" /><input type="submit" class="mi-rff-bt-submit" id="Excluir" name="Excluir" value="Excluir" /></td>';
            echo '</form>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Nenhum dado encontrado.</p>';
    }
}