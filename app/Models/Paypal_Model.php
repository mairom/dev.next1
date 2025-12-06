<?php
namespace App\Models;
use CodeIgniter\Model;

class Paypal_Model
{

    private $clientId = "AUlysqZ8Y9sx2cV5VOzpKaOXcf28rriWm8lWrz_sa2xpHvWRfPwnqGAWtBY6hvGJKOMGkwMOGwgLbTQS";
    private $secret = "EEcWFNkHwxJIc85tM13JKYotrS9o-4nHkqLPOdx726uLinbgApC2IEa2mzVjngZ28MlSMDLx_exu95rD";

    public function payment($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type:application/json",
                "Authorization: Bearer {$this->token}"
            )
        );
        $retorno_transaction = curl_exec($curl);
        curl_close($curl);
        return json_decode($retorno_transaction);
    }

    public function get_token()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->clientId . ":" . $this->secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($result);
        return $json->access_token;
    }


    public function executeApprovedPayment(array $payer)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/{$payer['paymentId']}/execute");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payer['payerId']));
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type:application/json",
                "Authorization: Bearer {$this->token}"
            )
        );
        $retorno_transaction = curl_exec($curl);
        curl_close($curl);
        return json_decode($retorno_transaction);
    }

    public function checkPayment($paymentId)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/{$paymentId}");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type:application/json",
                "Authorization: Bearer {$this->token}"
            )
        );
        $retorno_transaction = curl_exec($curl);
        curl_close($curl);
        return json_decode($retorno_transaction);
    }
}
