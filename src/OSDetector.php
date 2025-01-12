<?php

namespace M4W\LibIO;

use Exception;
use M4W\LibIO\Enums\OS;
use M4W\LibIO\Interfaces\KeyboardInterface;
use M4W\LibIO\Interfaces\MouseInterface;
use M4W\LibIO\Keyboard\KeyboardLinuxWayLand;
use M4W\LibIO\Keyboard\KeyboardLinuxX11;
use M4W\LibIO\Keyboard\KeyboardMac;
use M4W\LibIO\Keyboard\KeyboardWindows;
use M4W\LibIO\Mouse\MouseLinuxWayLand;
use M4W\LibIO\Mouse\MouseLinuxX11;
use M4W\LibIO\Mouse\MouseMac;
use M4W\LibIO\Mouse\MouseWindows;

class OSDetector
{
    /**
     * @return OS
     * @throws Exception
     */
    public static function detect(): OS
    {
        $os = PHP_OS_FAMILY;

        return match ($os) {
            'Windows' => OS::Windows,
            'Linux' => self::detectLinuxGraphicsEngine(),
            'Darwin' => OS::Mac,
            default => OS::Unknown,
        };
    }

    /**
     * Detect the graphics engine on Linux.
     *
     * @return OS The detected Linux graphics engine (LinuxX11 or LinuxWayLand).
     * @throws Exception
     */
    private static function detectLinuxGraphicsEngine(): OS
    {
        if (getenv('WAYLAND_DISPLAY')) {
            return OS::LinuxWayLand;
        } elseif (getenv('DISPLAY')) {
            return OS::LinuxX11;
        } else {
            throw new Exception("No graphical session detected on Linux");
        }
    }

    /**
     * @throws Exception
     */
    public static function getMouseInstance(): MouseInterface
    {
        return match (self::detect()) {
            OS::Mac => new MouseMac(),
            OS::Windows => new MouseWindows(),
            OS::LinuxX11 => new MouseLinuxX11(),
            OS::LinuxWayLand => new MouseLinuxWayLand(),
            default => throw new Exception("No realization for your system"),
        };
    }

    /**
     * @throws Exception
     */
    public static function getKeyboardInstance(): KeyboardInterface
    {
        return match (self::detect()) {
            OS::Mac => new KeyboardMac(),
            OS::Windows => new KeyboardWindows(),
            OS::LinuxX11 => new KeyboardLinuxX11(),
            OS::LinuxWayLand => new KeyboardLinuxWayLand(),
            default => throw new Exception("No realization for your system"),
        };
    }
}