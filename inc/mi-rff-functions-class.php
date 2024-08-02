<?php

/**
 * Este arquivo contém as funções de CRUD
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }


if(file_exists( MI_RFF_CORE_INC.'mi-rff-upload-image.php' )){
    require_once( MI_RFF_CORE_INC.'mi-rff-upload-image.php' );
}

$mi_rff_upload = new MiRffUpload();

class MiRffConection {
    function __construct(){}

    //Grava os dados na tabela
    function menuImage_rff_gravar_dados($nome, $urlImg, $urlLink, $altText) {
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';
        
        $wpdb->insert(
            $table_name,
            array(
                'nome' => $nome,
                'urlImg' => $urlImg,
                'urlLink' => $urlLink,
                'altText' => $altText,
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
    function menuImage_rff_editar_dados($id, $nome, $urlImg, $urlLink, $altText) {
        global $wpdb;
        // $table_name = $wpdb->prefix . 'meu_plugin_table';
        $table_name = $wpdb->prefix . 'menuImage_rff';

        $wpdb->update(
            $table_name,
            array(  // Um array associativo onde as chaves são os nomes das colunas e os valores são os novos dados a serem inseridos
                'nome' => $nome,
                'urlImg' => $urlImg,
                'urlLink' => $urlLink,
                'altText' => $altText,
            ),
            array('id' => $id), // Condição para atualizar (WHERE id = $id)
            array('%s'), // Tipo de dado dos valores novos (%s indica que o valor é uma string)
            array('%d') // Tipo de dado da condição (%d indica que o valor é um inteiro)
        );
    }

    

 }