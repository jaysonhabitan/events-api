<?php

namespace App\Tools;

class EndProcess
{
    public static function success(array $data = [])
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public static function failed(array $data = [])
    {
        return [
            'success' => false,
            'data' => $data
        ];
    }
}
