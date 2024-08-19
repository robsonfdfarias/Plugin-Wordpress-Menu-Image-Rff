<?php

/**
 * Este arquivo contém a classe de upload da imagem
 */

//se chamar diretamente e não pelo wordpress, aborta
if(!defined('WPINC')){
    die();
 }



class MiRffUpload{
    function uploadImage($file){
        // $uploadedfile = $_FILES['meu_plugin_image_upload'];
        $uploadedfile = $file;

        // Defina o diretório de destino para a pasta 'img' dentro do diretório do plugin
        // $plugin_dir = plugin_dir_path(__FILE__); // Caminho absoluto para o diretório do plugin
        // $plugin_dir = str_replace('inc/', '', plugin_dir_path(__FILE__)); // Caminho absoluto para o diretório do plugin
        // $upload_dir = $plugin_dir . 'img/'; // Diretório de upload
        // $urlBase = plugins_url('img/', __FILE__);
        $upload_dir = str_replace('inc/', 'img/', plugin_dir_path(__FILE__)); // Caminho absoluto para o diretório do plugin
        $urlBase = str_replace('inc/', '', plugins_url('img/', __FILE__));
        
        // Verifica se o diretório existe; caso contrário, tenta criar
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);
        }

        // Verifica o tipo de arquivo e o tamanho, se necessário
        $file_type = wp_check_filetype($uploadedfile['name']);
        if ($file_type['ext'] !== 'jpg' && $file_type['ext'] !== 'jpeg' && $file_type['ext'] !== 'png' && $file_type['ext'] !== 'gif' && $file_type['ext'] !== 'svg') {
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

    function removeImage($img){
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
    }
}