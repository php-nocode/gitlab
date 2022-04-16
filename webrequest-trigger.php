<?php

/**
 * Webhook URL  
 * 
 *   https://webrequest.cc/php-nocode/gitlab/trigger/pipeline?_bypass_landing_page=yes&base_id={Airtable Base ID}&project_id={GitLab Project ID}
 *  
 * Secret Token
 *
 *   {GitLab Trigger Token}+{Airtable Api Key}
 *
 */

[$gitlabToken, $airtableKey] = explode('+', input('X-Gitlab-Token'));

$airtable = new Airtable([
  'api_key' => $airtableKey,
  'base'    => input('base_id'),
]);

$http = new \GuzzleHttp\Client();
         
$projectId = input('project_id');
$gitlabUrl = 'https://gitlab.com/api/v4/projects/'.$projectId.'/trigger/pipeline';
                                       
$response = $http->request('POST', $gitlabUrl, [
  'form_params' => [
    'token' => $gitlabToken,
    'ref' => 'main',
  ]
]);

print_r($response->getBody()."");

$response = $airtable->saveContent('Logs', [
  'date()'          => date('Y-m-d H:i:s'),
  '$_GET'           => json_encode($_GET),
  '$_POST'          => json_encode($_POST),
  'getallheaders()' => json_encode(getallheaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
  '$_SERVER'        => json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
  'php://input'     => file_get_contents('php://input'),
  '$response'       => $response->getBody(),
]);

var_dump($response);
