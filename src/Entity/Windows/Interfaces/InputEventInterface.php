<?php

namespace M4W\LibIO\Entity\Windows\Interfaces;

use FFI\CData;
use M4W\LibIO\Entity\Vector2;

interface InputEventInterface
{
    public function mapToCData(CData &$cData, Vector2 $screenSize): void;
}