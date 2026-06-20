<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;

/** US-17: SVG QR codes for personal invitation URLs (no GD required — works on Vercel). */
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

        return (new SvgWriter)->write($qrCode);
    }

    public function toSvgMarkup(string $absoluteInviteUrl): string
    {
        return $this->make($absoluteInviteUrl)->getString();
    }

    public function toDataUri(string $absoluteInviteUrl): string
    {
        $result = $this->make($absoluteInviteUrl);

        return 'data:'.$result->getMimeType().';base64,'.base64_encode($result->getString());
    }
}
