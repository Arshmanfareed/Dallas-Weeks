@extends('partials/master')
@php
    use App\Models\Role_Permission;
@endphp
<style>
    span.edit_role {
        cursor: pointer;
    }

    span.edit_role:hover {
        color: #0f0;
    }

    span.delete_role {
        cursor: pointer;
    }

    span.delete_role:hover {
        color: #f00;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@if ($is_owner)
    <script src="{{ asset('assets/js/roles&permission.js') }}"></script>
@endif
@section('content')
    <section class="blacklist team_management role_per_sec">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 filtr_desc">
                    <div class="filter_head_row filt_opt  d-flex">
                        <div class="cont">
                            <h3>Roles & permissions</h3>
                        </div>
                        @if ($is_owner)
                            <div>
                                <div class="add_btn " bis_skin_checked="1">
                                    <span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#create_new_role">
                                        <a href="javascript:;" class="">
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                        Create custom role
                                    </span>
                                </div>
                                <div class="text-center">{{ $count_role }}/10 customized roles</div>
                            </div>
                        @endif
                    </div>
                    <div class="data_row">
                        <div class="data_head">
                            <table class="data_table w-100">
                                <thead>
                                    <tr>
                                        <th width="70%">Permission</th>
                                        @if ($roles->isNotEmpty())
                                            @foreach ($roles as $role)
                                                <th class="text-center" id="{{ 'table_row_' . $role['id'] }}">
                                                    {{ $role['role_name'] }}
                                                    @if ($is_owner)
                                                        {!! $role['team_id'] == 0
                                                            ? ''
                                                            : '<span class="edit_role"><i class="fa-solid fa-pencil"></i></span> <span class="delete_role"><i class="fa-solid fa-trash"></i></span>' !!}
                                                    @endif
                                                </th>
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
                                                                <td><span class="">View Only</span></td>
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
    @if ($is_owner)
        <div class="modal fade create_sequence_modal step_form_popup invite_team_modal " id="create_new_role" tabindex="-1"
            aria-labelledby="create_new_role" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="create_new_role">Create a custom role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body" bis_skin_checked="1">
                        <form class="invite_form" id="">
                            <label for="role_name">Role name</label>
                            <input type="text" name="role_name" required>
                            <div>
                                @if ($permissions->isNotEmpty())
                                    @foreach ($permissions as $permission)
                                        <div class="row invite_modal_row ">
                                            <div class="col-lg-6 checkboxes" style="display: flex; width: 390px;">
                                                <input class="permission"
                                                    style="width: 25px; height: 25px; margin-right: 25px;" type="checkbox"
                                                    id="permission_{{ $permission['permission_slug'] }}"
                                                    name="{{ $permission['permission_slug'] }}">
                                                <label
                                                    for="permission_{{ $permission['permission_slug'] }}">{{ $permission['permission_name'] }}</label>
                                            </div>
                                            <div class="col-lg-6 switch_box" style="display: none; width: 390px;">
                                                @if ($permission->allow_view_only == 1)
                                                    <input type="checkbox"
                                                        style="width: 25px; height: 25px; margin-right: 25px;"
                                                        id="view_only_{{ $permission['permission_slug'] }}"
                                                        class="view_only switch"
                                                        name="view_only_{{ $permission['permission_slug'] }}">
                                                    <label for="view_only_{{ $permission['permission_slug'] }}"></label>
                                                    View Only
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-next">Create Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($is_owner)
        <div class="modal fade create_sequence_modal step_form_popup invite_team_modal " id="edit_role" tabindex="-1"
            aria-labelledby="edit_role" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_role">Edit role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body" bis_skin_checked="1">
                        <form class="edit_form">
                            <label for="role_name">Role name</label>
                            <input type="text" id="role_name" name="role_name" required>
                            <div>
                                @if ($permissions->isNotEmpty())
                                    @foreach ($permissions as $permission)
                                        <div class="row invite_modal_row ">
                                            <div class="col-lg-6 checkboxes" style="display: flex; width: 390px;">
                                                <input class="permission"
                                                    style="width: 25px; height: 25px; margin-right: 25px;" type="checkbox"
                                                    id="edit_permission_{{ $permission['permission_slug'] }}"
                                                    name="{{ $permission['permission_slug'] }}">
                                                <label
                                                    for="edit_permission_{{ $permission['permission_slug'] }}">{{ $permission['permission_name'] }}</label>
                                            </div>
                                            <div class="col-lg-6 switch_box" style="display: none; width: 390px;">
                                                @if ($permission->allow_view_only == 1)
                                                    <input type="checkbox"
                                                        style="width: 25px; height: 25px; margin-right: 25px;"
                                                        id="edit_view_only_{{ $permission['permission_slug'] }}"
                                                        class="view_only switch"
                                                        name="view_only_{{ $permission['permission_slug'] }}">
                                                    <label
                                                        for="edit_view_only_{{ $permission['permission_slug'] }}"></label>
                                                    View Only
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-next">Edit Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($is_owner)
        <script>
            var customRoleRoute = "{{ route('customRole') }}";
            var deleteRoleRoute = "{{ route('deleteRole', [':id']) }}";
            var getRoleRoute = "{{ route('getRole', [':id']) }}";
            var editRoleRoute = "{{ route('editRole', [':id']) }}";
        </script>
    @endif
@endsection
