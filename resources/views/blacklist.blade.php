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
                    <p>Enter an exact or partial match of a company name, lead’s full name, job title, or profile URL you don’t wish to target with your campaigns.</p>
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
  @for ($i = 0; $i < 5; $i++)
  <tr>
      <td><div class="d-flex align-items-center"><img src="assets/img/acc_img{{$i+1}}.png" alt=""><strong>John doe</strong></div></td>
      <td><a href="#" class="black_list_activate">Blacklisted</a></td>
      <td>
        <a href="javascript:;" type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
        <ul class="setting_list">
            <li><a href="#">Edit</a></li>
            <li><a href="#">Delete</a></li>
        </ul>   
    </td>
    </tr>
    @endfor
   
  </tbody>
</table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection