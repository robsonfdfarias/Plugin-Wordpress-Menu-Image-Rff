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


register_activation_hook(__FILE__, 'menuImage_rff_install');
register_deactivation_hook(__FILE__, 'menuImage_rff_uninstall');

//Cria a tabela na ativação do plugin
function menuImage_rff_install() {
    global $wpdb;
    // $table_name = $wpdb->prefix . 'meu_plugin_table';
    $table_name = $wpdb->prefix . 'menuImage_rff';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome varchar(200),
        urlImg varchar(150) NOT NULL,
        urlLink varchar(150) NOT NULL,
        altText varchar(255),
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
//Apaga a tabela ao desativar o plugin
function menuImage_rff_uninstall() {
    global $wpdb;
    // $table_name = $wpdb->prefix . 'meu_plugin_table';
    $table_name = $wpdb->prefix . 'menuImage_rff';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}
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
            menuImage_rff_editar_dados($_POST['id'], $_POST['nome'], $_POST['urlImg'], $_POST['urlLink'], $_POST['altText']);
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
            $image = uploadImage($urlImg);
            // echo '//-----------------------------------//<br>';
            // echo $image;
            // echo '<br>........................................';
            // menuImage_rff_gravar_dados($nome, $urlImg, $urlLink, $altText);
            menuImage_rff_gravar_dados($nome, $image, $urlLink, $altText);
            echo '<div class="notice notice-success is-dismissible"><p>Dados gravados com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Todos os campos precisam ser preenchidos!</p></div>';
        }
    }else if(isset($_POST['Excluir']) && isset($_POST['id'])){
        if($_POST['id']!=''){
            menuImage_rff_excluir_dados($_POST['id'], $_POST['urlImg']);
            echo '<div class="notice notice-success is-dismissible"><p>Registro excluído com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Não foi possível excluir o registro!</p></div>';
        }
    }
    //mostra os dados gravados
    $dados = menuImage_rff_recuperar_dados();
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


// Registrar tipos e campos no GraphQL
add_action( 'graphql_register_types', 'register_custom_table_in_graphql' );

function register_custom_table_in_graphql() {
    register_graphql_object_type( 'CustomTableType', [
        'description' => __( 'Tabela de menu com imagem', 'your-textdomain' ),
        'fields' => [
            'id' => [
                'type' => 'ID',
                'description' => __( 'ID of the item', 'your-textdomain' ),
            ],
            'nome' => [
                'type' => 'String',
                'description' => __( 'Nome do item do menu image', 'your-textdomain' ),
            ],
            'urlImg' => [
                'type' => 'String',
                'description' => __( 'Url da image do item do menu image', 'your-textdomain' ),
            ],
            'urlLink' => [
                'type' => 'String',
                'description' => __( 'Url do link do item do menu image', 'your-textdomain' ),
            ],
            'altText' => [
                'type' => 'String',
                'description' => __( 'Texto alternativo do item do menu image', 'your-textdomain' ),
            ],
        ],
    ] );

    register_graphql_field( 'RootQuery', 'menuImage_rff', [
        'type' => [ 'list_of' => 'CustomTableType' ],
        'description' => __( 'Query de consulta da tabela', 'your-textdomain' ),
        'resolve' => function( $root, $args, $context, $info ) {
            global $wpdb;
            // $table_name = $wpdb->prefix . 'custom_table';
            $table_name = $wpdb->prefix . 'menuImage_rff';
            $results = $wpdb->get_results( "SELECT * FROM $table_name" );
            return $results;
        }
    ] );
}

function uploadImage($file){
    // $uploadedfile = $_FILES['meu_plugin_image_upload'];
    $uploadedfile = $file;

    // Defina o diretório de destino para a pasta 'img' dentro do diretório do plugin
    $plugin_dir = plugin_dir_path(__FILE__); // Caminho absoluto para o diretório do plugin
    $upload_dir = $plugin_dir . 'img/'; // Diretório de upload
    $urlBase = plugins_url('img/', __FILE__);
    
    // Verifica se o diretório existe; caso contrário, tenta criar
    if (!file_exists($upload_dir)) {
        wp_mkdir_p($upload_dir);
    }

    // Verifica o tipo de arquivo e o tamanho, se necessário
    $file_type = wp_check_filetype($uploadedfile['name']);
    if ($file_type['ext'] !== 'jpg' && $file_type['ext'] !== 'jpeg' && $file_type['ext'] !== 'png' && $file_type['ext'] !== 'gif') {
        echo "<p>Tipo de arquivo não permitido. Apenas imagens JPG, JPEG, PNG e GIF são permitidas.</p>";
        return;
    }

    $upload_overrides = array('test_form' => false, 'move_uploaded_file' => true);

    // Move o arquivo carregado para o diretório de upload
    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
    if ($movefile && !isset($movefile['error'])) {
        // $new_file_path = $upload_dir . basename($movefile['file']); // Caminho final do arquivo
        $arqNameG = basename($movefile['file']);
        $partes = explode('.', $arqNameG);
        $newName = $partes[0].time().'.'.$partes[1];
        // echo '<h1>Novo nome do arquivo: '.$newName.'</h1>';
        $new_file_path = $upload_dir . $newName; // Caminho final do arquivo
        if (rename($movefile['file'], $new_file_path)) {
            // echo "<p>Arquivo carregado com sucesso: <a href='" . plugins_url('img/' . basename($new_file_path), __FILE__) . "'>" . basename($new_file_path) . "</a></p>";
            return $urlBase.basename($new_file_path);
        } else {
            echo "<p>Erro ao mover o arquivo para o diretório de destino.</p>";
        }
    } else {
        echo "<p>Erro ao carregar arquivo: {$movefile['error']}</p>";
    }
    return null;
}

// https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/arte-2.png
// https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/arte.png