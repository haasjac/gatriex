// https://stackoverflow.com/a/13522434/8328808

$(document).delegate('.ui-dialog', 'keyup', function(e) {
    var tagName = e.target.tagName.toLowerCase();

    tagName = tagName === 'input' && e.target.type === 'button' ? 'button' : tagName;

    if (e.which === $.ui.keyCode.ENTER && tagName !== 'textarea' && tagName !== 'select' && tagName !== 'button') {
        $(this).find('.ui-dialog-buttonset button').eq(0).trigger('click');

        return false;
    }
});