<?php

class ApiProvider
{
    public static function post(ApiRequest $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }

    public static function send(ApiRequest $request, HttpMethod $method)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method->name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }
}

class ApiRequest
{
    public function __construct(String $url, array $header, $body)
    {
        $this->url = $url;
        $this->header = $header;
        $this->body = $body;
    }
}

enum HttpMethod
{
    case POST;
    case GET;
    case PUT;
    case DELETE;
}
