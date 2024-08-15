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


/***
  * includes 
  */
if(file_exists( MI_RFF_CORE_INC.'mi-rff-shortcode.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-shortcode.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-hook.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-hook.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-graphql.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-graphql.php' );
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
    // $connection_mi_rff->restoreData();
    $style_select = "padding: 5px 15px; padding-right: 20px; font-weight: bold; text-transform: uppercase; margin-top:-5px;";
    //
    ?>
    <div class="wrap" style="position:relative;">
        <h1>Configuração do Menu Imagem RFF </h1>
        <h3>Locais cadastrados</h3>
        <div class="mi_rff_bt_open" id="mi_rff_bt_open_cad_location">Cadastrar uma localização</div>
        <div class="mi_rff_geral" id="mi_rff_geral_insert_location">
            <div class="mi_rff_insert_item">
                <h1>Cadastrar item</h1>
                <div class="mi_rff_btclose" id="mi_rff_btclose_cad_location" title="Fechar a janela">X</div>
                <form method="post" action="" id="mi_rff_form" enctype="multipart/form-data">
                    <input type="text" name="mi_rff_title" placeholder="Digite o título" value="" required>
                    <select name="statusItem" id="statusItem" style="<?php echo $style_select; ?>" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                    <input type="submit" class="mi-rff-bt-submit" id="cadastrar_location" name="cadastrar_location" value="Cadastrar">
                </form>
            </div>
        </div>
    </div>
    <?php
        if(isset($_POST['cadastrar_location'])){
            $title = sanitize_text_field($_POST['mi_rff_title']);
            $connection_mi_rff->save_location($title, $_POST['statusItem']);
        }

        $dadosLocation = $connection_mi_rff->get_all_locations();
        if($dadosLocation){
            echo '<table class="wp-list-table widefat">';
            echo '<thead><tr><th>ID</th><th>Título</th><th>status</th><th>Ações</th></tr></thead>';
            echo '<tbody>';
            foreach($dadosLocation as $dadoLocation){
                echo '<form method="post" action="" enctype="multipart/form-data">';
                echo '<tr>';
                echo '<td>'.$dadoLocation->id.'</td>';
                echo '<td>'.$dadoLocation->title.'</td>';
                echo '<td>'.$dadoLocation->statusItem.'</td>';
                echo '<td id="down_rff_bts"><input type="submit" class="down_rff_edit" id="edit" name="edit" value="Editar" /><input type="submit" id="excluir_item" name="excluir_item" value="Excluir" /></td>';
                echo '</tr>';
                echo '</form>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    ?>
    <div class="wrap" style="position:relative;">
        <h3>Item cadastrados</h3>
        <div class="mi_rff_bt_open" id="mi_rff_bt_open_cad_item">Cadastrar um item</div>
        <div class="mi_rff_geral" id="mi_rff_geral_insert_item">
            <div class="mi_rff_insert_item">
                <h1>Cadastrar item</h1>
                <div class="mi_rff_btclose" id="mi_rff_btclose_cad_item" title="Fechar a janela">X</div>
                <form method="post" action="" id="mi_rff_form" enctype="multipart/form-data">
                    <input type="text" name="orderItems" size="5" placeholder="Digite a ordem que ele deve aparecer" value="" title="Digite a ordem que ele deve aparecer" required>
                    <input type="text" name="nome" placeholder="Digite o nome" value="" required>
                    <!-- <input type="text" name="urlImg" placeholder="Digite a url da imagem" value=""> -->
                    <span>
                        <label for="urlImg">Selecionar arquivo</label>
                        <input type="file" name="urlImg" id="urlImg" accept="image/*" required>
                    </span>
                    <input type="text" name="urlLink" placeholder="Digite a url do link" value="" required>
                    <input type="text" name="altText" placeholder="Digite o texto que deve aparecer ao passar o mouse por cima da imagem" title="Digite o texto que deve aparecer ao passar o mouse por cima da imagem" value="" required>
                    <select name="statusItem" id="statusItem" style="<?php echo $style_select; ?>" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                    <select name="locationId" id="locationId" style="<?php echo $style_select; ?>" required>
                        <?php
                            if($dadosLocation){
                                foreach($dadosLocation as $dadosL){
                                    echo '<option value="'.$dadosL->id.'">'.$dadosL->title.'</option>';
                                }
                            }
                        ?>
                    </select>
                    <input type="submit" class="mi-rff-bt-submit" id="Enviar" name="Enviar" value="Enviar">
                </form>
            </div>
        </div>
    </div>
    <?php
    if(isset($_POST['Editar']) && isset($_POST['id']) && isset($_POST['nome']) && isset($_POST['urlImg']) && isset($_POST['urlLink']) && isset($_POST['altText'])){
        if($_POST['id']!='' && $_POST['nome']!='' && $_POST['urlImg']!='' && $_POST['urlLink']!='' && $_POST['altText']!=''){
            $id = sanitize_text_field($_POST['id']);
            $orderItems = sanitize_text_field($_POST['orderItems']);
            $nome = sanitize_text_field($_POST['nome']);
            $urlLink = sanitize_text_field($_POST['urlLink']);
            $altText = sanitize_text_field($_POST['altText']);
            $statusItem = sanitize_text_field($_POST['statusItem']);
            $locationId = sanitize_text_field($_POST['locationId']);
            // $connection_mi_rff->menuImage_rff_editar_dados($_POST['id'], $orderItems, $_POST['nome'], $_POST['urlImg'], $_POST['urlLink'], $_POST['altText'], $statusItem);
            $connection_mi_rff->menuImage_rff_editar_dados($id, $orderItems, $nome, $_POST['urlImg'], $urlLink, $altText, $statusItem, $locationId);
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
            $orderItems = sanitize_text_field($_POST['orderItems']);
            $statusItem = sanitize_text_field($_POST['statusItem']);
            $locationId = sanitize_text_field($_POST['locationId']);
            // printf($urlImg);
            $image = $upload_mi_rff->uploadImage($urlImg);
            // echo '//-----------------------------------//<br>';
            // echo $image;
            // echo '<br>........................................';
            // menuImage_rff_gravar_dados($nome, $urlImg, $urlLink, $altText);
            $connection_mi_rff->menuImage_rff_gravar_dados($orderItems, $nome, $image, $urlLink, $altText, $statusItem, $locationId);
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
        echo '<table class="wp-list-table widefat fixed striped" style="table-layout: auto !important;">';
        echo '<thead><tr><th>ID</th><th>Ordem</th><th>Nome</th><th>Url Image</th><th>Url Link</th><th>Texto alternativo</th><th>Status</th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        foreach ($dados as $dado) {
            echo '<tr>';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<td><input type="hidden" value="'.esc_html($dado->id).'" name="id" id="id" />' . esc_html($dado->id) . '</td>';
            echo '<td><input type="text" value="' . esc_html($dado->orderItems) . '" name="orderItems" id="orderItems" size="5" placeholder="Digite a ordem que ele deve aparecer" value="" title="Digite a ordem que ele deve aparecer" /></td>';
            echo '<td><input type="text" value="' . esc_html($dado->nome) . '" name="nome" id="nome" placeholder="Digite o nome" /></td>';
            // echo '<td>' . esc_html($dado->urlImg) . '</td>';
            echo '<td>' . '<img src="'.$dado->urlImg.'" class="mi-rff-img-admin"><input type="hidden" name="urlImg" id="urlImg" value="'.$dado->urlImg.'" /></td>';
            echo '<td><input type="text" value="' . esc_html($dado->urlLink) . '" name="urlLink" id="urlLink" placeholder="Digite a url do link" /></td>';
            echo '<td><input type="text" value="' . esc_html($dado->altText) . '" name="altText" id="altText" placeholder="Digite o texto alternativo" /></td>';
            echo '<td><select name="statusItem" id="statusItem" style="'.$style_select.' margin-top: 0;">
                    <option value="'.$dado->statusItem.'">-> '.$dado->statusItem.' <-</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select></td>';
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