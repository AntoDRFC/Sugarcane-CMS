$(document).ready(function() {
    
    /* turns <input class="datefield" type="text" /> into a jQuery UI date select field */
    if(typeof datepicker == 'function') {
        $('.datefield').datepicker({ dateFormat: 'dd/mm/yy' });
    }

    $('#addHeader').click(function() {
        var header = $('.headerWrapper:first').clone();
        
        header.find('input').val('');
        header.find('div.headerLeft').html('<p>New Image</p>');
        
        $('#headers').append(header);
        $('.removeheader').show();
    });
    
    $('.removeheader').live('click', function() {
        $(this).parent().parent().remove();
        
        var numberOfHeaders = $('.headerWrapper').size();
        if(numberOfHeaders == 1) {
            $('.removeheader').hide();
        }
                
        console.log(numberOfHeaders);
    });
    
});