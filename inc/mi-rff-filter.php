<?php
/**
 * classe de filtro que armazena o filtro selecionado e guarda em um arquivo txt
 */

 $fileNameMi = MI_RFF_DIR_IMG.'filtro.txt';
 class FilterMiRff {
    function save_filter($filtro){
        global $fileNameMi;
        if(isset($filtro)){
            $arq = fopen($fileNameMi, 'w');
            if($arq){
                fwrite($arq, $filtro);
                fclose($arq);
            }else{
                echo 'Erro ao abrir o aquivo!';
            }
        }
    }

    function read_filter($conn){
        global $fileNameMi;
        if(!file_exists($fileNameMi)){
            $this->save_filter("0");
        }
        //Ler o arquivo em um array
        $linhas = file($fileNameMi, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $val=null;
        if($linhas===false){
            $itemDados = $conn->menuImage_rff_recuperar_dados();
        }else{
            if(sizeof($linhas)>0){
                if($linhas[0]!=0){
                    $val = $linhas[0];
                    $itemDados = $conn->menuImage_rff_recupera_por_id_localizacao($val);
                }else{
                    $itemDados = $conn->menuImage_rff_recuperar_dados();
                }
            }else{
                $itemDados = $conn->menuImage_rff_recuperar_dados();
            }
        }
        return ['val'=>$val, 'itemDados'=>$itemDados];
    }
 }