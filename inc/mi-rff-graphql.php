<?php
/**
 * Este arquivo inseri no graphql as tabelas deste plugin
 */

 //Se acessar esse arquivo por fora do wordpress, ele interrompe a execução
if(!defined('WPINC')){
    die();
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