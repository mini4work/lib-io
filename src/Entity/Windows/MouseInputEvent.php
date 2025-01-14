<?php

namespace M4W\LibIO\Entity\Windows;

use FFI\CData;
use M4W\LibIO\Entity\MouseButtonState;
use M4W\LibIO\Entity\Vector2;
use M4W\LibIO\Entity\Windows\Interfaces\InputEventInterface;

class MouseInputEvent implements InputEventInterface
{
    public function __construct(
        public Vector2          $point,
        public MouseButtonState $buttonState,
        public MouseButtonState $currentState,
    ) {}

    public function mapToCData(CData &$cData, Vector2 $screenSize): void
    {
        $scaledX = (int)(($this->point->x / $screenSize->x) * 65535);
        $scaledY = (int)(($this->point->y / $screenSize->y) * 65535);

        $flags = $this->buttonState->getState($this->currentState);

        $cData->type = 0;
        $cData->mi->dx = $scaledX;
        $cData->mi->dy = $scaledY;
        $cData->mi->mouseData = 0;
        $cData->mi->dwFlags = $flags;
        $cData->mi->time = 0;
        $cData->mi->dwExtraInfo = 0;
    }
}