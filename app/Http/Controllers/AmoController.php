<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AmoController extends Controller
{
    /*
     * id Статусы
     *  142 => Успешно реализовано
     *  16536847 => Пробная доставка
     *  16566964 => В работе
     *  27248140 => Доставлено
     *
     *
     * */

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

    public function apiUpdate()
    {
        /*$subdomain = env('AMO_SUBDOMAIN', '');
        $link = 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/set';

        $leads['request']['leads']['update'] = array(
            array(
                'id'=>3698752,
                'name'=>'Deal with a monkey',
                //'date_create'=>1298904164, //optional
                'last_modified'=>1375110129,
                'status_id'=>142,
                'price'=>602041,
                'responsible_user_id'=>109999,
                'custom_fields'=>array(
                    array(
                        'id'=>427493, # id поля типа numeric
                        'values'=>array(
                            array(
                                'value'=>65535 # сюда передается только числовое значение (float, int). Значения float передаются с точкой, например: 27.3
                            )
                        )
                    ),
                    array(
                        'id'=>427494, # id поля типа checkbox
                        'values'=>array(
                            array(
                                'value'=>1 # допустимые значения 1 или 0
                            )
                        )
                    ),
                    array(
                        'id'=>427495, #id поля типа select
                        'values'=>array(
                            array(
                                'value'=>1240662 # одно из enum значений
                            )
                        )
                    )
                )
            ),
            array(
                'id'=>3698754,
                'name'=>'Keep Calm',
                //'date_create'=>1298904164, //optional
                'last_modified'=>1375110129,
                'status_id'=>7087607,
                'price'=>1008200,
                'responsible_user_id'=>109999,
                'custom_fields'=>array(
                    array(
                        #Нестандартное дополнительное поле типа "мультисписок", которое мы создали
                        'id'=>426106,
                        'values'=>array(
                            1237755,
                            1237757
                        )
                    )
                )
            )
        );

        if ($this->amo_auth()) {

            $data = $this->amo_curl($link, 'POST', $leads);

            return $data['data']['_embedded']['items'];
        }

        return [];*/
    }
}
