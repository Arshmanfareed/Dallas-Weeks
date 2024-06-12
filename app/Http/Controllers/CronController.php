<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
use Illuminate\Http\JsonResponse;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use App\Models\CampaignElement;
use App\Models\ElementProperties;

class CronController extends Controller
{
    public function view_profile($action, $account_id)
    {
        $lead = Leads::where('id', $action->lead_id)->first();
        $url = $lead->profileUrl;
        $uc = new UnipileController();
        $profile = [
            'account_id' => $account_id,
            'profile_url' => $url,
        ];
        $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
        if ($user_profile instanceof JsonResponse) {
            $user_profile = $user_profile->getData(true);
            if (!isset($user_profile['error'])) {
                $user_profile = $user_profile['user_profile'];
                if (isset($user_profile['first_name']) && isset($user_profile['last_name'])) {
                    $name = $user_profile['first_name'] . ' ' . $user_profile['last_name'];
                    $lead->title_company = $name;
                }
                if (isset($user_profile['name'])) {
                    $name = $user_profile['name'];
                    $lead->title_company = $name;
                }
                if (isset($user_profile['contact_info']['phones'])) {
                    $contact = $user_profile['contact_info']['phones'][0];
                    $lead->contact = $contact;
                }
                if (isset($user_profile['phone'])) {
                    $contact = $user_profile['phone'];
                    $lead->contact = $contact;
                }
                $lead->save();
                if (isset($lead->id)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function invite_to_connect($action, $account_id, $element, $campaign_element)
    {
        $lead = Leads::where('id', $action->lead_id)->first();
        $url = $lead->profileUrl;
        $uc = new UnipileController();
        $profile = [
            'account_id' => $account_id,
            'profile_url' => $url,
        ];
        $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
        if ($user_profile instanceof JsonResponse) {
            $user_profile = $user_profile->getData(true);
            if (!isset($user_profile['error'])) {
                $user_profile = $user_profile['user_profile'];
                if (isset($user_profile['provider_id']) && $user_profile['is_relationship'] === true) {
                    $invite = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Connect Message') {
                                $invite['message'] = $cp->value;
                                $invite_to_connect = $uc->invite_to_connect(new \Illuminate\Http\Request($invite));
                                if ($invite_to_connect instanceof JsonResponse) {
                                    $invite_to_connect = $invite_to_connect->getData(true);
                                    if (!isset($invite_to_connect['error'])) {
                                        $invite_to_connect = $invite_to_connect['invitaion'];
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function message($action, $account_id, $element, $campaign_element)
    {
        $lead = Leads::where('id', $action->lead_id)->first();
        $url = $lead->profileUrl;
        $uc = new UnipileController();
        $profile = [
            'account_id' => $account_id,
            'profile_url' => $url,
        ];
        $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
        if ($user_profile instanceof JsonResponse) {
            $user_profile = $user_profile->getData(true);
            if (!isset($user_profile['error'])) {
                $user_profile = $user_profile['user_profile'];
                if (isset($user_profile['provider_id']) && $user_profile['is_relationship'] === true) {
                    $invite = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Message') {
                                $invite['message'] = $cp->value;
                                $message = $uc->message(new \Illuminate\Http\Request($invite));
                                if ($message instanceof JsonResponse) {
                                    $message = $message->getData(true);
                                    if (!isset($message['error'])) {
                                        $message = $message['message'];
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function inmail_message($action, $account_id, $element, $campaign_element)
    {
        $lead = Leads::where('id', $action->lead_id)->first();
        $url = $lead->profileUrl;
        $uc = new UnipileController();
        $profile = [
            'account_id' => $account_id,
            'profile_url' => $url,
        ];
        $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
        if ($user_profile instanceof JsonResponse) {
            $user_profile = $user_profile->getData(true);
            if (!isset($user_profile['error'])) {
                $user_profile = $user_profile['user_profile'];
                if (isset($user_profile['provider_id']) && $user_profile['is_relationship'] === true) {
                    $message = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Message') {
                                $invite['message'] = $cp->value;
                                $message = $uc->message(new \Illuminate\Http\Request($invite));
                                if ($message instanceof JsonResponse) {
                                    $message = $message->getData(true);
                                    if (!isset($message['error'])) {
                                        $message = $message['inmail_message'];
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
