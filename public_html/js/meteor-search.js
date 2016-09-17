$(function(){

    $('form#search-meteor-by-name').on('submit', function() {
        window.location = 'archive/search_name/name/'+$('form#search-meteor-by-name input[name="meteor_name"]').val();
        return false;
    });
    
});