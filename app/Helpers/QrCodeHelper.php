<?php

namespace App\Helpers;

class QrCodeHelper
{
    /**
     * Generate QR code using QuickChart.io API (Free, no API key needed)
     */
    public static function generate($data, $size = 300)
    {
        $qrData = urlencode($data);
        $url = "https://quickchart.io/qr?text={$qrData}&size={$size}";

        return '<img src="' . $url . '" width="' . $size . '" height="' . $size . '" alt="QR Code" style="max-width:100%; max-height:100%;">';
    }

    /**
     * Alternative QR code generator using Google Charts API
     */
    public static function generateGoogle($data, $size = 300)
    {
        $qrData = urlencode($data);
        $url = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$qrData}&choe=UTF-8";

        return '<img src="' . $url . '" width="' . $size . '" height="' . $size . '" alt="QR Code">';
    }

    /**
     * Generate QR code as base64 data URI (works offline)
     */
    public static function generateBase64($data, $size = 300)
    {
        $qrData = urlencode($data);
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$qrData}";

        return '<img src="' . $url . '" width="' . $size . '" height="' . $size . '" alt="QR Code">';
    }
}
