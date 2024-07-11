<?php

namespace App\Http\Controllers;

use App\Models\LinkedinSetting;
use Illuminate\Http\Request;

class LinkedinSettingController extends Controller
{
    public function get_value_of_setting($campaign_id, $setting_slug)
    {
        $setting = LinkedinSetting::where('campaign_id', $campaign_id)->where('setting_slug', $setting_slug)->first();
        if ($setting !== null && ($setting['value'] == 'yes')) {
            return true;
        } else {
            return false;
        }
    }
}
