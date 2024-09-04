@extends('partials/master')
@php
    use App\Models\Role_Permission;
@endphp
@section('content')
    <section class="blacklist team_management role_per_sec">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 filtr_desc">
                    <div class="filter_head_row filt_opt  d-flex">
                        <div class="cont">
                            <h3>Roles & permissions</h3>
                        </div>
                        <div class="add_btn " bis_skin_checked="1">
                            <a href="javascript:;" class="" data-bs-toggle="modal" data-bs-target="#create_new_role">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                            Create custom role
                        </div>
                    </div>
                    <div class="data_row">
                        <div class="data_head">
                            <table class="data_table w-100">
                                <thead>
                                    <tr>
                                        <th width="70%">Permission</th>
                                        @if ($roles->isNotEmpty())
                                            @foreach ($roles as $role)
                                                <th>{{ $role['role_name'] }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($permissions->isNotEmpty())
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td class="per">{{ $permission['permission_name'] }}</td>
                                                @if ($roles->isNotEmpty())
                                                    @foreach ($roles as $role)
                                                        @php
                                                            $role_permission = Role_Permission::where(
                                                                'role_id',
                                                                $role['id'],
                                                            )
                                                                ->where('permission_id', $permission['id'])
                                                                ->first();
                                                        @endphp
                                                        @if (!empty($role_permission))
                                                            @if ($role_permission['view_only'] == 1)
                                                                <td><span class="check">View Only</span></td>
                                                            @elseif ($role_permission['access'] == 1)
                                                                <td><span class="check checked"></span></td>
                                                            @else
                                                                <td><span class="check unchecked"></span></td>
                                                            @endif
                                                        @else
                                                            <td><span class="check unchecked"></span></td>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade step_form_popup " id="create_new_role" tabindex="-1" aria-labelledby="create_new_role"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create_new_role">Create a custom role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body" bis_skin_checked="1">
                    <form class="step_form" action="">
                        <label for="role_name">Role name</label>
                        <input type="text" name="role_name">
                        <div>
                            @if ($permissions->isNotEmpty())
                                @foreach ($permissions as $permission)
                                    <div style="display: flex; align-items: flex-start;">
                                        <input style="width: 25px; height: 25px; margin-right: 25px;" type="checkbox"
                                            name="{{ $permission['permission_slug'] }}">
                                        <label>{{ $permission['permission_name'] }}</label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-next">Create Role</a>
                </div>
            </div>
        </div>
    </div>
@endsection
