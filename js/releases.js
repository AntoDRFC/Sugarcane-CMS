$(document).ready(function() {
    
    $("#itunes_link").click(function() {
		$("#itunes_exit").dialog({
			height: 350,
			width: 600,
			draggable: false,
			resizable: true,
			modal: true
		});
		
		return false;
    });
    
    $("#nothanks").click(function() {
		$("#itunes_exit").dialog('close');
    });
    
    $("#newsletter_signup").submit(function() {
        var successful = false;
        var name = $("#input_name").val();
        var email = $("#input_email").val();
        
        $.ajax({
            type: 'POST',
            url: '/releases/postnewsletter',
            async: false,
            //url: 'http://www.sugarcanenews.co.uk/index.php?action=subscribe&mode=subscribe_error&lists=4&codes=21&s=71b8f4c91921e25092de624c38a95691&email=' + email,
            data: { 'email': email, 'name': name },
            success: function(anto) {
                successful = true;
            }
        });
        
        if(successful == true) {
            var destination = $("#destinationUrl").val();
            window.open(destination);
            $("#itunes_exit").dialog('close');
            
            $("#input_name").val('');
            $("#input_email").val('');
        }
        
        return false;
    });
    
});