<?php

namespace App\Services;

class BankValidator
{
    /**
     * Validate an IBAN against length and checksum rules.
     */
    public static function isValidIban(string $iban): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));
        
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban)) {
            return false;
        }
        
        $countryCode = substr($iban, 0, 2);
        $checkDigits = substr($iban, 2, 2);
        $bban = substr($iban, 4);
        
        $rearranged = $bban . $countryCode . $checkDigits;
        $numeric = '';
        
        foreach (str_split($rearranged) as $char) {
            if (ctype_alpha($char)) {
                $numeric .= ord($char) - 55;
            } else {
                $numeric .= $char;
            }
        }
        
        return bcmod($numeric, '97') === '1';
    }

    /**
     * Validate a SWIFT/BIC code format.
     */
    public static function isValidSwiftBic(string $swift): bool
    {
        $swift = strtoupper(trim($swift));
        return preg_match('/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/', $swift);
    }

    /**
     * Validate a US Routing Number via checksum.
     */
    public static function isValidRoutingNumber(string $routing): bool
    {
        $routing = trim($routing);
        if (!preg_match('/^[0-9]{9}$/', $routing)) {
            return false;
        }

        $weights = [3, 7, 1, 3, 7, 1, 3, 7, 1];
        $sum = 0;
        
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$routing[$i] * $weights[$i];
        }

        return ($sum % 10) === 0;
    }
}
