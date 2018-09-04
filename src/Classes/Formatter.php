<?php

namespace App;

class Formatter
{
    /**
     * Format a name
     *
     * @param string $name
     * @return string
     */
    public static function name($name)
    {
        // Convert to proper case formatting (e.g. john doe => John Doe)
        $name = strtolower($name);

        $name = implode("'", array_map('ucwords', explode("'", $name)));
        $name = implode('-', array_map('ucwords', explode('-', $name)));
        $name = implode('Mac', array_map('ucwords', explode('Mac', $name)));
        $name = implode('Mc', array_map('ucwords', explode('Mc', $name)));

        return $name;
    }

    /**
     * Format a phone number
     *
     * @param string $phone
     * @return string
     */
    public static function phone($phone)
    {
        // Remove space and ()
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace('-', '', $phone);

        return $phone;
    }
}