<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Please Verify Your Email Address</title>
</head>

<body style="background:#f9f9f9">
    <div style="max-width:640px;margin:0 auto;background:transparent;">
        <table style="width:100%;background:transparent" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" style="padding:40px 0">
                    <a href="{{ url('/') }}" target="_blank">
                        <img src="{{ $message->embed(public_path('assets/images/logo.png')) }}" alt="Logo"
                            style="width:138px;">
                    </a>
                </td>
            </tr>
        </table>
        <div style="max-width:640px;margin:0 auto;background:#e3c935">
            <table style="width:100%;background:#e3c935" align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td
                        style="text-align:center;padding:20px;color:white;font-family:Arial;font-size:36px;font-weight:600">
                        Welcome to Networked!
                    </td>
                </tr>
            </table>
        </div>
        <div style="max-width:640px;margin:0 auto;background:#ffffff;padding:25px;">
            <table style="width:100%;background:#ffffff" align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td
                        style="padding:40px 70px;text-align:left;color:#737f8d;font-family:Arial;font-size:16px;line-height:24px">
                        <img src="https://ci3.googleusercontent.com/meips/ADKq_NaUfXTO0mHgCWj1tAsqKguHaoXz0Ss0opZuJKvy8srOghJn6F6cH0MlgfVdf1A0ddKcZHeiqTGUflG7cefwHwFL_8do3r34aFnTp6ikAKDCcQrRuNWhIJT1i-z54lAXybf2N-9Gh-oA3iU=s0-d-e1-ft#https://linkedin-aws.s3.us-east-2.amazonaws.com/d204450a-baa7-474f-8379-9978b17f7648"
                            alt="Party Wumpus" style="display:block;margin:0 auto;width:50%">
                        <h2 style="font-weight:500;font-size:20px;color:#4f545c">Hey {{ $user->name }},</h2>
                        <p>Thanks for registering an account with Networked! Get ready for a new level of automation.
                        </p>
                        <p>Before we get started, we'll need to verify your email.</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:10px 25px">
                        <a href="{{ route('verify_an_Email', ['email' => $user->email]) }}"
                            style="display:inline-block;padding:15px 19px;background:#0080ff;color:white;text-decoration:none;border-radius:3px;font-size:15px">
                            Verify Email
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <div
            style="max-width:640px;margin:20px auto;text-align:center;color:#99aab5;font-family:Arial;font-size:12px;padding:26px;">
            Sent by Networked • <a href="{{ url('/') }}" style="color:#1eb0f4;text-decoration:none">Check our
                website</a>
        </div>
    </div>
</body>

</html>
