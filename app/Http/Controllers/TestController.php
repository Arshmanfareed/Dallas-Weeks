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
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;

use function PHPUnit\Framework\isEmpty;

class TestController extends Controller
{
    public function base()
    {
    }
}
