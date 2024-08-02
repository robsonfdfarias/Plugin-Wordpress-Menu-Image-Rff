<?php

/**
 * Este arquivo contém as funções de install, unistall e para adicionar a tabela no graphql
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
        orderItems mediumint(9),
        nome varchar(200),
        urlImg varchar(150) NOT NULL,
        urlLink varchar(150) NOT NULL,
        altText varchar(255),
        statusItem varchar(15),
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
            'orderItems' => [
                'type' => 'String',
                'description' => __( 'Ordem em que os itens devem aparecer', 'your-textdomain' ),
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
            'statusItem' => [
                'type' => 'String',
                'description' => __( 'Status do item do menu image, pode ser Ativo ou Inativo', 'your-textdomain' ),
            ],
        ],
    ] );

    register_graphql_field( 'RootQuery', 'menuImage_rff', [
        'type' => [ 'list_of' => 'CustomTableType' ],
        'description' => __( 'Query de consulta da tabela', 'your-textdomain' ),
        'args' => [
            'id' => [
                'type' => 'ID',
                'description' => __( 'ID of the item', 'your-textdomain' ),
            ],
            'orderItems' => [
                'type' => 'String',
                'description' => __( 'Ordem em que os itens devem aparecer', 'your-textdomain' ),
            ],
            'nome' => [
                'type' => 'String',
                'description' => __( 'Nome do item do menu image', 'your-textdomain' ),
            ],
            'statusItem' => [
                'type' => 'String',
                'description' => __( 'Status do item do menu image, pode ser Ativo ou Inativo', 'your-textdomain' ),
            ],
        ],
        'resolve' => function( $root, $args, $context, $info ) {
            global $wpdb;
            // $table_name = $wpdb->prefix . 'custom_table';
            $table_name = $wpdb->prefix . 'menuImage_rff';
            $where_clauses = [];
            if(!empty($args['id'])){
                $where_clauses[] = $wpdb->prepare("id = %d", $args['id']);
            }
            if(!empty($args['nome'])){
                $where_clauses[] = $wpdb->prepare("nome = %s", $args['nome']);
            }
            if(!empty($args['statusItem'])){
                $where_clauses[] = $wpdb->prepare("id = %s", $args['statusItem']);
            }
            $orderItem = '';
            if(!empty($args['orderItems'])){
                $orderItem = "ORDER BY orderItems ".$args['orderItems'];
            }
            $where_sql = '';
            if(!empty($where_clauses) && sizeof($where_clauses)>0){
                $where_sql = 'WHERE '.implode(' AND ', $where_clauses);
            }
            $sql = "SELECT * FROM $table_name $where_sql $orderItem";
            $results = $wpdb->get_results( $sql );
            return $results;
        }
    ] );
}