$(document).ready(function() {
    
    $('.deleteitem').click(function() {
        if(confirm('Are you sure you wish to remove this item.')) {
            return true;
        } else {
            return false;
        }
    });
    
    $('.deletemember').click(function() {
        if(confirm('Are you sure you delete this member.')) {
            return true;
        } else {
            return false;
        }
    });
    
    $('#news_date').datepicker({ dateFormat: 'dd/mm/yy' });
    
});

if($('#preview_text').length) {
    var ckeditor1 = CKEDITOR.replace('preview_text', {toolbar : 'MyToolbar', width: '690px'});
    
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor1});
}

if($('#news_text').length) {
    var ckeditor2 = CKEDITOR.replace('news_text', {toolbar : 'MyToolbar', width: '690px'});
    
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor2});
    
}

if($('#full_text').length) {
    var ckeditor3 = CKEDITOR.replace('full_text', {toolbar : 'MyToolbar', width: '690px'});
   
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor3});
}

if($('#summary').length) {
    var ckeditor4 = CKEDITOR.replace('summary', {toolbar : 'Basic', width: '690px', height: '100px'});

    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor4});
}

if($('#artist_description').length) {
    var ckeditor5 = CKEDITOR.replace('artist_description', {toolbar : 'MyToolbar', width: '690px'});
   
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor5});
}