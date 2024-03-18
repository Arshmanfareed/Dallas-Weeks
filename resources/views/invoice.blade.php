@extends('partials/master')
@section('content')
<section class="blacklist invoice_sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="filter_head_row d-flex">
                    <div class="cont">
                    <h3>Invoices</h3>
                    <p>Click on the link to download invoice</p>
                    </div>
                    
                    <div class="filt_opt d-flex">
                    <select name="name" id="name">
                        @for($i=0;$i<=5;$i++)
                            <option value="john">john{{$i}}</option>
                            @endfor
                        </select>
                        <select name="num" id="num">
                        @for($i=0;$i<=5;$i++)
                            <option value="01">{{$i}}0</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <!-- <div class="filtr_desc">
                    <div class="d-flex">
                        <strong>Team members</strong>
                        <div class="filter">
                            <form action="/search" method="get" class="search-form">
								<input type="text" name="q" placeholder="Search...">
								<button type="submit">
								<i class="fa fa-search"></i>
								</button>
							</form>
                            <a href="javascript:;" class="roles_btn">Roles & permissions</a>
                        </div>
                    </div>
                </div> -->
                <div class="data_row">
                    <div class="data_head">

<table class="data_table w-100">
  <thead>
    <tr>
      <th width="15%">Account</th>
      <th width="15%">Email</th>
      <th width="40%">Invoice data</th>
      <th width="15%">Date</th>
      <th width="15%">Download invoice</th>
    </tr>
  </thead>
  <tbody>
  @for ($i = 0; $i < 7; $i++)
  <tr>
      <td><div class="d-flex align-items-center"><img src="assets/img/acc_img{{$i+1}}.png" alt=""><strong>John doe</strong></div></td>
      <td>info@johndoe.com</td>
      <td  class="inv_data">Sed ut perspiciatis unde omnis iste natus error sit voluptatem</td>
      <td class="inv_date">28 August 2023</td>

      <td><a href="javascript:;" class="black_list_activate download">Download</a></td>
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