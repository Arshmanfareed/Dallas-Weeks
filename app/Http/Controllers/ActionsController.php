<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use Illuminate\Http\Request;
use App\Models\ImportedLeads;
use App\Models\LeadActions;
use App\Models\Leads;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Models\SeatInfo;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;

class ActionsController extends Controller
{
}
