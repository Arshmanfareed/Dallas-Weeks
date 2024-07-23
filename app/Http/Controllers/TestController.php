<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SeatInfo;
use GuzzleHttp\Client;
use App\Models\LeadActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Http\Controllers\CronController;
use App\Models\Leads;
use App\Models\ImportedLeads;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use Illuminate\Http\JsonResponse;

use function PHPUnit\Framework\isEmpty;

class TestController extends Controller
{
    public function base()
    {
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->first();
        $request = [
            'account_id' => $seat['account_id'],
        ];
        $uc = new UnipileController();
        $count = $uc->get_connection_count(new \Illuminate\Http\Request($request));
        $count = $count->getData(true)['count'];
        dd($count);
    }
}
