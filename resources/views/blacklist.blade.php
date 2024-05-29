@extends('partials/master')
@section('content')
    <section class="blacklist">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="filter_head_row d-flex">
                        <h3>Blacklist</h3>
                        <div class="filt_opt">
                            <select name="num" id="num">
                                <option value="01">10</option>
                                <option value="02">20</option>
                                <option value="03">30</option>
                                <option value="04">40</option>
                            </select>
                        </div>
                    </div>
                    <div class="filtr_desc">
                        <div class="d-flex">
                            <strong>Blacklist</strong>
                            <div class="filter">
                                <a href="#"><i class="fa-solid fa-filter"></i></a>
                                <form action="/search" method="get" class="search-form">
                                    <input type="text" name="q" placeholder="Search...">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p>Enter an exact or partial match of a company name, lead’s full name, job title, or profile URL
                            you don’t wish to target with your campaigns.</p>
                    </div>
                    <div class="data_row">
                        <div class="data_head">

                            <table class="data_table w-100">
                                <thead>
                                    <tr>
                                        <th width="80%">Keyword</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($blacklist->first()))
                                        @foreach ($blacklist as $item)
                                            <tr>
                                                @php
                                                    $asset = 'assets/img/acc_img' . 1 . '.png';
                                                @endphp
                                                <td>
                                                    <div class="d-flex align-items-center"><img src="{{ asset($asset) }}"
                                                            alt=""><strong>John doe</strong></div>
                                                </td>
                                                <td><a href="#" class="black_list_activate">Blacklisted</a></td>
                                                <td>
                                                    <a href="javascript:;" type="button" class="setting setting_btn"
                                                        id=""><i class="fa-solid fa-gear"></i></a>
                                                    <ul class="setting_list">
                                                        <li><a href="#">Edit</a></li>
                                                        <li><a href="#">Delete</a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">
                                                <div class="text-center text-danger"
                                                    style="font-size: 25px; font-weight: bold; font-style: italic;">
                                                    Not Found!
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $(".setting_list").hide();
            $(".setting_btn").on("click", function(e) {
                $(".setting_list").not($(this).siblings(".setting_list")).hide();
                $(this).siblings(".setting_list").toggle();
            });
            $(document).on("click", function(e) {
                if (!$(event.target).closest(".setting").length) {
                    $(".setting_list").hide();
                }
            });
        });
    </script>
@endsection
