<?php

namespace M4W\LibIO\IO;

use Exception;
use FFI;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\IOInterface;

class IOMac implements IOInterface
{
    private FFI|FFIInterface $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef(
            file_get_contents(realpath('src/') . '/CHeaders/CoreGraphicsAPI.h'),
            "/System/Library/Frameworks/ApplicationServices.framework/ApplicationServices"
        );
    }

    /**
     * @throws Exception
     */
    public function press(KeyCode $key): void
    {
        $this->down($key);
        usleep(10000); // 10ms
        $this->up($key);
    }

    /**
     * @throws Exception
     */
    public function down(KeyCode $key): void
    {
        $this->sendKeyEvent($key, true);
    }

    /**
     * @throws Exception
     */
    public function up(KeyCode $key): void
    {
        $this->sendKeyEvent($key, false);
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        $state = $this->ffi->CGEventSourceKeyState(null, $key->getCode());
        return $state === 1;
    }

    /**
     * @throws Exception
     */
    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void
    {
        if (is_null($x) || is_null($y)) {
            $coords = $this->getPosition();
            $x = $coords['x'];
            $y = $coords['y'];
        }

        $kCGEventMouseDown = match ($button) {
            MouseButton::Left => 1, // kCGEventLeftMouseDown
            MouseButton::Right => 3, // kCGEventRightMouseDown
            MouseButton::Middle => 5, // kCGEventOtherMouseDown
        };

        $kCGEventMouseUp = $kCGEventMouseDown + 1; // Up Event

        $point = $this->ffi->new("CGPoint");
        $point->x = floatval($x);
        $point->y = floatval($y);

        $eventDown = $this->ffi->CGEventCreateMouseEvent(null, $kCGEventMouseDown, $point, $button->value);
        if ($eventDown === null) {
            throw new Exception("Failed to create mouse down event");
        }

        $eventUp = $this->ffi->CGEventCreateMouseEvent(null, $kCGEventMouseUp, $point, $button->value);
        if ($eventUp === null) {
            throw new Exception("Failed to create mouse up event");
        }

        $this->ffi->CGEventPost(0, $eventDown);
        $this->ffi->CGEventPost(0, $eventUp);
    }

    public function move(int $x = 0, int $y = 0): void
    {
        $scaleFactor = $this->getMacScaleFactor();
        $scaledX = (int)($x * $scaleFactor);
        $scaledY = (int)($y * $scaleFactor);

        $point = $this->ffi->new("struct CGPoint");
        $point->x = $scaledX;
        $point->y = $scaledY;

        $this->ffi->CGDisplayMoveCursorToPoint(0, $point);
    }

    public function getPosition(): array
    {
        $event = $this->ffi->CGEventCreate(0);
        $position = $this->ffi->CGEventGetLocation($event);

        return [
            'x' => $position->x,
            'y' => $position->y
        ];
    }

    private function getMacScaleFactor(): float
    {
        $output = [];
        exec("system_profiler SPDisplaysDataType | grep 'Resolution'", $output);

        if (empty($output)) {
            return 1.0;
        }
        if (preg_match('/Resolution:\s*(\d+)\s*x\s*(\d+)\s*\(Retina\)/', $output[0], $matches)) {
            return 2.0; // If Retina display, return scale factor 2.0
        }

        return 1.0;
    }

    /**
     * Send a key event (down or up).
     *
     * @param KeyCode $key The key to send the event for.
     * @param bool $isDown True for key down, false for key up.
     * @return void
     * @throws Exception If the event creation fails.
     */
    private function sendKeyEvent(KeyCode $key, bool $isDown): void
    {
        $event = $this->ffi->CGEventCreateKeyboardEvent(null, $key->getCode(), $isDown ? 1 : 0);

        if ($event === null) {
            throw new Exception("Failed to create keyboard event for key: {$key->name}");
        }

        $this->ffi->CGEventPost(0, $event);

        // Check button press. Maximum time - 50ms
        $startTime = microtime(true);
        $intervalUs = 2500; // First polling - 2.5ms

        while ((microtime(true) - $startTime) < 0.05) {
            if ($this->isKeyPressed($key) === $isDown) {
                return;
            }

            usleep($intervalUs);
            $intervalUs = min($intervalUs * 2, 20000); // Increase interval
        }

        throw new Exception("Key status not changed: {$key->name}. Its can be system problem or event bus speed so low");
    }
}