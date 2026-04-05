<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;

/** US-17: PNG QR codes for personal invitation URLs. */
final class WeddingInviteQrGenerator
{
    public function __construct(
        private int $size = 320,
    ) {}

    public function make(string $absoluteInviteUrl): ResultInterface
    {
        $qrCode = new QrCode(
            data: $absoluteInviteUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $this->size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new PngWriter)->write($qrCode);
    }
}
