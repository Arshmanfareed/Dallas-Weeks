<?php

namespace App\Http\Controllers;

use App\Models\LinkedinSetting;
use Illuminate\Http\Request;

class LinkedinSettingController extends Controller
{
    public function get_value_of_setting($campaign_id, $setting_slug)
    {
        $setting = LinkedinSetting::where('setting_slug', $setting_slug)->where('campaign_id', $campaign_id)->first();
        if ($setting['value'] == 'yes') {
            return true;
        } else {
            return false;
        }
    }
}
