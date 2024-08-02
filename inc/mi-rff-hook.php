<?php

/**
 * Este arquivo contém as funções de CRUD
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }



//Cria a tabela na ativação do plugin
function menuImage_rff_install() {
    global $wpdb;
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

//Função que registra na tabela do GraphQl
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