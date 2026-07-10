<?php
$url = "https://api.github.com/repos/dompdf/dompdf/releases/latest";
$options = [
    'http' => [
        'header' => "User-Agent: PHP-Script\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$data = json_decode($response, true);

$download_url = '';
foreach ($data['assets'] as $asset) {
    if (strpos($asset['name'], 'dompdf') !== false && strpos($asset['name'], '.zip') !== false) {
        $download_url = $asset['browser_download_url'];
        break;
    }
}

if ($download_url) {
    echo "Downloading from $download_url...\n";
    file_put_contents('dompdf.zip', file_get_contents($download_url, false, $context));
    echo "Extracting...\n";
    $zip = new ZipArchive;
    if ($zip->open('dompdf.zip') === TRUE) {
        $zip->extractTo(__DIR__ . '/includes/');
        $zip->close();
        echo "Extracted successfully.\n";
        unlink('dompdf.zip');
    } else {
        echo "Failed to extract.\n";
    }
} else {
    echo "Could not find zip release.\n";
}
