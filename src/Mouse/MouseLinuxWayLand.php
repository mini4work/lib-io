<?php

namespace M4W\LibIO\Mouse;

use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\MouseInterface;
use RuntimeException;

class MouseLinuxWayLand implements MouseInterface
{
    public function __construct()
    {
        // Initialization for Wayland (requires proper Wayland bindings)
        if (!getenv('WAYLAND_DISPLAY')) {
            throw new RuntimeException("Wayland display not detected.");
        }
    }

    public function move(int $x = 0, int $y = 0): void
    {
        throw new RuntimeException("Mouse move not implemented for Wayland.");
    }

    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void
    {
        throw new RuntimeException("Mouse click not implemented for Wayland.");
    }

    public function getPosition(): array
    {
        throw new RuntimeException("Get mouse position not implemented for Wayland.");
    }
}
