@extends('partials/headerhome')
@section('content')

<!-- Banner Start -->
<section class="bannerSec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="imgbanner">
                    <img src="assets/images/bannerimg.png" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <h1>Turning connections into clients</h1>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                <div class="btn btn-blue btn-yellow">
                    <a href="javascript:;"><i class="fa-solid fa-plus"></i>get Started</a>
                </div>
                <div class="img3">
                    <img src="assets/images/img1.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner End -->
<!-- Logo Section Start -->
<section class="logoSec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="logonew owl-item owl-carousel">
                    <div class="item">
                        <img src="assets/images/logo1.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo2.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo3.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo4.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo5.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo6.png" alt="">
                    </div>
                    <div class="item">
                        <img src="assets/images/logo7.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Logo Section End -->
<!-- Connect Start -->
<section class="connectSec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Connect your linked in to start sending connections and messages now</h1>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                <div class="laptop">
                    <img src="assets/images/laptop.png" alt="">
                </div>
                <div class="btn btn-blue btn-yellow">
                    <a href="javascript:;"><i class="fa-solid fa-plus"></i>Try it now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Connect End -->
<section class="linkedin">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Manage your LinkedIn connections and Messages</h1>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
            </div>
            <div class="col-md-6">
                <div class="conntImg">
                    <img src="assets/images/conn.png" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="condition">
                    <h3>Conditions</h3>
                    <ul>
                        <li><img src="assets/images/ic1.png" alt="">Custom condition</li>
                        <li><img src="assets/images/ic2.png" alt="">If connected</li>
                        <li><img src="assets/images/ic3.png" alt="">If email is opened</li>
                        <li><img src="assets/images/ic4.png" alt="">If has imported email</li>
                        <li><img src="assets/images/ic5.png" alt="">If has verified email</li>
                        <li><img src="assets/images/ic6.png" alt="">If Free InMail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="tabsSec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="tabsinner">
                    <nav>
                        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="iinkedIn-automations" data-bs-toggle="tab" data-bs-target="#iinkedIn-automation" type="button" role="tab" aria-controls="iinkedIn-automation" aria-selected="true">LinkedIn automation</button>
                            <button class="nav-link" id="email-automations" data-bs-toggle="tab" data-bs-target="#email-automation" type="button" role="tab" aria-controls="email-automation" aria-selected="false">Email automation</button>
                            <button class="nav-link" id="email-verification" data-bs-toggle="tab" data-bs-target="#email-verifications" type="button" role="tab" aria-controls="email-verifications" aria-selected="false">Email verification</button>
                            <button class="nav-link" id="images-personalizations" data-bs-toggle="tab" data-bs-target="#images-personalization" type="button" role="tab" aria-controls="images-personalization" aria-selected="false">Images personalization</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="iinkedIn-automation" role="tabpanel" aria-labelledby="iinkedIn-automations">
                            <h2>LinkedIn Automation</h2>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="assets/images/chartnew.png" alt="">
                                </div>
                                <div class="col-md-6">
                                    <img src="assets/images/chart.png" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="email-automation" role="tabpanel" aria-labelledby="email-automations">
                            <h2>Email automation</h2>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="assets/images/chartnew.png" alt="">
                                </div>
                                <div class="col-md-6">
                                    <img src="assets/images/chart.png" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="email-verifications" role="tabpanel" aria-labelledby="email-verification">
                            <h2>Email verification</h2>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="assets/images/chartnew.png" alt="">
                                </div>
                                <div class="col-md-6">
                                    <img src="assets/images/chart.png" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="images-personalization" role="tabpanel" aria-labelledby="images-personalizations">
                            <h2>Images personalization</h2>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="assets/images/chartnew.png" alt="">
                                </div>
                                <div class="col-md-6">
                                    <img src="assets/images/chart.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn btn-blue btn-yellow">
                    <a href="javascript:;"><i class="fa-solid fa-plus"></i>Explore</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="getInSec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Get in touch with your clients now</h3>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,</p>
                <div class="btn btn-blue btn-yellow btn-chat">
                    <a href="javascript:;"><i class="fa-regular fa-comment"></i>Start Chat</a>
                </div>
            </div>
            <div class="col-md-6">
                <img src="assets/images/getimg.png" alt="">
            </div>
        </div>
    </div>
</section>
<section class="testimonialSec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2>Testimonials</h2>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                <div class="testimonialInner owl-carousel owl-item">
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonialInner2 owl-carousel owl-item">
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- John doe</h4>
                            </div>
                            <p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>
                            <div class="star-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
