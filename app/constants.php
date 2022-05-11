<?php

enum NetworkProvider
{
    case VODAFONE;
    case MTN;
    case TIGO;

    public static function fromString(string $network)
    {
        $network = strtoupper($network);
        return match (true) {
            $network == 'VODAFONE' => static::VODAFONE,
            $network == 'MTN' => static::MTN,
            $network == 'TIGO' => static::TIGO
        };
    }
}
