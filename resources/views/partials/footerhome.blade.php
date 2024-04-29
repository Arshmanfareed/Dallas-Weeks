<footer>
    <div class="container-fluid">
        <div class="connentin">
            <div class="row">
                <div class="col-md-5 revealleft">
                    <h3>Connect your LinkedIn Today</h3>
                    <p>Connect your LinkedIn to start setting meetings today!</p>
                </div>
                <div class="col-md-7 revealright">
                    <div class="startedrating">
                        <div class="btn btn-blue btn-yellow">
                            <a href="javascript:;"><i class="fa-solid fa-plus"></i>Get started</a>
                        </div>
                        <div class="ratingimg">
                            <img src="{{ asset('assets/images/rating.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="widthinner">
            <div class="row">
                <div class="col-md-3 revealleft">
                    <div class="col1">
                        <a class="navbar-brand" href="{{ URL('/') }}"><img
                                src="{{ asset('assets/images/logo.png') }}" alt=""></a>
                        <div class="socialicons">
                            <ul>
                                <li><a href="https://www.facebook.com/networkedsite.admin/" target="_blank"><i
                                            class="fa-brands fa-facebook"></i></a></li>
                                <li><a href="https://www.linkedin.com/company/networked-turning-connections-into-clients/"
                                        target="_blank"><i class="fa-brands fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                        <p>© 2023 <a href="javascript:;">Networked.</a> All Rights Reserved.</p>
                    </div>
                </div>
                <div class="col-md-3 revealleft">
                    <div class="col2">
                        <h4>Quick links</h4>
                        <ul>
                            <li><a href="{{ URL('/') }}">Home</a></li>
                            <li><a href="{{ URL('/about') }}">About Us</a></li>
                            <li><a href="{{ URL('/pricing') }}">Pricing</a></li>
                            <li><a href="{{ URL('/faq') }}">FAQ's</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 revealright">
                    <div class="col3">
                        <h4>Contact Us</h4>
                        <p><a href="mailto:info@networked.com">admin@networked.site</a></p>
                        <p><a href="javascript:;">1250 Wood Branch Park <br> Dr. Houston, TX. 77079</a></p>
                    </div>
                </div>
                <div class="col-md-3 revealright">
                    <div class="col4">
                        <h4>Newsletter</h4>
                        <p>Subscribe to our newsletter to get updates on account maintenance, discounts, etc!</p>
                        <form action="javascript:;">
                            <input type="email" placeholder="Enter your email">
                            <input class="btn btn-blue" type="submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader">
        <div class="loader-inner"></div>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    document.getElementById('captureButton').addEventListener('click', function() {

        html2canvas(document.getElementById('capture')).then(function(canvas) {
            console.log('adsadsa');
            var img = canvas.toDataURL();
            console.log(img);
            // Do something with the image like displaying it on the page or saving it
            var screenshotImage = document.createElement('img');
            screenshotImage.src = img;
            // document.body.appendChild(screenshotImage);
            document.querySelector('.canvas').appendChild(screenshotImage);
        });
    });
</script>

<script src="{{ asset('assets/js/custom.js') }}"></script>
<script>
    window.addEventListener("load", function() {
        // When the page is fully loaded, hide the loader
        var loader = document.getElementById("loader");
        loader.style.display = "none";
    });

    document.addEventListener("DOMContentLoaded", function() {
        // When DOM content is loaded (before images and other resources), show the loader
        var loader = document.getElementById("loader");
        loader.style.display = "block";
    });
</script>
</body>

</html>
