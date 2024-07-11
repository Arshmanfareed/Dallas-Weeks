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
use Exception;

class CronController extends Controller
{
    public function view_profile($action, $account_id)
    {
        try {
            $lead = Leads::where('id', $action->lead_id)->first();
            $url = $lead->profileUrl;
            $uc = new UnipileController();
            $profile = [
                'account_id' => $account_id,
                'profile_url' => $url,
                'notify' => true,
            ];
            $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
            if ($user_profile instanceof JsonResponse) {
                $user_profile = $user_profile->getData(true);
                if (!isset($user_profile['error'])) {
                    return true;
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function invite_to_connect($action, $account_id, $element, $campaign_element)
    {
        try {
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
                    if (isset($user_profile['provider_id'])) {
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
                                    return true;
                                } else {
                                    throw new Exception($invite_to_connect['error']);
                                }
                            } else {
                                throw new Exception('Invite to connect is not instance of');
                            }
                        } else {
                            if (!isset($element)) {
                                throw new Exception('Element is not saved');
                            } else {
                                throw new Exception('Campaign Element is not saved');
                            }
                        }
                    } else {
                        throw new Exception('User do not have provider_id');
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function message($action, $account_id, $element, $campaign_element)
    {
        try {
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
                                    return true;
                                } else {
                                    throw new Exception($message['error']);
                                }
                            } else {
                                throw new Exception('Message is not instance of');
                            }
                        } else {
                            if (!isset($element)) {
                                throw new Exception('Element is not saved');
                            } else {
                                throw new Exception('Campaign Element is not saved');
                            }
                        }
                    } else {
                        if (!isset($user_profile['provider_id'])) {
                            throw new Exception('User do not have provider_id');
                        } else {
                            throw new Exception('User is not in relation');
                        }
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function inmail_message($action, $account_id, $element, $campaign_element)
    {
        try {
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
                                    return true;
                                } else {
                                    throw new Exception($inmail_message['error']);
                                }
                            } else {
                                throw new Exception('In Mail Message is not instance of');
                            }
                        } else {
                            if (!isset($element)) {
                                throw new Exception('Element is not saved');
                            } else {
                                throw new Exception('Campaign Element is not saved');
                            }
                        }
                    } else {
                        if (!isset($user_profile['provider_id'])) {
                            throw new Exception('User do not have provider_id');
                        } else {
                            throw new Exception('User is not in relation');
                        }
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function follow($action, $account_id)
    {
        try {
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
                    if (isset($user_profile['provider_id'])) {
                        $follow = [
                            'account_id' => $account_id,
                            'identifier' => $user_profile['provider_id'],
                        ];
                        $follow_user = $uc->follow(new \Illuminate\Http\Request($follow));
                        if ($follow_user instanceof JsonResponse) {
                            $follow_user = $follow_user->getData(true);
                            if (!isset($follow_user['error'])) {
                                return true;
                            } else {
                                throw new Exception($follow_user['error']);
                            }
                        } else {
                            throw new Exception('In Mail Message is not instance of');
                        }
                    } else {
                        throw new Exception('User do not have provider_id');
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function email_message($action, $account_id, $element, $campaign_element)
    {
        try {
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
                                    return true;
                                } else {
                                    throw new Exception($email_message['error']);
                                }
                            } else {
                                throw new Exception('Email Message is not instance of');
                            }
                        } else {
                            if (!isset($element)) {
                                throw new Exception('Element is not saved');
                            } else {
                                throw new Exception('Campaign Element is not saved');
                            }
                        }
                    } else {
                        throw new Exception('Email not found of User');
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function if_connected($action, $account_id)
    {
        try {
            $lead = Leads::where('id', $action->lead_id)->first();
            $url = $lead->profileUrl;
            $uc = new UnipileController();
            $profile = [
                'account_id' => $account_id,
                'profile_url' => $url
            ];
            $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
            if ($user_profile instanceof JsonResponse) {
                $user_profile = $user_profile->getData(true);
                if (!isset($user_profile['error'])) {
                    if ($user_profile['is_relationship'] == true) {
                        return 'true';
                    } else {
                        return 'false';
                    }
                } else {
                    throw new Exception($user_profile['error']);
                }
            } else {
                throw new Exception('User Profile is not instance of');
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function if_email_is_opened($action)
    {
        try {
            $lead = Leads::where('id', $action['lead_id'])->first();
            if ($lead) {
                if (!is_null($lead['email']) && $lead['email'] != '') {
                    return 'true';
                } else {
                    return 'false';
                }
            } else {
                return 'false';
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function if_has_imported_email($action)
    {
        try {
            $lead = Leads::where('id', $action['lead_id'])->first();
            if ($lead) {
                if (!is_null($lead['email']) && $lead['email'] != '') {
                    return 'true';
                } else {
                    return 'false';
                }
            } else {
                return 'false';
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function if_has_verified_email($action)
    {
        try {
            $lead = Leads::where('id', $action['lead_id'])->first();
            if ($lead) {
                if (!is_null($lead['email']) && $lead['email'] != '') {
                    return 'true';
                } else {
                    return 'false';
                }
            } else {
                return 'false';
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function if_free_inmail($action)
    {
        try {
            $lead = Leads::where('id', $action['lead_id'])->first();
            if ($lead) {
                if (!is_null($lead['email']) && $lead['email'] != '') {
                    return 'true';
                } else {
                    return 'false';
                }
            } else {
                return 'false';
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }
}
