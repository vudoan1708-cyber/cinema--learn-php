<?php

namespace cinema\utilities;

class Fetch {
  public static function get(string $url) {
    if (!$url) {
      return [];
    }

    $curl_handle = curl_init();

    // Set the curl URL option
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    
    // This option will return data as a string instead of direct output
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    
    // Execute curl & store data in a variable
    $response = curl_exec($curl_handle);
    
    curl_close($curl_handle);

    // Decode JSON into PHP array and return it
    return json_decode($response, true);
  }
}
