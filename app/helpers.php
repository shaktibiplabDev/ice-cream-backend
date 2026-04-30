<?php

if (!function_exists('number_to_words')) {
    function number_to_words($number) {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $num = (float) $number;
        $whole = floor($num);
        $decimal = round(($num - $whole) * 100);

        $result = '';

        if ($whole == 0) {
            $result = 'Zero';
        } else {
            $parts = [];
            $temp = $whole;

            // Handle crores
            if ($temp >= 10000000) {
                $parts[] = number_to_words_chunk(floor($temp / 10000000)) . ' Crore';
                $temp %= 10000000;
            }

            // Handle lakhs
            if ($temp >= 100000) {
                $parts[] = number_to_words_chunk(floor($temp / 100000)) . ' Lakh';
                $temp %= 100000;
            }

            // Handle thousands
            if ($temp >= 1000) {
                $parts[] = number_to_words_chunk(floor($temp / 1000)) . ' Thousand';
                $temp %= 1000;
            }

            // Handle remaining
            if ($temp > 0) {
                $parts[] = number_to_words_chunk($temp);
            }

            $result = implode(' ', $parts);
        }

        if ($decimal > 0) {
            $result .= ' and ' . number_to_words_chunk($decimal) . ' Paise';
        }

        return $result;
    }
}

if (!function_exists('number_to_words_chunk')) {
    function number_to_words_chunk($num) {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $result = '';

        if ($num >= 100) {
            $result .= $ones[floor($num / 100)] . ' Hundred';
            $num %= 100;
            if ($num > 0) $result .= ' and ';
        }

        if ($num >= 20) {
            $result .= $tens[floor($num / 10)];
            $num %= 10;
            if ($num > 0) $result .= ' ' . $ones[$num];
        } elseif ($num > 0) {
            $result .= $ones[$num];
        }

        return $result;
    }
}
