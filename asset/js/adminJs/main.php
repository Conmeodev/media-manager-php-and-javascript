
function setThumb(id,root) {
    $.ajax({
        method: "POST",
        url: "/ajax/ajaxFile.php",
        data: {act: "setThumbFolders",id:id,root:root},
        success: function(response) {
            var res = $.parseJSON(response);
            _alert(res.title);
        }
    });
}
function delThumb(id) {
    $.ajax({
        method: "POST",
        url: "/ajax/ajaxFile.php",
        data: {act: "delThumb",id:id},
        success: function(response) {
            var res = $.parseJSON(response);
            _alert(res.title);
        }
    });
}