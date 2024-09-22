<?php
class KeyController{

    public static function getPublicKey()
    {
        $data['type'] = "card";
        $curl = curl_init('https://sandbox.api.pagseguro.com/public-keys/');
        curl_setopt($curl,CURLOPT_HTTPHEADER,Array(
            'Content-Type: application/json',
            'Authorization: 279c9a74-9859-45cf-a74b-c336795236940fcf1fe54a438803fd2c2d6d9e80b69b3122-2b98-4396-a7b9-23aed7fc468c'
        ));
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $retorno = curl_exec($curl);
        curl_close($curl);
        return json_decode($retorno)->public_key;
    }
}