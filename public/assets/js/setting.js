$(document).ready(function () {
<<<<<<< HEAD
    function handleTabClick(event, tabClass, paneClass) {
        event.preventDefault();
        $(tabClass).removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(paneClass).removeClass("active");
        $("#" + id).addClass("active");
    }

    $(document).on("click", ".setting_tab", function (e) {
        handleTabClick.call(this, e, ".setting_tab", ".setting_pane");
    });

    $(document).on("click", ".linkedin_setting", function (e) {
        handleTabClick.call(this, e, ".linkedin_setting", ".linkedin_pane");
=======
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
>>>>>>> seat_work
    });
});
