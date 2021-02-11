<?php

namespace StatIg\Load;

use function StatIg\Storage\saveUserData;
	
function loadRemoteUserData(string $username, ?string $sessionId): string
{
	$httpClient = new \GuzzleHttp\Client();
	$fullUrl = "https://www.instagram.com/$username/?__a=1";
	
	$options = [];
	
	if ($sessionId !== null) {
	
	$options = [
		'headers' => [
			'cookie' => "sessionid=$sessionId",
		]
	];
	
	/*
	$jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
	
		[
			'sessionid' => $sessionId,
		],
		'instagram.com'
	
	);
	
	$options = [

			'cookies' => $jar,

	];
	*/
	}
	
	
	$response = $httpClient->get($fullUrl, $options);
	/*
	$response = $httpClient->request(
		'get',
		$fullUrl,
		$options);
	*/
	$contents = $response->getBody()->getContents();
	$data = json_decode($contents);
	if (!is_object($data)) {
		throw new \Exception("User data is not object:" . $contents);
	}
	
	return saveUserData($username, $data);
}

