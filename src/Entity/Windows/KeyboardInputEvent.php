<?php

namespace M4W\LibIO\Entity\Windows;

use FFI\CData;
use M4W\LibIO\Entity\Vector2;
use M4W\LibIO\Entity\Windows\Interfaces\InputEventInterface;
use M4W\LibIO\Enums\KeyCode;

class KeyboardInputEvent implements InputEventInterface
{
    public function __construct(
        public KeyCode $keyCode,
        public bool $isPressed
    ) {}

    public function mapToCData(CData &$cData, Vector2 $screenSize): void
    {
        $cData->type = 1; // INPUT_KEYBOARD
        $cData->ki->wVk = $this->keyCode->getCode();
        $cData->ki->wScan = 0;
        $cData->ki->dwFlags = $this->isPressed ? 0x0000 : 0x0002;
        $cData->ki->time = 0;
        $cData->ki->dwExtraInfo = 0;
    }
}