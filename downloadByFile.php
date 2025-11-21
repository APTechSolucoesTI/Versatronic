<?php
$file = $_GET['file'] ?? null;

if (!$file || !file_exists($file)) {
    http_response_code(404);
    exit('Arquivo não encontrado.');
}

$basename = basename($file);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"$basename\"");
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
