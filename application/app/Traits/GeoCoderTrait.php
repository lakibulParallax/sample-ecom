<?php

namespace App\Traits;


use Illuminate\Support\Facades\Log;

trait GeoCoderTrait
{

    public $geolocation = "";
    public $address = "";
    public $file_contents = "";


    public function getLocationByLatLong($latitude, $longitude)
    {
        $this->geolocation = "$latitude,$longitude";
        $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $this->geolocation . '&key=' . env('GOOGLE_MAP_API_KEY');
        return $this->parseLocation($request);
    }


    public function getLocationByAddress($address)
    {
        $this->address = "$address";
        $request = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $this->address . $this->api_key;
        return $this->parseLocation($request);
    }


    public function getLatLongByAddress($address)
    {
        $this->address = "$address";
        $request = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(" ", "", $this->address) . $this->api_key;
        return $this->getLatLong($request);
    }

    public function getLatLong($request)
    {
        $this->file_contents = file_get_contents($request);
        $json_decode = json_decode($this->file_contents);
        $result['lat'] = @$json_decode->results[0]->geometry->location->lat;
        $result['lng'] = @$json_decode->results[0]->geometry->location->lng;
        return $result;
    }

    public function getTimeZone($lat, $lng)
    {
        $request = 'https://maps.googleapis.com/maps/api/timezone/json?location=' . $lat . ',' . $lng . '&timestamp=' . time() . $this->api_key;
        return $this->getTimeZoneId($request);
    }

    public function getTimeZoneId($request)
    {
        $this->file_contents = file_get_contents($request);
        $json_decode = json_decode($this->file_contents);
        return $json_decode->timeZoneId;
    }

    public function parseLocation($request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: maps.googleapis.com",
                "accept-encoding: gzip, deflate",
                "cache-control: no-cache"
            ),
        ));

        $this->file_contents = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        }




        $jsondata = json_decode($this->file_contents,true);

//        Log::info("geo code response: ", ["response"=>$jsondata]);

        try {

            $address = array(
                'country' => $this->google_getCountry($jsondata),
                'province' => $this->google_getProvince($jsondata),
                'city' => $this->google_getCity($jsondata),
                'state' => $this->google_getState($jsondata),
                'street' => $this->google_getStreet($jsondata),
                'postal_code' => $this->google_getPostalCode($jsondata),
                'country_code' => $this->google_getCountryCode($jsondata),
                'formatted_address' => $this->google_getAddress($jsondata),
            );
        }catch (\Exception $exception){
            $address = array(
                'country' => "unknown",
                'province' => "",
                'city' => "unknown",
                'street' => "",
                'postal_code' => "",
                'country_code' => "",
                'formatted_address' => "",
            );
        }
        return $address;




        $json_decode = json_decode($this->file_contents);

        if (isset($json_decode->results[0])) {
            $response = array();
            foreach ($json_decode->results[0]->address_components as $addressComponet) {
                if (in_array('political', $addressComponet->types)) {
                    $response[] = $addressComponet->long_name;
                }
            }

            if (isset($response[0])) {
                $first = $response[0];
            } else {
                $first = 'null';
            }
            if (isset($response[1])) {
                $second = $response[1];
            } else {
                $second = 'null';
            }
            if (isset($response[2])) {
                $third = $response[2];
            } else {
                $third = 'null';
            }
            if (isset($response[3])) {
                $fourth = $response[3];
            } else {
                $fourth = 'null';
            }
            if (isset($response[4])) {
                $fifth = $response[4];
            } else {
                $fifth = 'null';
            }


            $loc['formatted_address'] = $json_decode->results[0]->formatted_address;
            $loc['address'] = '';
            $loc['city'] = '';
            $loc['state'] = '';
            $loc['country'] = '';
            if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null') {
                $loc['address'] = $first;
                $loc['city'] = $second;
                $loc['state'] = $fourth;
                $loc['country'] = $fifth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null') {
                $loc['address'] = $first;
                $loc['city'] = $second;
                $loc['state'] = $third;
                $loc['country'] = $fourth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null') {
                $loc['city'] = $first;
                $loc['state'] = $second;
                $loc['country'] = $third;
            } else if ($first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                $loc['state'] = $first;
                $loc['country'] = $second;
            } else if ($first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                $loc['country'] = $first;
            }
        } else {
            $loc['city'] = "";
            $loc['country'] = "";
        }

        $loc['formatted_address'] = implode(', ', @$loc);
        return @$loc;
    }


    public function check_status($jsondata) {
        if ($jsondata["status"] == "OK") return true;
        return false;
    }

    public function google_getCountry($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("country", $jsondata["results"][0]["address_components"]);
        return $return_data;
    }
    public function google_getProvince($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("administrative_area_level_1", $jsondata["results"][0]["address_components"], true);
        return $return_data;
    }
    public function google_getCity($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("locality", $jsondata["results"][0]["address_components"]);
        return $return_data;
    }
    public function google_getState($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("administrative_area_level_1", $jsondata["results"][0]["address_components"]);
        return $return_data;
    }
    public function google_getStreet($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("street_number", $jsondata["results"][0]["address_components"]) . ' ' . $this->Find_Long_Name_Given_Type("route", $jsondata["results"][0]["address_components"]);
        return $return_data;
    }
    public function google_getPostalCode($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("postal_code", $jsondata["results"][0]["address_components"]);
        return $return_data;
    }
    public function google_getCountryCode($jsondata) {
        $return_data = $this->Find_Long_Name_Given_Type("country", $jsondata["results"][0]["address_components"], true);
        return $return_data;
    }
    public function google_getAddress($jsondata) {
        return $jsondata["results"][0]["formatted_address"];
    }

    public function Find_Long_Name_Given_Type($type, $array, $short_name = false) {
        foreach( $array as $value) {
            if (in_array($type, $value["types"])) {
                if ($short_name)
                    return $value["short_name"];
                return $value["long_name"];
            }
        }
    }

}
