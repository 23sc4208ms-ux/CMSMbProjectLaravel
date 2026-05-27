<?php
// Test logins by fetching CSRF token and posting credentials as AJAX requests.
$host = $argv[1] ?? 'http://127.0.0.1:10000';
$accounts = [
    ['username' => 'admin@example.com', 'password' => 'AdminPass123!'],
    ['username' => 'teacher@example.com', 'password' => 'TeacherPass123!'],
    ['username' => 'student@example.com', 'password' => 'StudentPass123!'],
];

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

foreach ($accounts as $acct) {
    echo "\n--- Testing {$acct['username']} ---\n";
    $cookieJar = sys_get_temp_dir() . '/cookie_' . md5($acct['username'] . time());
    // GET /login
    list($resp, $info) = fetch($host . '/login', $cookieJar);
    // Extract CSRF token from HTML
    $token = null;
    if (preg_match('/name="_token" value="([^"]+)"/', $resp, $m)) {
        $token = $m[1];
    }
    if (!$token) {
        echo "Failed to get CSRF token for {$acct['username']}\n";
        continue;
    }

    // POST /login as AJAX
    $postFields = http_build_query([
        '_token' => $token,
        'username' => $acct['username'],
        'password' => $acct['password'],
    ]);
    $ch = curl_init($host . '/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Requested-With: XMLHttpRequest',
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Account: {$acct['username']} -> HTTP {$code}\n";
    $decoded = json_decode($res, true);
    if ($decoded) {
        echo "Response: " . json_encode($decoded) . "\n";
    } else {
        echo "Raw response: " . substr($res, 0, 500) . "\n";
    }

    unlink($cookieJar);
}

echo "Done testing logins.\n";
