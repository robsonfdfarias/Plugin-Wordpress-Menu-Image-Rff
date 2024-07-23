<?php

/**
 * Este arquivo contém as funções de CRUD
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }


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
    //pega a imagem para excluir
    $imgParts = explode('/', $img);
    $imgPath = MI_RFF_DIR_IMG.$imgParts[(sizeof($imgParts)-1)];
    // echo '<h1>Url da img a ser Excluida: '.$imgPath.'</h1>';
    if(file_exists($imgPath)){
        if(unlink($imgPath)){
            // echo '<h2>Imagem '.$imgPath.' excluída com sucesso!</h2>';
            echo '<div class="notice notice-success is-dismissible"><p>Imagem excluída com sucesso!</p></div>';
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>Não foi possível excluir a imagem!</p></div>';
        }
    }else{
        echo '<div class="notice notice-failure is-dismissible"><p>Imagem não encontrada!</p></div>';
    }
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
