<?php

namespace StatIg\Storage;

const STORAGE_DIR = __DIR__ . '/../data';
	
function saveUserData(string $username, \stdClass $data): string
{
	if (!is_dir(STORAGE_DIR)) {
		mkdir(STORAGE_DIR, 0777, true);
	}
	
	$realFullFilePath = getFullFilePath($username);
	
	$encodedData = json_encode(
	$data,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	
	file_put_contents($realFullFilePath, $encodedData);
	return $realFullFilePath;
}

function getUserData(string $username): \stdClass
{
	$fullFilePath = getFullFilePath($username);
	if (!is_file($fullFilePath)) {
		return null;
	}
	
	$encodedData = file_get_contents($fullFilePath);
	
	return json_decode($encodedData);
	
}

function getFullFilePath(string $username): string
{
	$fullFilePath = STORAGE_DIR . "/$username.json";
	
	echo "\n".realpath ($fullFilePath). "\n";
	
	return $fullFilePath;
	
	return realpath($fullFilePath);
}