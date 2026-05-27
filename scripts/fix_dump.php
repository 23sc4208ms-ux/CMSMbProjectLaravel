<?php
if ($argc < 3) {
    echo "Usage: php fix_dump.php input.sql output.sql\n";
    exit(1);
}
$in = $argv[1];
$out = $argv[2];
if (!is_file($in)) { echo "Input not found: $in\n"; exit(1); }
$data = file_get_contents($in);
// Try detect UTF-16LE BOM
if (substr($data,0,2) === "\xFF\xFE" || substr($data,0,2) === "\xFE\xFF" || preg_match('/\x00[\x00-\x7F]/', substr($data,0,100))) {
    // Convert from UTF-16 (LE or BE) to UTF-8
    $utf8 = mb_convert_encoding($data, 'UTF-8', 'UTF-16LE');
} else {
    $utf8 = $data;
}
// Remove stray first-line if it contains Get-Clipboard
$lines = preg_split('/\r\n|\n|\r/', $utf8);
if (isset($lines[0]) && stripos($lines[0], 'Get-Clipboard') !== false) {
    array_shift($lines);
}
$clean = implode("\n", $lines);
file_put_contents($out, $clean);
echo "Wrote cleaned file: $out\n";
