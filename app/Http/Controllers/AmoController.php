<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;

class AmoController extends Controller
{
    public function amo_curl($link, $method = 'POST', $array = [])
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($array)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($array));
        }
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

        $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        $out = curl_exec($curl);

        curl_close($curl);

        return [
            'code' => $code,
            'data' => json_decode($out, true)
        ];
    }

    public function amo_auth()
    {
        $link = 'https://'. env('AMO_SUBDOMAIN', '') .'.amocrm.ru/private/api/auth.php?type=json';

        $user = [
            'USER_LOGIN' => env('AMO_LOGIN', ''),
            'USER_HASH'  => env('AMO_HASH', '')
        ];

        $auth = $this->amo_curl($link, 'POST', $user);

        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        );

        return array_key_exists((int) $auth['code'], $errors) ? false : true;
    }

    public function amo_leads(int $status, int $limit = 10)
    {
        $subdomain = env('AMO_SUBDOMAIN', '');
        $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads?limit_rows=' . $limit . '&status=' . $status;

        if ($this->amo_auth()) {

            $data = $this->amo_curl($link, 'GET');

            return $data['data']['_embedded']['items'];
        }

        return [];
    }
}
