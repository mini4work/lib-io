<?php

namespace M4W\LibIO\Interfaces;

use FFI;

/**
 * Interface for FFI methods
 *
 * Methods for macOS:
 * -----------------
 * Library: ApplicationServices.framework
 * @method void CGDisplayMoveCursorToPoint(int $display, object $point)
 * @method void CGWarpMouseCursorPosition(object $point)
 * @method object CGEventCreateMouseEvent(?object $source, int $mouseType, object $mouseCursorPosition, int $mouseButton)
 * @method object CGEventCreateKeyboardEvent(?object $source, int $virtualKey, int $keyDown)
 * @method void CGEventPost(int $tap, object $event)
 * @method int CGEventSourceKeyState(?object $source, int $keyCode)
 * @method int CGGetActiveDisplayList(int $maxDisplays, object $activeDisplays, object $displayCount)
 * @method void CGDisplayBounds(int $display, object $bounds)
 *
 * Methods for Windows:
 * -------------------
 * Library: user32.dll
 * @method int SetCursorPos(int $x, int $y)
 * @method int GetCursorPos(object $lpPoint)
 * @method int SendInput(int $nInputs, object $pInputs, int $cbSize)
 * @method int GetAsyncKeyState(int $vKey)
 * @method int GetSystemMetrics(int $nIndex)
 *
 * Methods for Linux:
 * -----------------
 * Library: X11 (through native bindings)
 * @method int XWarpPointer(object $display, int $srcWindow, int $destWindow, int $srcX, int $srcY, int $srcWidth, int $srcHeight, int $destX, int $destY)
 * @method int XQueryPointer(object $display, int $window, object $rootReturn, object $childReturn, object $rootX, object $rootY, object $winX, object $winY, object $mask)
 * @method int XFlush(object $display)
 * @method object DefaultRootWindow(object $display)
 * @method object XKeysymToKeycode(object $display, int $keysym)
 * @method void XTestFakeKeyEvent(object $display, int $keycode, int $isPress, int $delay)
 * @method object XOpenDisplay(?string $displayName)
 * @method void XCloseDisplay(object $display)
 * @method int XSendEvent(object $display, int $w, bool $propagate, int $event_mask, object $event_send) Sends an event to the specified window, returns 1 on success, 0 on failure.
 *
 * @mixin FFI
 */
interface FFIInterface
{
}
