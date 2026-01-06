<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_POST["link"])) {
  die("❌ الرابط ما وصلش");
}

$link = $_POST["link"];

// المسارات
$input  = "uploads/input.mp4";
$output = "uploads/reel.mp4";
$overlay = "overlay_v2.png";

// مسح القديم
@unlink($input);
@unlink($output);

// 1️⃣ تحميل الفيديو
$cmd1 = "yt-dlp -f mp4 -o " . escapeshellarg($input) . " " . escapeshellarg($link) . " 2>&1";
exec($cmd1, $out1, $code1);

if ($code1 !== 0) {
  die("❌ فشل التحميل:<br>" . implode("<br>", $out1));
}

// 2️⃣ إضافة الـ overlay
$cmd2 = "ffmpeg -y -i $input -i $overlay -filter_complex \"[0:v][1:v] overlay=0:0\" -c:a copy $output 2>&1";
exec($cmd2, $out2, $code2);

if ($code2 !== 0) {
  die("❌ خطأ ffmpeg:<br>" . implode("<br>", $out2));
}

echo "OK";
