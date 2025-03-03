<?php

namespace M4W\LibIO;

use Exception;
use M4W\LibIO\Enums\OS;
use M4W\LibIO\Interfaces\IOInterface;
use M4W\LibIO\IO\IOMac;
use M4W\LibIO\IO\IOWindows;

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
            'Linux' => self::detectLinuxGraphicsEngine(),
            'Darwin' => OS::Mac,
            default => OS::Unknown,
        };
    }

    /**
     * Detect the graphics engine on Linux.
     *
     * @return OS The detected Linux graphics engine (LinuxX11 or LinuxWayLand).
     */
    private static function detectLinuxGraphicsEngine(): OS
    {
        if (getenv('WAYLAND_DISPLAY')) {
            return OS::LinuxWayLand;
        }

        return OS::LinuxX11;
    }

    /**
     * @throws Exception
     */
    public static function getIOInstance(): IOInterface
    {
        return match (self::detect()) {
            OS::Mac => new IOMac(),
            OS::Windows => new IOWindows(),
            default => throw new Exception("No realization for your system"),
        };
    }
}