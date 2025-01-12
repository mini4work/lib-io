<?php

namespace M4W\LibIO\Interfaces;

use M4W\LibIO\Enums\MouseButton;

interface MouseInterface
{
    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void;

    public function move(int $x = 0, int $y = 0): void;

    public function getPosition(): array;
}