<?php

namespace M4W\LibIO\Enums;

enum OS
{
    case LinuxWayLand;
    case LinuxX11;
    case Windows;
    case Mac;
    case Unknown;

    public function getName(): string
    {
        return match ($this) {
            self::Windows => 'Windows',
            self::LinuxX11 => 'Linux (X11)',
            self::LinuxWayLand => 'Linux (Wayland)',
            self::Mac => 'Mac',
            self::Unknown => 'Unknown',
        };
    }
}
