<?php

namespace M4W\LibIO\Mouse;

use Exception;
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\MouseInterface;

class MouseWindows implements MouseInterface
{
    /**
     * @throws Exception
     */
    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void
    {
        // TODO: Implementation
    }

    public function move(int $x = 0, int $y = 0): void
    {
        // TODO: Implementation
    }

    public function getPosition(): array
    {
        // TODO: Implementation
        return [];
    }
}