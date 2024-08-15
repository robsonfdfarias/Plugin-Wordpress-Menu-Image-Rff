jQuery(document).ready(function($){
    "use strict";
    //é possível pegar o input com classe teste usando o $
    // $('input.teste').val('');
    // alert('dkvkjvdbnk')
    $('#mi_rff_btclose_cad_item').on('click', function(){
        $('#mi_rff_geral_insert_item').hide(500)
    })
    $('#mi_rff_bt_open_cad_item').on('click', function(){
        $('#mi_rff_geral_insert_item').show(500)
    })


    $('#mi_rff_btclose_cad_location').on('click', function(){
        $('#mi_rff_geral_insert_location').hide(500)
    })
    $('#mi_rff_bt_open_cad_location').on('click', function(){
        $('#mi_rff_geral_insert_location').show(500)
    })
});