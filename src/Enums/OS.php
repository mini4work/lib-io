<?php

namespace M4W\LibIO\Enums;

enum OS
{
    case Linux;
    case Windows;
    case Mac;
    case Unknown;

    public function getName(): string
    {
        return match ($this) {
            self::Windows => 'Windows',
            self::Linux => 'Linux',
            self::Mac => 'Mac',
            self::Unknown => 'Unknown',
        };
    }
}
