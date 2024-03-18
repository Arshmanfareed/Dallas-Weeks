@extends('partials/master')
@section('content')
<section class="blacklist setting_sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="filter_head_row d-flex">
                    <div class="cont">
                        <h3>Roles & permissions</h3>
                        <p>Your email was confirmed: johndoe@gmail.com</p>
                    </div>
                </div>
                <div class="setting_pass">
                    <strong>Change password</strong>
                    <form action="change_password.php" method="post" class="password_form d-flex">
                        <div class="pass_inp">
                            <label for="old_password">Old Password:</label>
                            <input type="password" id="old_password" name="old_password" required placeholder="Enter old password">
                        </div>
                        <div class="pass_inp">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required placeholder="Enter New password">
                        </div>
                        <div class="pass_inp">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Enter Confirm password">
                        </div>
                        <input type="submit" value="Change Password" class="pass_btn">
                    </form>
                </div>
                
            <div class="setting_pass API_Key">
                <div class="d-flex justify-content-between"><strong>API Key</strong><p>Use this API key to access your Skylead account when using Skylead API.</p></div>
                <form action="change_password.php" method="post" class="password_form api_key_form">
                    <label for="old_password">Your Api key</label>
                    <input type="text" id="api_key" name="api_key" required placeholder="000000-11223344-556677-00000">
                </form>
            </div>
            <div class="setting_pass API_Key del_pass">
                <div class="d-flex"><strong>Delete account</strong><p>Are you sure you want to delete this account?</p></div>
                <button class="del_btn">Delete Account</button>
            </div>
            </div>
        </div>
        
    </div>
</div>
</div>
</section>
@endsection