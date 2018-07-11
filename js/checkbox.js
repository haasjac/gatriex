$(document).delegate('.fakeCheck', 'click', function(e) {
    
    $(this).toggleClass("fa-square");
    $(this).toggleClass("fa-check-square");
    
    var id = "#" + $(this).attr("data-realcheck");
    
    $(id).prop("checked", !$(id).prop("checked"));
});