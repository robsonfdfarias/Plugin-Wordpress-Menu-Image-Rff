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
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();
    $table_name2 = $wpdb->prefix . 'menuImage_rff_location';
    $sql2 = "CREATE TABLE $table_name2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        statusItem varchar(15),
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql2);
    //
    $table_name = $wpdb->prefix . 'menuImage_rff';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        orderItems mediumint(9),
        nome varchar(200),
        urlImg varchar(150) NOT NULL,
        urlLink varchar(150) NOT NULL,
        altText varchar(255),
        statusItem varchar(15),
        locationId mediumint(9),
        PRIMARY KEY  (id),
        FOREIGN KEY (locationId) REFERENCES $table_name2(id)
    ) $charset_collate;";
    dbDelta($sql);
}
//Apaga a tabela ao desativar o plugin
function menuImage_rff_uninstall() {
    global $wpdb;
    // $table_name = $wpdb->prefix . 'meu_plugin_table';
    $table_name = $wpdb->prefix . 'menuImage_rff';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
    $table_name2 = $wpdb->prefix . 'menuImage_rff_location';
    $sql2 = "DROP TABLE IF EXISTS $table_name2;";
    $wpdb->query($sql2);
}

