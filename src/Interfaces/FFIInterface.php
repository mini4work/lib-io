<?php

namespace M4W\LibIO\Interfaces;

/**
 * Interface for FFI methods
 *
 * @method void CGDisplayMoveCursorToPoint(int $display, object $point)
 * @method void CGWarpMouseCursorPosition(object $point)
 * @method object CGEventCreateMouseEvent(?object $source, int $mouseType, object $mouseCursorPosition, int $mouseButton)
 * @method object CGEventCreateKeyboardEvent(?object $source, int $virtualKey, int $keyDown)
 * @method void CGEventPost(int $tap, object $event)
 * @method int CGEventSourceKeyState(?object $source, int $keyCode)
 * @method int CGGetActiveDisplayList(int $maxDisplays, object $activeDisplays, object $displayCount)
 * @method void CGDisplayBounds(int $display, object $bounds)
 */
interface FFIInterface
{
}
