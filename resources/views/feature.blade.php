@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard feature_sec">
    <div class="container_fluid">
        <div class="row">
            <div class="col-lg-1">
                @include('partials/dashboard_sidebar_menu')
            </div>
            <div class="col-lg-11 col-sm-12">
                <div class="row">
                    <div class="col-4">
                        <div class="feature_side">
                            <h3>Feature suggestions</h3>
                            <div class="border_box">
                                <h4>Suggest a new feature</h4>
                                <p>We're listening your feedback! Let us know what you want to see next in Networked.</p>
                                <form action="#" method="post" class="comment_form">
                                    <label for="title">Title:</label>
                                    <input type="text" id="title" name="title" placeholder="Short, descriptive title" >
                                    
                                    <label for="details">Details:</label>
                                    <input type="text" id="details" name="details" placeholder="Any additional details">
                                    
                                    <input type="submit" value="Submit feedback">
                                </form>
                            </div>
                            <div class="grey_box">
                                <div class="note">
                                    <div class="d-flex justify-content-between">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <div class="note_cont">
                                            Note:Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="filt_opt d-flex justify-content-between">
                            <form action="/search" method="get" class="search-form">
                                <input type="text" name="q" placeholder="Search account here...">
                                <button type="submit">
                                <i class="fa fa-search"></i>
                                </button>
                            </form>
                            <div class="filt_opt">
                                <select name="Completed" id="Completed">
                                    <option value="01">Completed</option>
                                    <option value="02">Completed</option>
                                    <option value="03">Completed</option>
                                    <option value="04">Completed</option>
                                </select>
                                <select name="Trending" id="Trending">
                                    <option value="01">Trending</option>
                                    <option value="02">Trending</option>
                                    <option value="03">Trending</option>
                                    <option value="04">Trending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal Export leads -->
<div class="modal fade create_sequence_modal export_modal" id="export_modal" tabindex="-1" aria-labelledby="export_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Export data from all campaigns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <p class="w-75">Once the export is complete, we will send you the exported data is a CSV file. Please insert the email you would like us to use.</p>
                                <input type="email" placeholder="admin@gmail.com">
                            </div>
                        </div>
                        
                        <a href="javascript:;" class="crt_btn ">Submit<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection