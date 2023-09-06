<?php
$html = file_get_contents($_SERVER['HTTP_ORIGIN']);
preg_match_all('/href="(https:\/\/legacyclient\.atlanticexpresscorp\.com\/order\/cache-full-image[^"]*)"/', $html, $matches);
$links = [];
foreach ($matches[1] as $match) {
    array_push($links, $match);
}

$error = "";
if (!empty($links)) {
    if (extension_loaded('zip')) {
        $zip = new ZipArchive(); 
        $zip_name = "photos.zip";
        if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {

            $error .= "Ошибка создания ZIP-архива";
        }
        $i = 1;
        foreach ($links as $link) {
            $linkContent = file_get_contents($link);
            $zip->addFromString("image{$i}.jpg", $linkContent); 
            ++$i;
        }
        $zip->close();
        if (file_exists($zip_name)) {
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_name . '"');
            readfile($zip_name);
            unlink($zip_name);
        }
    } else
        $error .= "Отсутствует ZIP-расширение";
}
