$(document).ready(function () {
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
    });
});
