<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response;
use \Propaganistas\LaravelPhone\PhoneNumber;

if (!function_exists('active_menu')) {
    /**
     * set class active in current url
     * @param $url
     * @return string
     */
    function active_menu($url)
    {
        if (is_array($url)) {
            foreach ($url as $item) {
                if (is_url($item)) {
                    return 'active';
                }
            }
        } else {
            if (Request::is($url) || Request::is($url . '/*')) {
                if (in_array('admin', explode('/', $url))) {
                    return 'active';
                } else {
                    return 'active';
                }
            }
        }

        return '';
    }
}

if (!function_exists('is_url')) {
    /**
     * @param $url
     * @return mixed
     */
    function is_url($url)
    {
        return (Request::is($url));
    }
}

if (!function_exists('json')) {
    /**
     * Return json
     *
     * @param null $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \App\Http\JsonResponse
     */
    function json($data = null, $status = 200, $headers = [], $options = 0)
    {
        return (new \App\Http\JsonResponse($data, get_error_code($status), $headers, $options))->withCode($status);
    }
}

if (!function_exists('get_error_code')) {
    /**
     * @param $code
     * @param bool $getMessage
     * @return array
     */
    function get_error_code($code, $getMessage = false)
    {
        $customCodes = [
            Response::HTTP_BAD_REQUEST => [],
            Response::HTTP_NOT_FOUND => [
                USER_NOT_FOUND_CODE => trans('api.user_not_found'),
                USER_ACTIVE_CODE_NOT_FOUND => trans('api.user_active_code_not_found'),
                FOLLOW_NOT_FOUND_CODE => trans('api.record_not_found'),
                FOLLOW_USER_NOT_FOUND_CODE => trans('api.record_not_found'),
                SHARE_CODE_NOT_FOUND => trans('api.share_code_not_found')
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY => [
                USER_WRONG_CREDENTIAL_CODE => trans('api.wrong_credentials'),
                USER_LOGIN_FAIL_CODE => trans('api.failed'),
                USER_NOT_ACTIVE_CODE => trans('api.user_have_not_activated'),
                USER_ALREADY_ACTIVATED_CODE => trans('api.user_is_already_activated'),
                FOLLOW_LIST_CARD_FAIL => trans('api.follows_fail'),
                FOLLOW_CARD_FAIL => trans('api.follow_fail'),
                DELETE_USER_EXPIRED_ACTIVATION => trans('api.user_have_expired_activated'),
                FOLLOW_USER_NOT_YOUR_SELF => trans('api.can_not_follow_yourself')
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR => [
                SERVER_ERROR_CODE => trans('api.server_error'),
                NOTIFY_FAIL => trans('api.send_notify_fail')
            ]
        ];

        $result = ['code' => $code, 'message' => null];
        foreach ($customCodes as $parentCode => $customCode) {
            if (in_array($code, array_keys($customCode))) {
                $result = ['code' => $parentCode, 'message' => $customCode[$code]];
            }
        }

        return $getMessage ? $result['message'] : $result['code'];
    }
}

if (!function_exists('validateUniquePhone')) {
    /**
     * Validate phone number unique.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    function validateUniquePhone(\Illuminate\Http\Request $request)
    {
        if (!$request->has('phone_number') && !$request->isMethod('post')) {
            return;
        }

        $userId = !empty($request->user()) ? $request->user()->id : 'NULL';

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone_number' => 'required|max:20|unique:users,phone_number,' . $userId . ',id,deleted_at,NULL,is_active,1',
        ]);

        $errors = $validator->errors()->toArray();

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax() || $request->pjax()) {
                $response = new \Illuminate\Http\JsonResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $response = \response()->redirectTo(url()->previous())
                    ->withInput($request->all())
                    ->withErrors($errors);
            }

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        return;
    }
}

if (!function_exists('classActiveSegment')) {
    function classActiveSegment($value, $output = "active")
    {
        if (URL::current() == URL::to($value)) {
            return $output;
        }
    }
}

if (!function_exists('classActivePath')) {
    function classActivePath($path, $class = 'active')
    {
        $active = Request::is($path);
        if (!$active) {
            $active = !empty(Request::segment(1)) ? Request::segment(1) == $path : false;
        }

        if (!$active) {
            foreach (explode('/', $path) as $key => $name) {
                $flag = !empty(Request::segment($key+1)) ? Request::segment($key+1) == $name : false;
                if (!$flag) {
                    break;
                }
            }
            $active = $flag;
        }

        return $active ? $class : '';
    }
}

if (!function_exists('get_device_types')) {
    function get_device_types()
    {
        return [DEVICE_TYPE_WEB, DEVICE_TYPE_IOS, DEVICE_TYPE_ANDROID];
    }
}

if (!function_exists('get_sending_notify_types')) {
    function get_sending_notify_types()
    {
        return [SENDING_NOTIFY_TYPE_EMAIL, SENDING_NOTIFY_TYPE_PUSH, SENDING_NOTIFY_TYPE_SMS];
    }
}

if (!function_exists('get_countries')) {
    function get_countries()
    {
        $countries = \App\Country::getCountries();
        return $countries->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
    }
}

if (!function_exists('get_notification_type')) {
    function get_notification_type($type)
    {
        switch ($type) {
            case \App\Notifications\SendActivation::class:
                $result = NOTIFICATION_TYPE_SEND_ACTIVATION;
                break;
            case \App\Notifications\UpdateCard::class:
                $result = NOTIFICATION_TYPE_UPDATE_CARD;
                break;
            default:
                $result = NOTIFICATION_TYPE_EMPTY;
        }

        return $result;
    }
}

if (!function_exists('get_notification_type_text')) {
    function get_notification_type_text($type)
    {
        switch ($type) {
            case \App\Notifications\SendActivation::class:
                $result = trans('site.notification_type_user_activation');
                break;
            case \App\Notifications\UpdateCard::class:
                $result = trans('site.notification_type_card_link_update');
                break;
            default:
                $result = null;
        }

        return $result;
    }
}

if (!function_exists('get_number')) {
    function get_number($number) {
        return preg_replace('/\D/', '', $number);
    }
}

if (!function_exists('get_phone_codes')) {
    function get_phone_codes() {
        return [
            [
                'country' => 'Vietnam',
                'code' => '+84',
                'flag' => asset('images/uploads/flag/vietnam.png')
            ],
            [
                'country' => 'Malaysia',
                'code' => '+60',
                'flag' => asset('images/uploads/flag/malaysia.jpg')
            ],
            [
                'country' => 'Singapore',
                'code' => '+65',
                'flag' => asset('images/uploads/flag/singapore.png')
            ]
        ];
    }
}

if (!function_exists('convert_phones_with_E164')) {
    function convert_phones_with_E164(array $contacts) {
        $countryCodes = array_keys(\Iso3166\Codes::$phoneCodes);
//        $countryCodes = COUNTRY_PHONE_CODES;
        $results = [];

        foreach ($contacts as $contact) {
            $tmp = [];
            foreach ($countryCodes as $code) {
                $tmp[] = (string) PhoneNumber::make($contact, $code);
            }

            $results[$contact] = array_unique($tmp);
        }

        return $results;
    }
}

if (!function_exists('phone_code_selector')) {
    function phone_code_selector($selected = null) {
        $class = 'form-control';
        $name = 'phone_code';
        $countries = \Iso3166\Codes::$countries;

        return sprintf('<select class="%s" name="%s">', $class, $name)
            . implode(array_map(function($phoneCodeKey, $phoneCodeNumber) use ($countries, $selected) {
                $country = $countries[$phoneCodeKey];

                return ($phoneCodeNumber == $selected)
                    ? sprintf('<option value="%s" selected>%s</option>', $phoneCodeNumber, $country)
                    : sprintf('<option value="%s">%s</option>', $phoneCodeNumber, $country);
            }, array_keys(\Iso3166\Codes::$phoneCodes), \Iso3166\Codes::$phoneCodes))
            . '</select>';
    }
}

if (!function_exists('custom_find_array')) {
    function custom_find_array($item, array $arrays) {
        foreach ($arrays as $k => $array) {
            if (in_array($item, $array)) {
                return $k;
            }
        }

        return $item;
    }
}


if (!function_exists('countries')) {
    /**
     * Get country list
     *
     * @return array
     */
    function countries()
    {
        return [
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Afghanistan'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Albania'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Algeria'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'American Samoa'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Andorra'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Angola'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Anguilla'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Antigua and Barbuda'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Argentina'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Armenia'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Aruba'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Australia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Austria'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Azerbaijan'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Bahamas'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Bahrain'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Bangladesh'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Barbados'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Belarus'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Belgium'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Belize'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Benin'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Bermuda'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Bhutan'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Bolivia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Bosnia and Herzegovina'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Botswana'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Brazil'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Brunei Darussalam'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Bulgaria'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Burkina Faso'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Burundi'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Cambodia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Cameroon'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Canada'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Cape Verde'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Cayman Islands'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Central African Republic'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Chad'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Chile'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'China'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Christmas Island'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Cocos (Keeling) Islands'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Colombia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Comoros'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Democratic Republic of the Congo'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Congo'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Cook Islands'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Costa Rica'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => "Côte D'ivoire"
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Croatia'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Cuba'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Cyprus'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Czech Republic'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Denmark'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Djibouti'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Dominica'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Dominican Republic'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'East Timor'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Ecuador'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Egypt'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'El Salvador'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Equatorial Guinea'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Eritrea'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Estonia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Ethiopia'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Falkland Islands'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Faroe Islands'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Fiji'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Finland'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'France'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'French Guiana'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'French Polynesia'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'French Southern'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Gabon'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Gambia'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Georgia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Germany'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Ghana'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Gibraltar'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Great Britain'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Greece'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Greenland'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Grenada'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Guadeloupe'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Guam'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Guatemala'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Guinea'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Guinea-Bissau'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Guyana'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Haiti'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Holy See'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Honduras'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Hong Kong'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Hungary'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Iceland'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'India'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Indonesia'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Iran'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Iraq'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Ireland'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Israel'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Italy'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Ivory Coast'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Jamaica'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Japan'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Jordan'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Kazakhstan'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Kenya'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Kiribati'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'North Korea'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'South Korea'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Kuwait'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Kyrgyzstan'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Lao'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Latvia'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Lebanon'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Lesotho'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Liberia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Libya'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Liechtenstein'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Lithuania'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Luxembourg'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Macau'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Macedonia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Madagascar'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Malawi'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Malaysia'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Maldives'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Mali'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Malta'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Marshall Islands'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Martinique'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Mauritania'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Mauritius'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Mayotte'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Mexico'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Micronesia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Moldova'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Monaco'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Mongolia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Montenegro'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Montserrat'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Morocco'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Mozambique'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Myanmar'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Namibia'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Nauru'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Nepal'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Netherlands'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Netherlands Antilles'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'New Caledonia'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'New Zealand'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Nicaragua'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Niger'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Nigeria'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Niue'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Northern Mariana Islands'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Norway'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Oman'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Pakistan'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Palau'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Palestinian'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Panama'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Papua New Guinea'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Paraguay'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Peru'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Philippines'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Pitcairn Island'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Poland'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Portugal'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Puerto Rico'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Qatar'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Reunion Island'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Romania'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Russian Federation'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Rwanda'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Saint Kitts and Nevis'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Saint Lucia'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Saint Vincent and the Grenadines'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Samoa'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'San Marino'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Sao Tome and Principe'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Saudi Arabia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Senegal'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Serbia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Seychelles'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Sierra Leone'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Singapore'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Slovakia'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Slovenia'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Solomon Islands'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Somalia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'South Africa'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'South Sudan'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Spain'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Sri Lanka'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Sudan'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Suriname'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Swaziland'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Sweden'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Switzerland'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Syria'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Taiwan'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Tajikistan'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Tanzania'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Thailand'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Tibet'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Timor-Leste'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Togo'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Tokelau'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Tonga'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Trinidad and Tobago'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Tunisia'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Turkey'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Turkmenistan'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Turks and Caicos Islands'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Tuvalu'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Uganda'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Ukraine'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'United Arab Emirates'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'United Kingdom'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'United States'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Uruguay'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Uzbekistan'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Vanuatu'
            ],
            [
                'continent' => CONTINENT_EUROPE,
                'name' => 'Vatican City State'
            ],
            [
                'continent' => CONTINENT_AMERICA,
                'name' => 'Venezuela'
            ],
            [
                'continent' => CONTINENT_ASIA,
                'name' => 'Vietnam'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Virgin Islands (British)'
            ],
            [
                'continent' => REGION_CARIBBEAN,
                'name' => 'Virgin Islands (U.S.)'
            ],
            [
                'continent' => CONTINENT_OCEANIA,
                'name' => 'Wallis and Futuna Islands'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Western Sahara'
            ],
            [
                'continent' => REGION_MIDDLE_EAST,
                'name' => 'Yemen'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Zambia'
            ],
            [
                'continent' => CONTINENT_AFRICA,
                'name' => 'Zimbabwe'
            ]
        ];
    }
}