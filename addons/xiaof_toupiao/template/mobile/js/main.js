function xfdialog(content, isclose) {

    if (isclose) {
        $("#popup-close").show();
    } else {
        $("#popup-close").hide();
    }
    $(".popup-container").html(content);
    $("#dialog").addClass("is-visible");
}

function vote(id, This) {

    $.get(
        "/app/index.php?c=entry&do=vote&m=xiaof_toupiao&i=" + window.sysinfo.uniacid + "&type=good&id=" + id
        , function (data) {
            var result = new Function('return' + data)();
			if (result.errno == 0) {
				This.html(parseInt(This.html()) + 1);
			}
			xfdialog(result.message, true);	
        });
}