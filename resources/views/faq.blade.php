@extends('partials/headerhome')
@section('content')
<!-- Banner Start -->
<section class="bannerSec aboutSec faqSec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Frequently Asked <br> Questions</h1>
                <section class="faqSection">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <span>01</span>Have any other questions?  
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>Contact admin@networked.site</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <span>02</span>What if I haven't set any meetings?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>Although consistency in using the platform is typically key in setting meetings, sometimes there may be other factors that are deterring prospects from replying to your messages:</p>
                                            <ul>
                                                <li>Does your message prompt a response? This may seem like a silly question, however a lot of mistakes we see in clients is that their initial message to their prospect doesn’t require a response. We encourage having an initial message that is more personable, and begins with a question. It can be something as simple as "Hey, ____! How is 2024 going for you so far?" </li>
                                                <li>Is your linked in updated? If you plan on using linked in to source prospects, you must consider what they see when they click on your profile. Is your headshot up to date? Do you have a cover photo? Is your about section and your profile up to date? Have you posted recently? All of these are factors that play a role into whether or not the prospect may reply. </li>
                                                <li>Are you connecting with prospects that may find you relevant? When using Networked to source prospects, you must consider the audience you are reaching out to. Is this audience active on linked in/ will they even check your message?  Do you have any commonalities with this prospect that would make them more eager to reply (alma mater, location, etc)?</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <span>03</span>Why is networked a 3-month commitment?  
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>Just like prospecting in the real world, consistency is key. By allowing Networked to send connections and messages each day, the number of meetings you set will gradually increase over time. Although some advisors experience sets right away, 3 months is typically the time frame it takes to start seeing a more consistent influx of meetings.  </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-6">
                <div class="imgbanner">
                    <img src="assets/images/faqimg.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner End -->
<section class="getInSec">
    <div class="container">
        <div class="row">
            <div class="col-md-6 revealleft">
                <h3>Compliance Friendly</h3>
                <p>Our team works closely with compliance within the financial advising space to insure we are implementing successful strategies to create a safe environment for communication for both clients and advisors.</p>
                <div class="btn btn-blue btn-yellow btn-chat">
                    <a href="javascript:;"><i class="fa-regular fa-comment"></i>Start Chat</a>
                </div>
            </div>
            <div class="col-md-6 reveal">
                <img src="assets/images/getimg.png" alt="">
            </div>
        </div>
    </div>
</section>
<section class="testimonialSec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="reveal">Testimonials</h2>
                <p class="reveal">Hear from the users of our platform!</p>
                <div class="testimonialInner owl-carousel owl-item revealleft">
                    <div class="item">
                        <div class="testimonialBox">
                            <div class="titleImg">
                                <img src="assets/images/test1.png" alt="">
                                <h4>- Dallas W.</h4>
                            </div>
                            <p>"Networked has allowed my business to grow substantially with automated connecting and messaging."</p>
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
                                <h4>- Peyton C.</h4>
                            </div>
                            <p>"Networked has been extremely accessible to use and makes keeping track of statistics for the business easy. I have set so many meetings!"</p>
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
                                <h4>- Stephen M.</h4>
                            </div>
                            <p>"The platform has saved a lot of my time by automatically sending connections. It’s a lifesaver! Within my first 6 months of using Networked, I closed over 150k of premium strictly from using the platform!"</p>
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
                                <h4>- Faith C.</h4>
                            </div>
                            <p>"This has kept my day-to-day tasks concise with the message templates. I highly recommend it!"</p>
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
                                <h4>- Evan W.</h4>
                            </div>
                            <p>"Such an easy way to get additional meetings set, we use this platform for every member of my team!"</p>
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