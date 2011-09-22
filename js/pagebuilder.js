$(document).ready(function() {
    
    $('.deletepage').click(function() {
        text = $(this).find('a').html();
        deleteText = text.slice(-4);
        
        if(confirm('Are you sure you want to delete this '+deleteText+'?')) {
            return true;
        } else {
            return false;
        }
    });
    
    $('input[name="type"]').change(function() {
        if($('input[name="type"]:checked').val() == 'link') {
            $('.url').show();
            $('input[name="menu_text"]').focus();
        } else {
            $('.url').hide();
            $('input[name="menu_text"]').val('');
            $('input[name="url"]').val('http://');
        }
    });
    
    $('input[name="menu_text"]').keyup(function() {
        menuTextValue = $('input[name="menu_text"]').val();
        permalinkValue = menuTextValue.replace(/[^A-Za-z0-9 ]/g, "").toLowerCase().replace(/ /g, '-');
        
        $('input[name="permalink"]').val(permalinkValue);
    });
    
    if($('.sortablelist').length) {
        $(".sortablelist, .sortablelist ul").sortable({
        	placeholder: 'ui-state-highlight',
        	//connectWith: '.sortablelist, .sortablelist ul',
        	cursor: 'crosshair',
        	//items: 'ul > li',
        	start: function(event, ui) {
        	           $(ui.item).css('background-color', '#efefef');
        	           $(ui.item).css('opacity', '0.7');
        	       },
        	stop: function(event, ui) {
        	           $(ui.item).css('background-color', '#fff');
        	           $(ui.item).css('opacity', '1');
        	       },
        	handle: '.pagetitle',
            update : function () {
                updateNavigationOrder(false);
            }
        });

    }
    
    function processChildren(item) {

        $('#subpages_'+item).find('li:not(.moveformli)').each(function() {
            
            //console.log(this);
            
            //console.log($(this).hasClass('placeholder'));
            
            
            if( !$(this).hasClass('placeholder') ) {
                updateOrder['subpages_'+item].push({itemId : this.id, parent:item});
            }
            
/*
            lis = $(this).find('li:not(.placeholder)');
            
            if( lis.length ) {
                updateOrder['OL_'+last_id] = [];
                
                updateOrder['OL_'+last_id].push({itemId : this.id, parent:'0'});
                
                processChildren(this.id);
            }
*/
        });
    }
    
    function updateNavigationOrder(refreshpage) {
        updateOrder = {'parent': [] };
                        
        $('.sortablelist').children().each(function() {
            var last_id = this.id;                    
            updateOrder['parent'].push({itemId : this.id, parent:'0'});
            
            lis = $(this).find('li');
            
            if( lis.length ) {
                updateOrder['subpages_'+last_id] = [];
            
//                        $('#OL_'+last_id+' li.placeholder').hide();
                
//                        updateOrder['OL_'+last_id].push({itemId : this.id, parent:'0'});
                
                processChildren(this.id);
            }
        });
    
        //console.log(updateOrder);
               
        $.ajax({
            type: 'POST',
            url: '/pagebuilder/savepageorder/',
            data: {'pagedata':updateOrder},
            success: function(){
                if(refreshpage) {
                    window.location.reload();
                }
            }
        });
    }

    
    $('.move').click(function() {
        rowToMove = $(this).parent();
        
        isPageParent = $(rowToMove).parent().attr('id');
        if(isPageParent == 'noparent') {
            if(!confirm('You are about to move a top level page, moving this page will also move any subpages you may have. Are you sure you want to continue?')) {
                return false;
            }
        }
        
        $('#moveform').show();
        
        $('.subpages span').show();
        $('.subpages li').css('background','');
        //$('.subpages span.pagetitle').css('padding-left','0px');
        //$('.subpages span.pagetitle').css('width','280px');
        
        $(rowToMove).find('span:not(.pagetitle)').hide();
        $(rowToMove).css('background','url(/images/move.png) 0 7px no-repeat');
        //$(rowToMove).find('span.pagetitle').css('padding-left','22px');
        //$(rowToMove).find('span.pagetitle').css('width','260px');
        
        $('#moveform').appendTo(rowToMove);
    });
    
    $('#cancel-move').click(function() {
        $('#moveform').hide();
        
        hideMoveForm(this);
    });
    
    $('#accept-move').click(function() {
        $('#moveform').hide();
        
        rowToMove = $(this).parent().parent().parent().parent();
        moveInto  = $('#parent').val();
        
        console.log(moveInto);        

        if(moveInto == 0) {
            $(rowToMove).appendTo('#noparent');
        } else {
            $(rowToMove).appendTo('#subpages_page_'+moveInto);
        }
        
        updateNavigationOrder(true);
        hideMoveForm(this);
    });
    
    var hideMoveForm = function(item) {
        $(item).parent().parent().parent().parent().find('span').show();
        $(item).parent().parent().parent().parent().css('background','');
        //$(item).parent().parent().parent().parent().find('span.pagetitle').css('padding-left','0px');
        //$(item).parent().parent().parent().parent().find('span.pagetitle').css('width','280px');        
    }
    
    $('#submit_create').click(function() {
        $('#createpage').submit();
    });
    
    $('#savepagebutton').click(function() {
        $('#createpageform').attr('action','/pagebuilder/savepage/');
        $('#createpageform').attr('target','_self');
    });
    
    $('#previewbutton').click(function() {
        $('#createpageform').attr('action','/pagebuilder/previewpage/');
        $('#createpageform').attr('target','_blank');
    });
    
    $('#cancelchanges').click(function() {
        window.location = '/pagebuilder/';
    });
    
    $('.closewindow').click(function() {
        window.close();
    });
    
});

if($('#page_content').length) {
    var ckeditor1 = CKEDITOR.replace('page_content', {toolbar : 'MyToolbar', width: '690px'});
    
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor1});
/*
    var pageContentEditor = new YAHOO.widget.Editor('page_content', {
        height: '350px',
        width: '600px',
        dompath: true, //Turns on the bar at the bottom
        animate: true, //Animates the opening, closing and moving of Editor windows
        handleSubmit: true
    });
    yuiImgUploader(pageContentEditor, 'page_content', '/pagebuilder/uploadimage','image');
    pageContentEditor.render();
*/
}