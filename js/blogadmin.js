$(document).ready(function() {
    
    $('.deletepost').click(function() {
        if(confirm('Are you sure you want to delete this post?')) {
            return true;
        } else {
            return false;
        }
    });
    
});

if($('#post_content').length) {
    var ckeditor1 = CKEDITOR.replace('post_content', {toolbar : 'BlogToolbar', width: '640px'});
    
    AjexFileManager.init({returnTo: 'ckeditor', editor: ckeditor1});
}