<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
function linkDouyin($douyinUrl) {
    $apiUrl = "https://api-douyin-downloader-without-watermark-download-douyin-videos.p.rapidapi.com/";
    $apiKey = "ae46f55197msh682efba35a269a1p163941jsnb4f606e1a89d";

    $postFields = "URL=" . urlencode($douyinUrl);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
            "x-rapidapi-host: api-douyin-downloader-without-watermark-download-douyin-videos.p.rapidapi.com",
            "x-rapidapi-key: $apiKey"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);
    return $data ?? "❌ Không tìm thấy video!";
}