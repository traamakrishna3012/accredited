<?php
$logo = imagecreatetruecolor(200, 50);
$bg = imagecolorallocate($logo, 240, 240, 240);
$text_color = imagecolorallocate($logo, 0, 0, 0);
imagefill($logo, 0, 0, $bg);
imagestring($logo, 5, 20, 15, 'COMPANY LOGO', $text_color);
imagepng($logo, 'c:/xampp/htdocs/accredited/uploads/settings/company_logo.png');

$seal = imagecreatetruecolor(150, 50);
$bg2 = imagecolorallocate($seal, 255, 255, 255);
$text_color2 = imagecolorallocate($seal, 0, 0, 255);
imagefill($seal, 0, 0, $bg2);
imagestring($seal, 5, 10, 15, 'DIGITAL SEAL & SIGN', $text_color2);
imagepng($seal, 'c:/xampp/htdocs/accredited/uploads/settings/digital_seal.png');

echo "Images created.";
