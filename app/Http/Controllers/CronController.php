<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
use Illuminate\Http\JsonResponse;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use App\Models\CampaignElement;
use App\Models\ElementProperties;
use Illuminate\Support\Facades\Mail;

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
                if (isset($user_profile['contact_info']['emails'])) {
                    $email = $user_profile['contact_info']['emails'][0];
                    $lead->email = $email;
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
                    $invite_to_connect = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Connect Message') {
                                $invite_to_connect['message'] = $cp->value;
                            }
                        }
                        $invite_to_connect = $uc->invite_to_connect(new \Illuminate\Http\Request($invite_to_connect));
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
                    $message = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Message') {
                                $message['message'] = $cp->value;
                            }
                        }
                        $message = $uc->message(new \Illuminate\Http\Request($message));
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
                    $inmail_message = [
                        'account_id' => $account_id,
                        'identifier' => $user_profile['provider_id'],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Message') {
                                $inmail_message['message'] = $cp->value;
                            }
                        }
                        $inmail_message = $uc->inmail_message(new \Illuminate\Http\Request($inmail_message));
                        if ($inmail_message instanceof JsonResponse) {
                            $inmail_message = $inmail_message->getData(true);
                            if (!isset($inmail_message['error'])) {
                                $inmail_message = $inmail_message['inmail_message'];
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

    public function email_message($action, $account_id, $element, $campaign_element)
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
                if (isset($user_profile['contact_info']['emails'])) {
                    $email_message = [
                        'account_id' => $account_id,
                        'email' => $user_profile['contact_info']['emails'][0],
                    ];
                    if (isset($element) && isset($campaign_element)) {
                        $campaign_property = UpdatedCampaignProperties::where('element_id', $campaign_element->id)->get();
                        foreach ($campaign_property as $cp) {
                            $property = ElementProperties::where('id', $cp->property_id)->first();
                            if ($property->property_name == 'Body') {
                                $email_message['message'] = $cp->value;
                            }
                            if ($property->property_name == 'Subject') {
                                $email_message['subject'] = $cp->value;
                            }
                        }
                        $email_message = $uc->email_message(new \Illuminate\Http\Request($email_message));
                        if ($email_message instanceof JsonResponse) {
                            $email_message = $email_message->getData(true);
                            if (!isset($email_message['error'])) {
                                $email_message = $email_message['email_message'];
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
