<?php

namespace M4W\LibIO\Keyboard;

use M4W\LibIO\Interfaces\KeyboardInterface;
use M4W\LibIO\Enums\KeyCode;
use RuntimeException;

class KeyboardLinuxWayLand implements KeyboardInterface
{
    public function __construct()
    {
        // Initialization for Wayland (requires proper Wayland bindings)
        if (!getenv('WAYLAND_DISPLAY')) {
            throw new RuntimeException("Wayland display not detected.");
        }
    }

    public function down(KeyCode $key): void
    {
        throw new RuntimeException("Key down not implemented for Wayland.");
    }

    public function up(KeyCode $key): void
    {
        throw new RuntimeException("Key up not implemented for Wayland.");
    }

    public function press(KeyCode $key): void
    {
        $this->down($key);
        //usleep(50000); // 50 ms delay to simulate key press
        //$this->up($key);
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        throw new RuntimeException("Method isKeyPressed not implemented for Wayland.");
    }
}
