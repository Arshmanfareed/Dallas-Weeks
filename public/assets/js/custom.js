$('.logonew').owlCarousel({
	items:5,
	loop:true,
	nav:false,
	stagePadding:100,
	margin:0,
	autoplay:true,
	responsive:{
		0:{
			items:5,
			stagePadding:0
		},
		600:{
			items:1
		},
		768:{
			items:5
		},
		1400:{
			items:5
		}
	}
});

$('.testimonialInner').owlCarousel({
	items:5,
	loop:true,
	nav:false,
	stagePadding:250,
	margin:26,
	autoplay:true,
	responsive:{
		0:{
			items:2,
			stagePadding:0
		},
		600:{
			items:1
		},
		768:{
			items:2
		},
		1400:{
			items:3
		}
	}
});

$('.testimonialInner2').owlCarousel({
	items:5,
	loop:true,
	nav:false,
	stagePadding:150,
	margin:26,
	autoplay:true,
	responsive:{
		0:{
			items:2,
			stagePadding:0
		},
		600:{
			items:1
		},
		768:{
			items:2
		},
		1400:{
			items:3
		}
	}
});

$('.offerCarousel').owlCarousel({
	items:5,
	loop:false,
	nav:false,
	stagePadding:50,
	margin:26,
	autoplay:true,
	responsive:{
		0:{
			items:2,
			stagePadding:0
		},
		600:{
			items:1
		},
		768:{
			items:2
		},
		1400:{
			items:3
		}
	}
});

$('.testimonialPricing').owlCarousel({
	items:1,
	loop:true,
	nav:false,
	margin:26,
	autoplay:true,
	responsive:{
		0:{
			items:1,
			stagePadding:0
		},
		600:{
			items:1
		},
		768:{
			items:1
		},
		1400:{
			items:1
		}
	}
});

jQuery( document ).ready(function() {
function reveal() {
  var reveals = document.querySelectorAll(".reveal");

  for (var i = 0; i < reveals.length; i++) {
    var windowHeight = window.innerHeight;
    var elementTop = reveals[i].getBoundingClientRect().top;
    var elementVisible = 150;

    if (elementTop < windowHeight - elementVisible) {
      reveals[i].classList.add("active");
    } else {
      reveals[i].classList.remove("active");
    }
  }
}

window.addEventListener("scroll", reveal);

});
  
  
jQuery( document ).ready(function() {
    function revealleft() {
  var revealsleft = document.querySelectorAll(".revealleft");

  for (var i = 0; i < revealsleft.length; i++) {
    var windowHeight = window.innerHeight;
    var elementTop = revealsleft[i].getBoundingClientRect().top;
    var elementVisible = 150;

    if (elementTop < windowHeight - elementVisible) {
      revealsleft[i].classList.add("active");
    } else {
      revealsleft[i].classList.remove("active");
    }
  }
}

window.addEventListener("scroll", revealleft);

});
  
  
  
jQuery( document ).ready(function() {
    function revealright() {
  var revealsright = document.querySelectorAll(".revealright");

  for (var i = 0; i < revealsright.length; i++) {
    var windowHeight = window.innerHeight;
    var elementTop = revealsright[i].getBoundingClientRect().top;
    var elementVisible = 150;

    if (elementTop < windowHeight - elementVisible) {
      revealsright[i].classList.add("active");
    } else {
      revealsright[i].classList.remove("active");
    }
  }
}

window.addEventListener("scroll", revealright);
});
$(".logonew").owlCarousel({
    items: 5,
    loop: true,
    nav: false,
    stagePadding: 100,
    margin: 0,
    autoplay: true,
    responsive: {
        0: {
            items: 5,
            stagePadding: 0,
        },
        600: {
            items: 1,
        },
        768: {
            items: 5,
        },
        1400: {
            items: 5,
        },
    },
});

$(".testimonialInner").owlCarousel({
    items: 5,
    loop: true,
    nav: false,
    stagePadding: 250,
    margin: 26,
    autoplay: true,
    responsive: {
        0: {
            items: 2,
            stagePadding: 0,
        },
        600: {
            items: 1,
        },
        768: {
            items: 2,
        },
        1400: {
            items: 3,
        },
    },
});

$(".testimonialInner2").owlCarousel({
    items: 5,
    loop: true,
    nav: false,
    stagePadding: 150,
    margin: 26,
    autoplay: true,
    responsive: {
        0: {
            items: 2,
            stagePadding: 0,
        },
        600: {
            items: 1,
        },
        768: {
            items: 2,
        },
        1400: {
            items: 3,
        },
    },
});

$(".offerCarousel").owlCarousel({
    items: 5,
    loop: false,
    nav: false,
    stagePadding: 50,
    margin: 26,
    autoplay: true,
    responsive: {
        0: {
            items: 2,
            stagePadding: 0,
        },
        600: {
            items: 1,
        },
        768: {
            items: 2,
        },
        1400: {
            items: 3,
        },
    },
});

$(".testimonialPricing").owlCarousel({
    items: 1,
    loop: true,
    nav: false,
    margin: 26,
    autoplay: true,
    responsive: {
        0: {
            items: 1,
            stagePadding: 0,
        },
        600: {
            items: 1,
        },
        768: {
            items: 1,
        },
        1400: {
            items: 1,
        },
    },
});

jQuery(document).ready(function () {
    function reveal() {
        var reveals = document.querySelectorAll(".reveal");

        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 150;

            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            } else {
                reveals[i].classList.remove("active");
            }
        }
    }

    window.addEventListener("scroll", reveal);
});

jQuery(document).ready(function () {
    function revealleft() {
        var revealsleft = document.querySelectorAll(".revealleft");

        for (var i = 0; i < revealsleft.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = revealsleft[i].getBoundingClientRect().top;
            var elementVisible = 150;

            if (elementTop < windowHeight - elementVisible) {
                revealsleft[i].classList.add("active");
            } else {
                revealsleft[i].classList.remove("active");
            }
        }
    }

    window.addEventListener("scroll", revealleft);
});

jQuery(document).ready(function () {
    function revealright() {
        var revealsright = document.querySelectorAll(".revealright");

        for (var i = 0; i < revealsright.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = revealsright[i].getBoundingClientRect().top;
            var elementVisible = 150;

            if (elementTop < windowHeight - elementVisible) {
                revealsright[i].classList.add("active");
            } else {
                revealsright[i].classList.remove("active");
            }
        }
    }

    window.addEventListener("scroll", revealright);
});
