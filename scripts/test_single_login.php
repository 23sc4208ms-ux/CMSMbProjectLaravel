<?php
$host = $argv[1] ?? 'http://127.0.0.1:10000';
$username = $argv[2] ?? 'teacher@example.com';
$password = $argv[3] ?? 'TeacherPass123!';

function fetch($url, &$cookieJar, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$resp, $info];
}

echo "Testing {$username}\n";
$cookieJar = sys_get_temp_dir() . '/cookie_' . md5($username . time());
list($resp, $info) = fetch($host . '/login', $cookieJar);
if (preg_match('/name="_token" value="([^"]+)"/', $resp, $m)) $token = $m[1];
else { echo "No token found\n"; exit(1); }
echo "Token: {$token}\n";

$postFields = http_build_query(['_token'=>$token, 'username'=>$username, 'password'=>$password]);
$ch = curl_init($host . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP {$code}\n";
echo "Response: {$res}\n";

unlink($cookieJar);
