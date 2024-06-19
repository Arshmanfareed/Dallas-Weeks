<?php

namespace App\Http\Controllers;

use App\Models\CampaignSchedule;
use App\Models\ScheduleDays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleCampaign extends Controller
{
    private function sort_Days($days_array)
    {
        $selectedDays = array();
        $selectedDays['mon'] = $days_array['mon'];
        $selectedDays['tue'] = $days_array['tue'];
        $selectedDays['wed'] = $days_array['wed'];
        $selectedDays['thurs'] = $days_array['thurs'];
        $selectedDays['fri'] = $days_array['fri'];
        $selectedDays['sat'] = $days_array['sat'];
        $selectedDays['sun'] = $days_array['sun'];
        return $selectedDays;
    }

    function createSchedule(Request $request)
    {
        $user_id = Auth::user()->id;
        $all = $request->all();
        $name = $request->schedule_name == null ? '' : $request->schedule_name;
        unset($all['schedule_name']);
        $selectedDays = array();
        $timing = array();
        foreach ($all as $key => $value) {
            if (str_contains($key, '_selected_day')) {
                $str = str_replace('_selected_day', '', $key);
                $selectedDays[$str] = $value;
                unset($all[$key]);
            } else if (str_contains($key, '_end_time')) {
                $str = str_replace('_end_time', '', $key);
                $timing[$str]['end_time'] = $value;
                unset($all[$key]);
            } else if (str_contains($key, '_start_time')) {
                $str = str_replace('_start_time', '', $key);
                $timing[$str]['start_time'] = $value;
                unset($all[$key]);
            }
        }
        $selectedDays = $this->sort_Days($selectedDays);
        $campaign_schedule = new CampaignSchedule();
        $campaign_schedule->schedule_name = $name;
        $campaign_schedule->created_at = now();
        $campaign_schedule->updated_at = now();
        $campaign_schedule->user_id = $user_id;
        $campaign_schedule->save();
        foreach ($selectedDays as $key => $value) {
            $schedule_days = new ScheduleDays();
            $schedule_days->schedule_id = $campaign_schedule->id;
            $schedule_days->start_time = $timing[$key]['start_time'] == null ? '00:00:00' : $timing[$key]['start_time'];
            $schedule_days->end_time = $timing[$key]['end_time'] == null ? '00:00:00' : $timing[$key]['end_time'];
            $schedule_days->created_at = now();
            $schedule_days->updated_at = now();
            $schedule_days->schedule_day = $key;
            $schedule_days->is_active = $value == 'true' ? '1' : '0';
            $schedule_days->save();
        }
        $schedules = CampaignSchedule::where('user_id', $user_id)->orWhere('user_id', 0)->orderBy('created_at')->get();
        foreach ($schedules as $schedule) {
            $schedule['Days'] = ScheduleDays::where('schedule_id', $schedule->id)->orderBy('id')->get();
        }
        return response()->json(['success' => true, 'schedules' => $schedules]);
    }

    function filterSchedule($search)
    {
        $user_id = Auth::user()->id;
        $schedules = CampaignSchedule::where('user_id', $user_id);
        if ($search != 'null') {
            $schedules = $schedules->where('schedule_name', 'LIKE', '%' . $search . '%');
        }
        $schedules = $schedules->orWhere('user_id', 0);
        if ($search != 'null') {
            $schedules = $schedules->where('schedule_name', 'LIKE', '%' . $search . '%');
        }
        $schedules = $schedules->orderBy('created_at')->get();
        foreach ($schedules as $schedule) {
            $schedule['Days'] = ScheduleDays::where('schedule_id', $schedule->id)->orderBy('id')->get();
        }
        if (count($schedules) != 0) {
            return response()->json(['success' => true, 'schedules' => $schedules]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
