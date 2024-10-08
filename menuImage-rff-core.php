<?php
/**
 * 
 */

 if(!defined('WPINC')){
    die();
 }

 /***
  * includes 
  */
if(file_exists( MI_RFF_CORE_INC.'mi-rff-shortcode.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-shortcode.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-functions-class.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-functions-class.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-upload-image.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-upload-image.php' );
}
if(file_exists( MI_RFF_CORE_INC.'mi-rff-filter.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-filter.php' );
}

$connection_mi_rff = new MiRffConection();
$upload_mi_rff = new MiRffUpload();
$filterMi = new FilterMiRff();


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
    global $filterMi;
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
                <h1>Cadastrar localização</h1>
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
        <!-- Formulário de edição da LOCALIZAÇÃO -->
        <div class="mi_rff_geral" id="mi_rff_geral_edit_location">
            <div class="mi_rff_insert_item">
                <h1>Editar localização</h1>
                <div class="mi_rff_btclose" id="mi_rff_btclose_edit_location" title="Fechar a janela">X</div>
                <form method="post" action="" id="mi_rff_form" enctype="multipart/form-data">
                    <input type="hidden" id="mi_rff_id_location" name="mi_rff_id_location" placeholder="Digite o título" value="" required>
                    <input type="text" id="mi_rff_title_location" name="mi_rff_title" placeholder="Digite o título" value="" required>
                    <select name="statusItem" id="statusItem_location" style="<?php echo $style_select; ?>" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                    <input type="submit" class="mi-rff-bt-submit" id="edit_location" name="edit_location" value="Editar">
                </form>
            </div>
        </div>
    </div>
    <?php
        if(isset($_POST['cadastrar_location'])){
            $title = sanitize_text_field($_POST['mi_rff_title']);
            $connection_mi_rff->save_location($title, $_POST['statusItem']);
        }else if(isset($_POST['edit_location'])){
            $title = sanitize_text_field($_POST['mi_rff_title']);
            $connection_mi_rff->edit_location($_POST['mi_rff_id_location'], $title, $_POST['statusItem']);
        }else if(isset($_POST['excluir_location'])){
            $connection_mi_rff->delete_location($_POST['mi_rff_id_location']);
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
                echo '<input type="hidden" value="'.$dadoLocation->id.'" name="mi_rff_id_location">';
                echo '<td id="down_rff_bts"><input type="submit" class="down_rff_edit_location" value="Editar" /><input type="submit" id="excluir_location" name="excluir_location" value="Excluir" /></td>';
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

        <!-- Formulário de edição do ITEM -->
        <div class="mi_rff_geral" id="mi_rff_geral_edit_item">
            <div class="mi_rff_insert_item">
                <h1>Cadastrar item</h1>
                <div class="mi_rff_btclose" id="mi_rff_btclose_edit_item" title="Fechar a janela">X</div>
                <form method="post" action="" id="mi_rff_form" enctype="multipart/form-data">
                    <span id="mi_rff_img_edit">
                    </span>
                    <input type="hidden" id="item_id" name="item_id" size="5" required>
                    <input type="number" id="orderItems_item" name="orderItems" size="5" placeholder="Digite a ordem que ele deve aparecer" value="" title="Digite a ordem que ele deve aparecer" required>
                    <input type="text" id="nome_item" name="nome" placeholder="Digite o nome" value="" required>
                    <input type="url" id="urlLink_item" name="urlLink" placeholder="Digite a url do link" value="" required>
                    <input type="text" id="altText_item" name="altText" placeholder="Digite o texto que deve aparecer ao passar o mouse por cima da imagem" title="Digite o texto que deve aparecer ao passar o mouse por cima da imagem" value="" required>
                    <select name="statusItem" id="statusItem_item" style="<?php echo $style_select; ?>" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                    <select name="locationId" id="locationId_edit" style="<?php echo $style_select; ?>" required>
                        <?php
                            if($dadosLocation){
                                foreach($dadosLocation as $dadosL){
                                    echo '<option value="'.$dadosL->id.'">'.$dadosL->title.'</option>';
                                }
                            }
                        ?>
                    </select>
                    <input type="submit" class="mi-rff-bt-submit" id="Enviar" name="edit_item" value="Editar">
                </form>
            </div>
        </div>
    </div>
    <?php
    if(isset($_POST['edit_item'])){
        if($_POST['item_id']!='' && $_POST['nome']!='' && $_POST['urlImg']!='' && $_POST['urlLink']!='' && $_POST['altText']!='' && $_POST['orderItems']!=''){
            $id = sanitize_text_field($_POST['item_id']);
            $orderItems = sanitize_text_field($_POST['orderItems']);
            $nome = sanitize_text_field($_POST['nome']);
            $urlLink = sanitize_text_field($_POST['urlLink']);
            $altText = sanitize_text_field($_POST['altText']);
            $statusItem = sanitize_text_field($_POST['statusItem']);
            $locationId = sanitize_text_field($_POST['locationId']);
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
            $image = $upload_mi_rff->uploadImage($urlImg);
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
    // Verifica se foi aplicado um filtro usando o formulário de filtro
    if(isset($_POST['mi_filter_form'])){
        //Grava o filtro selecionado
        $filterMi->save_filter($_POST['mi_rff_filter_local']);
    }
    //Lê o filtro gravado no arquivo de filtro, se não existir, ele cria o arquivo com o valor 0, que representa todos
    $filtro = $filterMi->read_filter($connection_mi_rff);
        echo '<h2>Dados Gravados</h2>';
        echo '<div>
            <form action="" method="post">
                <select name="mi_rff_filter_local" required>
                    <option value="0">Todos</option>';
                    if($dadosLocation){
                        foreach($dadosLocation as $dadosL){
                            echo '<option value="'.$dadosL->id.'">'.$dadosL->title.'</option>';
                        }
                    }
        echo '  </select>
                <input type="submit" name="mi_filter_form" value="Filtrar">';
        if($filtro['val']!=null){
            $localizacaoMi = $connection_mi_rff->get_location_by_id($filtro['val']);
            echo '  <span>  Você pesquisou por: <strong>'.$localizacaoMi->title.'</strong></span>';
        }
        echo '</form>
        </div>';
        $dados = $filtro['itemDados'];
    if ($dados) {
        echo '<table class="wp-list-table widefat fixed striped" style="table-layout: auto !important;">';
        echo '<thead><tr><th>ID</th><th>Ordem</th><th>Nome</th><th>Url Image</th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        foreach ($dados as $dado) {
            $location = $connection_mi_rff->get_location_by_id($dado->locationId);
            echo '<tr>';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<td>'.esc_html($dado->id) . '</td>';
            echo '<td>' . esc_html($dado->orderItems) . '</td>';
            echo '<td>' . esc_html($dado->nome) . '</td>';
            echo '<td><img src="'.$dado->urlImg.'" class="mi-rff-img-admin"><input type="hidden" name="urlImg" id="urlImg" value="'.$dado->urlImg.'" /></td>';
            echo '<td style="display:none;">' . esc_html($dado->urlLink) . '</td>';
            echo '<td style="display:none;">' . esc_html($dado->altText) . '</td>';
            echo '<td style="display:none;">'.$dado->statusItem.'</td>';
            echo '<td id='.$location->id.' style="display:none;">'.$location->title.'</td>';
            echo '<td style="display:none;"><input type="text" value="'.esc_html($dado->id) . '" name="id"></td>';
            echo '<td id="td_submit_bts"><input type="submit" class="mi_rff_open_edit_item" value="Editar" /><input type="submit" class="mi-rff-bt-submit" id="Excluir" name="Excluir" value="Excluir" /></td>';
            echo '</form>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Nenhum dado encontrado.</p>';
    }
}