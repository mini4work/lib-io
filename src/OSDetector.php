<?php

namespace M4W\LibIO;

use M4W\LibIO\Enums\OS;
use M4W\LibIO\Interfaces\KeyboardInterface;
use M4W\LibIO\Interfaces\MouseInterface;
use M4W\LibIO\Keyboard\KeyboardMac;
use M4W\LibIO\Keyboard\KeyboardWindows;
use M4W\LibIO\Mouse\MouseMac;
use M4W\LibIO\Mouse\MouseWindows;

class OSDetector
{
    /**
     * @return OS
     */
    public static function detect(): OS
    {
        $os = PHP_OS_FAMILY;

        return match ($os) {
            'Windows' => OS::Windows,
            'Linux' => OS::Linux,
            'Darwin' => OS::Mac,
            default => OS::Unknown,
        };
    }

    public static function getMouseInstance(): MouseInterface
    {
        return match (self::detect()) {
            OS::Mac => new MouseMac(),
            OS::Windows => new MouseWindows(),
            default => null,
        };
    }

    public static function getKeyboardInstance(): KeyboardInterface
    {
        return match (self::detect()) {
            OS::Mac => new KeyboardMac(),
            OS::Windows => new KeyboardWindows(),
            default => null,
        };
    }
}