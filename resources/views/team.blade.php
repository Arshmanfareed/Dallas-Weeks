@extends('partials/master')
@section('content')
<section class="blacklist team_management">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="filter_head_row d-flex">
                    <div class="cont">
                        <h3>Team Management</h3>
                        <p>Invite team members and manage team permissions.</p>
                    </div>
                    
                    <div class="filt_opt d-flex">
                        <div class="add_btn ">
                            <a href="javascript:;" class="" data-toggle="modal" data-target="#"><i class="fa-solid fa-plus"></i></a>Add team member
                        </div>
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
                        <strong>Team members</strong>
                        <div class="filter">
                            <form action="/search" method="get" class="search-form">
                                <input type="text" name="q" placeholder="Search...">
                                <button type="submit">
                                <i class="fa fa-search"></i>
                                </button>
                            </form>
                            <a href="/team-rolesandpermission" class="roles_btn">Roles & permissions</a>
                        </div>
                    </div>
                </div>
                <div class="data_row">
                    <div class="data_head">
                        <table class="data_table w-100">
                            <thead>
                                <tr>
                                    <th width="75%">Name</th>
                                    <th width="10%">Email</th>
                                    <th width="5%">Role</th>
                                    <th width="5%">Status</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td><div class="d-flex align-items-center"><img src="/assets/img/acc_img{{$i+1}}.png" alt=""><strong>John doe</strong></div></td>
                                    <td>info@johndoe.com</td>
                                    <td>Executive</td>
                                    <td><a href="javascript:;" class="black_list_activate active">Active</a></td>
                                    <td>
                                        <a href="javascript:;" type="button" class="setting setting_btn" id="" data-bs-toggle="modal" data-bs-target="#invite_team_modal"><i class="fa-solid fa-gear"></i></a>
                                       <!--  <ul class="setting_list">
                                            <li><a href="javascript:;">Edit</a></li>
                                            <li><a href="javascript:;">Delete</a></li>
                                        </ul> -->
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
<div class="modal fade create_sequence_modal invite_team_modal" id="invite_team_modal" tabindex="-1" aria-labelledby="invite_team_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Invite a team member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row invite_modal_row">
                        <div class="col-lg-6">
                            <label for="name">Name</label>
                            <input type="text" name="name" placeholder="Enter team member's name">
                        </div>
                        <div class="col-lg-6">
                            <label for="email">Email</label>
                            <input type="email" name="email" placeholder="Enter team member's email" >
                        </div>
                        <span>Select one or more roles for your team member</span>
                        <div class="col-lg-6">                            
                            <div class="checkboxes">
                                <div class="check">
                                    <input type="checkbox" id="verified" name="verified" checked="">
                                    <label for="verified">Owner</label>
                                </div>
                                <div class="check">
                                    <input type="checkbox" id="verified1" name="verified1" checked="">
                                    <label for="verified1">Admin</label>
                                </div>
                                <div class="check">
                                    <input type="checkbox" id="verified2" name="verified2" checked="">
                                    <label for="verified2">Editor</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 add_col">
                            <div class="d-flex justify-content-end">
                                <div class="add_btn">
                                    <a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#contact_modal"><i class="fa-solid fa-plus"></i></a>Create custom role
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border_box">
                                <h6>Manage payment system</h6>    
                                <p>This is a global option that enables access to invoices and adding seats.</p>
                                <div class="switch_box"><input type="checkbox" class="switch" id="switch0"><label for="switch0">Toggle</label></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border_box">
                                <h6>Manage global blacklist</h6>    
                                <p>This is a global option that enables managing the global blacklist on the team level.</p>
                                <div class="switch_box"><input type="checkbox" class="switch" id="switch1"><label for="switch1">Toggle</label></div>
                            </div>
                        </div>
                        
                        <a href="javascript:;" class="crt_btn">Invite member<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection