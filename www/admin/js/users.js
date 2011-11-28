$(document).ready(function() {

    $('.deleteuser').click(function() {
        if(confirm('Are you sure you wish to remove this user from the CMS.')) {
            return true;
        } else {
            return false;
        }
    });
    
});