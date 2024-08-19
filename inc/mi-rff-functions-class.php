<?php

/**
 * Este arquivo contém a classe com as funções de CRUD
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }


if(file_exists( MI_RFF_CORE_INC.'mi-rff-upload-image.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-upload-image.php' );
}
global $wpdb;
$mi_rff_upload = new MiRffUpload();

$table_location_mi_rff = $wpdb->prefix . 'menuImage_rff_location';
class MiRffConection {
    function __construct(){}

    function save_location($title, $statusItem){
        global $wpdb;
        global $table_location_mi_rff;
        $result = $wpdb->insert(
            $table_location_mi_rff,
            array(
                'title'=>$title,
                'statusItem'=>$statusItem,
            )
        );
        if($result>0){
            echo '<div class="notice notice-success is-dismissible"><p>Localização inserida com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Falha ao inserir a localização! Erro: '.$wpdb->last_error.'</p></div>';
        }
    }

    function get_all_locations(){
        global $wpdb;
        global $table_location_mi_rff;
        $results = $wpdb->get_results("SELECT * FROM $table_location_mi_rff");
        return $results;
    }

    function get_location_by_id($id){
        global $wpdb;
        global $table_location_mi_rff;
        $results = $wpdb->get_results("SELECT * FROM $table_location_mi_rff WHERE id=$id");
        return $results[0];
    }

    function edit_location($id, $title, $statusItem){
        global $wpdb;
        global $table_location_mi_rff;
        $result = $wpdb->update(
            $table_location_mi_rff,
            array(
                'title'=>$title,
                'statusItem'=>$statusItem,
            ),
            array('id'=>$id),
            array('%s'),
            array('%d'),
        );
        if($result<=0){
            echo '<div class="notice notice-failure is-dismissible"><p>Falha ao atualizar a localização!</p></div>';
        }else{
            echo '<div class="notice notice-success is-dismissible"><p>Localização atualizada com sucesso!</p></div>';
        }
    }

    function delete_location($id){
        global $wpdb;
        global $table_location_mi_rff;
        $result = $wpdb->delete(
            $table_location_mi_rff,
            array('id'=>$id),
            array('%d')
        );
        if($result<=0){
            echo '<div class="notice notice-failure is-dismissible"><p>Falha ao excluir a localização!</p></div>';
        }else{
            echo '<div class="notice notice-success is-dismissible"><p>Localização excluída com sucesso com sucesso!</p></div>';
        }
    }


    /**
     * Aqui começa a tabela de itens
     */

    //Grava os dados na tabela
    function menuImage_rff_gravar_dados($orderItems, $nome, $urlImg, $urlLink, $altText, $statusItem, $locationId) {
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';
        
        $wpdb->insert(
            $table_name,
            array(
                'orderItems' => $orderItems,
                'nome' => $nome,
                'urlImg' => $urlImg,
                'urlLink' => $urlLink,
                'altText' => $altText,
                'statusItem' => $statusItem,
                'locationId' => $locationId,
            )
        );
    }

    //recupera os dados da tabela
    function menuImage_rff_recuperar_dados() {
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';
        
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        return $results;
    }

    //recupera dados por id da localização
    function menuImage_rff_recupera_por_id_localizacao($id){
        global $wpdb;
        $table_name = $wpdb->prefix . 'menuImage_rff';
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE locationId=$id");
        return $results;
    }

    //excluir o registro
    function menuImage_rff_excluir_dados($id, $img) {
        global $wpdb;
        global $mi_rff_upload;
        //Remove a imagem
        $mi_rff_upload->removeImage($img);
        //Pega a tabela e remove o registro com o ID passado
        $table_name = $wpdb->prefix . 'menuImage_rff';
        $wpdb->delete(
            $table_name,
            array('id' => $id), // Condição para atualizar (WHERE id = $id)
            array('%d') // Tipo de dado da condição (%d indica que o valor é um inteiro)
        );
    }

    //editar o registro
    function menuImage_rff_editar_dados($id, $orderItems, $nome, $urlImg, $urlLink, $altText, $statusItem, $locationId) {
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';

        $wpdb->update(
            $table_name,
            array(  // Um array associativo onde as chaves são os nomes das colunas e os valores são os novos dados a serem inseridos
                'orderItems' => $orderItems,
                'nome' => $nome,
                'urlImg' => $urlImg,
                'urlLink' => $urlLink,
                'altText' => $altText,
                'statusItem' => $statusItem,
                'locationId' => $locationId,
            ),
            array('id' => $id), // Condição para atualizar (WHERE id = $id)
            array('%s'), // Tipo de dado dos valores novos (%s indica que o valor é uma string)
            array('%d') // Tipo de dado da condição (%d indica que o valor é um inteiro)
        );
    }


    //-------------------------------------------------------------APAGAR DEPOIS
    function restoreData(){
        $data = '0,Protocolo,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/protocolo1721747465.jpg,http://localhost:3000/pages/protocolo,Protocolo,Ativo|0,IPTU,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/iptu20241721747524.jpg,https://www.jaraguadosul.sc.gov.br/informacoes-iptu-2024,IPTU,Ativo|0,Emprego,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/botao_emprego1721747557.png,https://www.jaraguadosul.sc.gov.br/emprego,Emprego,Ativo|0,Portal da transparência,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/portal_transparencia1721747586.jpg,https://www.jaraguadosul.sc.gov.br/portal-da-transparencia,Portal da transparência,Ativo|0,Radar da transparência,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/radar1721747622.jpg,https://radar.tce.mt.gov.br/extensions/radar-da-transparencia-publica/radar-da-transparencia-publica.html,Radar da transparência,Ativo|0,Empresas,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/empresas1721747681.jpg,https://www.jaraguadosul.sc.gov.br/empresas,Empresas,Ativo|0,E-nota,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/nfs_e1721996813.jpg,https://www.jaraguadosul.sc.gov.br/e-nota,Nota Fiscal,Ativo|0,Diario Oficial,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/diario_oficial1721997118.jpg,https://www.jaraguadosul.sc.gov.br/diario-oficial,Diario Oficial,Ativo|0,Carta de Serviços,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/carta_serv1721997196.jpg,https://www.jaraguadosul.sc.gov.br/cartaservicos,Carta de Serviços,Ativo|0,Concursos Estágios e Processos Seletivos,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/conc_pro1721997328.jpg,https://www.jaraguadosul.sc.gov.br/concursos-estagios-e-processos-seletivos,Concursos Estágios e Processos Seletivos,Ativo|0,Ouvidoria,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/PMJS1721997474.jpg,https://www.jaraguadosul.sc.gov.br/ouvidoria,Ouvidoria,Ativo|0,Ouvidoria SUS,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/SUS1721997537.jpg,https://www.jaraguadosul.sc.gov.br/ouvidoria-sus,Ouvidoria SUS,Ativo|0,Geoportal,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/Geo-Jaragua1721997619.jpg,https://www.jaraguadosul.sc.gov.br/geoportal,Geoportal,Ativo|0,Projeto Legal,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/PMJS-21721998335.png,https://www.jaraguadosul.sc.gov.br/projeto-legal,Projeto Legal,Ativo|0,Certidao Negativa de Débito,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/certidao_neg_debito1721999448.jpg,https://www.jaraguadosul.sc.gov.br/certidao-negativa-debito,Certidao Negativa de Débito,Ativo|0,Licitações,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/banner_licitacoes1721999659.jpg,https://www.jaraguadosul.sc.gov.br/licitacoes,Licitações,Ativo|0,Defesa Civil,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/banner_novo1721999707.jpg,https://www.jaraguadosul.sc.gov.br/defesa-civil,Defesa Civil,Ativo|0,Saúde,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/banner_saude1721999756.jpg,https://www.jaraguadosul.sc.gov.br/saude,Saúde,Ativo|0,Conciliação,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/conciliacao1721999789.jpg,https://www.jaraguadosul.sc.gov.br/conciliacao,Conciliação,Ativo|0,Serviço Funerário,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/funerario1721999824.jpg,https://www.jaraguadosul.sc.gov.br/servico-funerario,Serviço Funerário,Ativo|0,Zoneamento Escolar,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/zoneamento_escolar1721999860.jpg,https://www.jaraguadosul.sc.gov.br/zoneamento-escolar,Zoneamento Escolar,Ativo|0,COAPES,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/coapes1721999882.jpg,https://www.jaraguadosul.sc.gov.br/coapes,COAPES,Ativo|0,Rede de Atenção Animal,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/rede_atencao_animal1721999924.jpg,https://www.jaraguadosul.sc.gov.br/atencao-animal,Rede de Atenção Animal,Ativo|0,Procon,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/procon1721999950.jpg,https://www.jaraguadosul.sc.gov.br/procon,Procon,Ativo|0,Turismo,https://wordpress.jaraguadosul.sc.gov.br/wp-content/plugins/menuImage-rff/img/site_turismo1721999989.jpg,https://www.jaraguadosul.sc.gov.br/turismo,Turismo,Ativo|';
        $regis = explode('|', $data);
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';
        foreach($regis as $reg){
            $dd = explode(',', $reg);
            $result = $wpdb->insert(
                $table_name,
                array(
                    'orderItems' => $dd[0],
                    'nome' => $dd[1],
                    'urlImg' => $dd[2],
                    'urlLink' => $dd[3],
                    'altText' => $dd[4],
                    'statusItem' => $dd[5],
                )
            );
            if($result){
                echo 'Sucesso regitro com nome: <strong>'.$dd[1].'</strong><br>';
            }
        }
    }

 }