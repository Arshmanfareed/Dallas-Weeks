{
    /* <script type="text/javascript" src="https://js.stripe.com/v2/"></script> */
}

// <script type="text/javascript">

$(document).ready( function() {
    $('.close').on('click', function() {
        $('.alert').remove();
    });
});

$(function () {
    /*------------------------------------------
    --------------------------------------------
    Stripe Payment Code
    --------------------------------------------
    --------------------------------------------*/

    var $form = $(".require-validation");

    $("form.require-validation").bind("submit", function (e) {
        var $form = $(".require-validation"),
            inputSelector = [
                "input[type=email]",
                "input[type=password]",
                "input[type=text]",
                "input[type=file]",
                "textarea",
            ].join(", "),
            $inputs = $form.find(".required").find(inputSelector),
            $errorMessage = $form.find("div.error"),
            valid = true;
        $errorMessage.addClass("hide");

        $(".has-error").removeClass("has-error");
        $inputs.each(function (i, el) {
            var $input = $(el);
            if ($input.val() === "") {
                $input.parent().addClass("has-error");
                $errorMessage.removeClass("hide");
                e.preventDefault();
            }
        });

        if (!$form.data("cc-on-file")) {
            e.preventDefault();
            Stripe.setPublishableKey($form.data("stripe-publishable-key"));
            Stripe.createToken(
                {
                    number: $(".card-number").val(),
                    cvc: $(".card-cvc").val(),
                    exp_month: $(".card-expiry-month").val(),
                    exp_year: $(".card-expiry-year").val(),
                },
                stripeResponseHandler
            );
        }
    });

    /*------------------------------------------
    --------------------------------------------
    Stripe Response Handler
    --------------------------------------------
    --------------------------------------------*/
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $(".error")
                .removeClass("hide")
                .find(".alert")
                .text(response.error.message);
        } else {
            /* token contains id, last4, and card type */
            var token = response["id"];

            $form.find("input[type=text]").empty();
            $form.append(
                "<input type='hidden' name='stripeToken' value='" +
                    token +
                    "'/>"
            );
            $form.get(0).submit();
        }
    }
});

const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");
const addExperienceBtn = document.querySelector(".add-exp-btn");
const experiencesGroup = document.querySelector(".experiences-group");
const btnComplete = document.querySelector(".btn-complete");
// btnComplete.addEventListener("click", () => {
//     document.getElementsByTagName('form').submit
// })
let formStepsNum = 0;
let experienceNum = 1;

// addExperienceBtn.addEventListener("click", () => {
//     experienceNum++;
//     let text = `
//         <hr>
//     <div class='experience-item'>
//         <div class='input-group' >
//         <label for='title'>Company name</label>
//         <input type='text' name='title[]' id='title'>
//     </div>
//     <div class='input-group'>
//         <label for='start-date'>Start date</label>
//         <input type='date' name='start-date[]' id='start-date'>
//     </div>
//     <div class='input-group'>
//         <label for='end-date'>End date</label>
//         <input type='date' name='nd-date[]' id='end-date'>
//     </div>
//     <div class='input-group'>
//         <label for='job-description'>Description</label>
//         <textarea name='job-description[]' id='job-description' cols='42' rows='6'></textarea>
//     </div>
// </div > `
//     experiencesGroup.insertAdjacentHTML('beforeend', text);
// })

function updateFormSteps() {
    formSteps.forEach((formStep) => {
        formStep.classList.contains("active") &&
            formStep.classList.remove("active");
    });
    formSteps[formStepsNum].classList.add("active");
}

function updateProgressBar() {
    progressSteps.forEach((progressStep, idx) => {
        if (idx < formStepsNum + 1) {
            progressStep.classList.add("active");
        } else {
            progressStep.classList.remove("active");
        }
    });

    const progressActive = document.querySelectorAll(".progress-step.active");
    progress.style.width =
        ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
}

nextBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
        formStepsNum++;
        updateFormSteps();
        updateProgressBar();
        console.log("kk");
    });
});

prevBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
        formStepsNum--;
        updateFormSteps();
        updateProgressBar();
        console.log("kk");
    });
});

// Menu Active Class
var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
jQuery("header  ul a").each(function () {
    if (this.href === path) {
        jQuery(this).addClass("active");
    }
});

// Darkmode
// jQuery("li.darkmode a").click(function () {
//     jQuery("body").toggleClass("darkmode");
// });

// Add Class to body
var path = window.location.pathname; // Get the current path

// Remove leading slash from the path and replace any other slashes with underscores
var pathClass = path.replace(/\//g, "_").replace(/^_/, "");

// Add the processed path as a class to the body
jQuery("body").addClass(pathClass);

// Fadein Fade Out switch Account
jQuery("header nav.navbar .right_nav ul .acc i").click(function () {
    jQuery(".right_nav ul .acc .switch_acc").fadeIn();
});
jQuery(".switch_acc .switch_head .btn-close").click(function () {
    jQuery(".right_nav ul .acc .switch_acc").fadeOut();
});

jQuery(".messages_box a.message_filter").click(function () {
    jQuery(".messages_box .msg_filter_cont").fadeToggle("slow");
});

jQuery(function ($) {
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
    jQuery(".sidebar_menu  ul a").each(function () {
        if (this.href === path) {
            jQuery(this).addClass("active");
        }
    });
});
// Dark mode
$(document).ready(function () {
    // Check if user has a preference for dark mode
    if (sessionStorage.getItem("lightMode") === "enabled") {
        enableDarkMode();
    }

    // Toggle dark mode on button click
    $("#darkModeToggle").on("click", function () {
        if ($("body").hasClass("light-mode")) {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    });
});

function enableDarkMode() {
    $("body").addClass("light-mode");
    sessionStorage.setItem("lightMode", "enabled");
}

function disableDarkMode() {
    $("body").removeClass("light-mode");
    sessionStorage.setItem("lightMode", null);
}
