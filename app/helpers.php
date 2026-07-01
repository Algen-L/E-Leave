<?php

if (! function_exists('storage_url')) {
    /**
     * Generate URL for storage files (profile pics, e-signatures).
     * Uses /media/ route to avoid conflict with physical storage directory.
     */
    function storage_url(?string $path): string
    {
        if (empty($path) || ! is_string($path)) {
            return '';
        }

        $path = preg_replace('#^storage/#', '', trim($path));

        return $path ? url('media/'.$path) : '';
    }
}

if (! function_exists('format_credit_3_decimal')) {
    /**
     * Format a leave credit number to exactly 3 decimal places without rounding.
     */
    function format_credit_3_decimal($number): string
    {
        if ($number === '' || $number === null) {
            return '0.000';
        }
        $floatVal = (float)$number;
        $str = sprintf('%.10f', $floatVal);
        
        $dotPos = strpos($str, '.');
        if ($dotPos === false) {
            return number_format($floatVal, 3, '.', ',');
        }
        
        $intPart = substr($str, 0, $dotPos);
        $decPart = substr($str, $dotPos + 1, 3);
        
        $formattedInt = number_format((float)$intPart, 0, '.', ',');
        
        if ($floatVal < 0 && $formattedInt[0] !== '-') {
            $formattedInt = '-' . $formattedInt;
        }
        
        return $formattedInt . '.' . $decPart;
    }
}

if (! function_exists('truncate_credit_for_input')) {
    /**
     * Format credits for numeric input values (no commas, exactly 3 decimal places without rounding).
     */
    function truncate_credit_for_input($number): string
    {
        if ($number === '' || $number === null) {
            return '';
        }
        $floatVal = (float)$number;
        $str = sprintf('%.10f', $floatVal);
        $dotPos = strpos($str, '.');
        if ($dotPos === false) {
            return sprintf('%.3f', $floatVal);
        }
        $intPart = substr($str, 0, $dotPos);
        $decPart = substr($str, $dotPos + 1, 3);
        return $intPart . '.' . $decPart;
    }
}

