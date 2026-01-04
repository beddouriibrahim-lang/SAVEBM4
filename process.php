<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ======================
   CHECK LINK
====================== */
if (!isset($_POST['link']) || empty($_POST['link'])) {
    die("NO LINK");
}

$link = escapeshellarg($_POST['link']);

/* ======================
   PATHS
====================== */
$baseDir   = __DIR__;
$ytdlp     = $baseDir . "/yt-dlp.exe";
$ffmpeg    = $baseDir . "/ffmpeg.exe";
$overlay   = $baseDir . "/overlay_v2.png"; // ✅ الاسم الجديد
$uploadDir = $baseDir . "/uploads/";

if (!file_exists($ytdlp))   die("NO yt-dlp.exe");
if (!file_exists($ffmpeg))  die("NO ffmpeg.exe");
if (!file_exists($overlay)) die("NO overlay_v2.png");

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$inputVideo  = $uploadDir . "input.mp4";
$outputVideo = $uploadDir . "reel.mp4";

/* ======================
   CLEAN OLD FILES
====================== */
@unlink($inputVideo);
@unlink($outputVideo);

/* ======================
   1) DOWNLOAD VIDEO
====================== */
$cmdDownload = "\"$ytdlp\" -f mp4 -o \"$inputVideo\" $link";
exec($cmdDownload . " 2>&1", $out1, $ret1);

if ($ret1 !== 0 || !file_exists($inputVideo)) {
    echo "<pre>DOWNLOAD ERROR\n" . implode("\n", $out1) . "</pre>";
    exit;
}

/* ======================
   2) VIDEO INSIDE 9:16 + PNG FRAME
   (نفس المنطق اللي كان خدام)
====================== */
$cmdFfmpeg = "\"$ffmpeg\" -y -i \"$inputVideo\" -i \"$overlay\" " .
"-filter_complex \" " .
"[0:v]scale='if(gt(a,9/16),1080,-1)':'if(gt(a,9/16),-1,1920)'," .
"pad=1080:1920:(ow-iw)/2:(oh-ih)/2:black[vid]; " .
"[vid][1:v]overlay=0:0 " .
"\" " .
"-map 0:a? -c:v libx264 -preset veryfast -crf 23 -pix_fmt yuv420p " .
"-c:a copy \"$outputVideo\"";

exec($cmdFfmpeg . " 2>&1", $out2, $ret2);

/* ======================
   RESULT
====================== */
if ($ret2 === 0 && file_exists($outputVideo)) {
    echo "OK";
} else {
    echo "<pre>FFMPEG ERROR\n" . implode("\n", $out2) . "</pre>";
}
