$(document).ready(function () {
    $(".setting_tab").on("click", function (e) {
        e.preventDefault();
        $(".setting_tab").removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(".setting_pane").removeClass("active");
        $("#" + id).addClass("active");
    });
    $(".linkedin_setting").on("click", function (e) {
        e.preventDefault();
        $(".linkedin_setting").removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(".linkedin_pane").removeClass("active");
        $("#" + id).addClass("active");
    });
});
