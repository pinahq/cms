<?php

namespace Pina\Modules\reCAPTCHA;

class Captcha
{

    public function verify()
    {
        $secretKey = \Pina\Modules\CMS\Config::get(__NAMESPACE__, 'secret_key');
        if (empty($secretKey)) {
            return true;
        }
        $response = \Pina\Request::input('g-recaptcha-response');

        $json = RecaptchaResponseGateway::instance()->whereBy('response', $response)->value('json');
        if (empty($json)) {
            RecaptchaResponseGateway::instance()->whereExpired()->delete();
            $json = $this->makeRequest($secretKey, $response);
            RecaptchaResponseGateway::instance()->put(['response' => $response, 'json' => $json]);
        }

        if (empty($json)) {
            return false;
        }

        $result = json_decode($json, true);

        if (isset($result['success']) && $result['success']) {
            return true;
        }

        return false;
    }

    private function makeRequest($secretKey, $response)
    {
        $req = implode('&', [
            'secret=' . urlencode(stripslashes($secretKey)),
            'response=' . urlencode(stripslashes($response)),
            'remoteip=' . urlencode(stripslashes(filter_input(INPUT_SERVER, "REMOTE_ADDR"))),
        ]);

        $out = '';

        try {
            $curl = curl_init();
            if ($curl) {
                curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $req);

                $out = curl_exec($curl);

                curl_close($curl);
            }
        } catch (\Exception $e) {
            return false;
        }

        return $out;
    }

}
