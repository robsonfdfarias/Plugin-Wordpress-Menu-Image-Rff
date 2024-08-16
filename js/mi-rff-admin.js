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

    $('.down_rff_edit_location').on('click', function(e){
        e.preventDefault();
        var tdId = $(this).parent().parent().children().eq(0).html();
        var tdTitle = $(this).parent().parent().children().eq(1).html();
        var tdStatus = $(this).parent().parent().children().eq(2).html();
        var op = $('<option selected></option>').val(tdStatus).text('->'+tdStatus);
        console.log(tdTitle)
        $('#mi_rff_id_location').val(tdId)
        $('#mi_rff_title_location').val(tdTitle)
        $('#statusItem_location').prepend(op)
        $('#mi_rff_geral_edit_location').show('slow')
    })
    $('#mi_rff_btclose_edit_location').on('click', function(){
        $('#mi_rff_geral_edit_location').hide('slow')
    })

    //ITEM----------------------------------------------------------------------

    $('.mi_rff_open_edit_item').on('click', function(e){
        e.preventDefault();
        var tdId = $(this).parent().parent().children().eq(1).html();
        var orderItems = $(this).parent().parent().children().eq(2).html();
        var nome = $(this).parent().parent().children().eq(3).html();
        var urlImg = $(this).parent().parent().children().eq(4).clone();
        urlImg.children().eq(0).attr('style', 'max-width:200px;')
        var urlLink = $(this).parent().parent().children().eq(5).html();
        var altText = $(this).parent().parent().children().eq(6).html();
        var statusItem = $(this).parent().parent().children().eq(7).html();
        var locationId = $(this).parent().parent().children().eq(8);
        // console.log(urlImg)
        var op = $('<option selected></option>').val(statusItem).text('-> '+statusItem+' ');
        var opLoc = $('<option selected></option>').val(locationId.attr('id')).text('-> '+locationId.text()+' ');
        $('#item_id').val(tdId)
        $('#orderItems_item').val(orderItems)
        $('#nome_item').val(nome)
        $('#mi_rff_img_edit').html(urlImg)
        $('#urlLink_item').val(urlLink)
        $('#altText_item').val(altText)
        $('#statusItem_item').prepend(op)
        $('#mi_rff_geral_edit_item').show('slow')
        $('#locationId_edit').prepend(opLoc)
    })

    $('#mi_rff_btclose_edit_item').on('click', function(){
        $('#mi_rff_geral_edit_item').hide('slow')
    })
});