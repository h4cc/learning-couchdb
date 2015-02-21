<?php

$options = array(
  'DO_WAIT' => true,
);
foreach($argv as $value) {
  switch(strtolower($value)) {
      case '--no-wait':
        $options['DO_WAIT'] = false;
  }
}
foreach($options as $name => $value) {
  define($name, $value);
}


$baseUrl = 'http://localhost:5984';

function waitForInput() {
    if(DO_WAIT) {
      fgets(fopen("php://stdin","r"));
    }
}

function printJson($data) {
  echo "\n", json_encode($data, JSON_PRETTY_PRINT), "\n\n";
}

function waitReq($method, $url, $content=null) {
  return req($method, $url, $content, true);
}

function silentReq($method, $url, $content=null) {
  return req($method, $url, $content, true, true);
}

function req($method, $url, $content=null, $wait=false, $silent=false) {
  global $baseUrl;
  $url = $baseUrl . $url;
  
  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );
   
  $curl = curl_init();
   
  switch($method) {
      case 'GET':
          break;
      case 'POST':
          if(!is_null($content)) {
              $content = json_encode($content);
          }
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
          break;
      case 'PUT':
          if(!is_null($content)) {
              $content = json_encode($content);
          }
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
          curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
          break;
      case 'DELETE':
          if(!is_null($content)) {
              $content = json_encode($content);
          }
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
          curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
          break;
  }
   
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
   
   if(!$silent) {
     echo "\n", $method, ' ', $url;
     if($content) {
       echo "\n", json_encode(json_decode($content, true), JSON_PRETTY_PRINT);
     }
     if($wait) {
       waitForInput();
     }
   }

   
  $response = curl_exec($curl);
  $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $info = curl_getinfo($curl);
  curl_close($curl);
   
   $data = json_decode($response, true);
   
   if(!$silent) {
     echo "\n", $info['http_code'];
     printJson($data);
   }
   
   return $data;
}

