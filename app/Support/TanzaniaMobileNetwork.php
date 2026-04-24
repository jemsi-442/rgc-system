<?php

namespace App\Support;

final class TanzaniaMobileNetwork
{
    /**
     * Prefix guidance is based on the TCRA National Numbering and Signaling Point
     * Codes Plans, July 2025. Tanzania also supports mobile number portability,
     * so prefix-based matching should be treated as a best-effort default only.
     */
    private const NETWORK_PREFIXES = [
        'halopesa' => ['061'],
        'mixx_by_yas' => ['065', '067', '071', '077'],
        'airtel_money' => ['068', '069', '078'],
        'mpesa' => ['074', '075', '076', '079'],
    ];

    public static function normalizePhone(mixed $value): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '255' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && in_array($phone[0], ['6', '7'], true)) {
            return '255' . $phone;
        }

        return $phone;
    }

    public static function inferNetwork(mixed $value): ?string
    {
        $phone = self::normalizePhone($value);

        if ($phone === null || ! str_starts_with($phone, '255') || strlen($phone) < 6) {
            return null;
        }

        $prefix = substr($phone, 3, 3);

        foreach (self::NETWORK_PREFIXES as $network => $prefixes) {
            if (in_array($prefix, $prefixes, true)) {
                return $network;
            }
        }

        return null;
    }
}
