$(document).delegate('.fakeCheck', 'click', function(e) {
    
    $(this).toggleClass("fa-square-o");
    $(this).toggleClass("fa-check-square-o");
    
    var id = "#" + $(this).attr("data-realcheck");
    
    $(id).prop("checked", !$(id).prop("checked"));
});