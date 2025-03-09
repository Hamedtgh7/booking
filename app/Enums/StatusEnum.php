<?php

namespace app\Enums;

enum StatusEnum:string
{
    case PENDING='pending';
    case CONFIRMED='confirmed';
    case CANCELED='canceled';

    public static function values():array
    {
        return array_column(self::cases(),'value');    
    }
}