<?php

if (!function_exists('number_to_words')) {
    function number_to_words($number) {
        // Ensure non-negative and add bounds checking
        $num = max(0, (float) $number);
        $whole = (int) floor($num);
        $decimal = (int) round(($num - $whole) * 100);
        
        // Maximum limit: 99 crores (999,999,999)
        $maxLimit = 999999999;
        if ($whole > $maxLimit) {
            return 'Amount exceeds conversion limit';
        }
        
        if ($whole == 0 && $decimal == 0) {
            return 'Zero';
        }
        
        $parts = [];
        $temp = $whole;
        
        // Handle crores
        if ($temp >= 10000000) {
            $crorePart = (int) floor($temp / 10000000);
            $parts[] = number_to_words_chunk($crorePart) . ' Crore';
            $temp %= 10000000;
        }
        
        // Handle lakhs
        if ($temp >= 100000) {
            $lakhPart = (int) floor($temp / 100000);
            $parts[] = number_to_words_chunk($lakhPart) . ' Lakh';
            $temp %= 100000;
        }
        
        // Handle thousands
        if ($temp >= 1000) {
            $thousandPart = (int) floor($temp / 1000);
            $parts[] = number_to_words_chunk($thousandPart) . ' Thousand';
            $temp %= 1000;
        }
        
        // Handle remaining
        if ($temp > 0) {
            $parts[] = number_to_words_chunk($temp);
        }
        
        $result = implode(' ', array_filter($parts));
        
        if ($decimal > 0 && $decimal < 100) {
            $result .= ' and ' . number_to_words_chunk($decimal) . ' Paise';
        }
        
        return $result;
    }
}

if (!function_exists('number_to_words_chunk')) {
    function number_to_words_chunk($num) {
        $num = (int) $num;
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $result = '';

        if ($num >= 100) {
            $hundreds = (int) floor($num / 100);
            $result .= $ones[$hundreds] . ' Hundred';
            $num %= 100;
            if ($num > 0) $result .= ' and ';
        }

        if ($num >= 20) {
            $tenDigit = (int) floor($num / 10);
            $result .= $tens[$tenDigit];
            $num %= 10;
            if ($num > 0) $result .= ' ' . $ones[$num];
        } elseif ($num > 0) {
            $result .= $ones[$num];
        }

        return trim($result);
    }
}
