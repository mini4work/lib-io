<?php

namespace M4W\LibIO\Mouse;

use Exception;
use FFI;
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\MouseInterface;

class MouseMac implements MouseInterface
{
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

        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
    typedef struct {
        double x;
        double y;
    } CGPoint;

    typedef void* CGEventRef;
    typedef unsigned int CGEventType;
    typedef unsigned int CGMouseButton;
    typedef void* CGEventSourceRef;

    CGEventRef CGEventCreateMouseEvent(CGEventSourceRef source, CGEventType mouseType, CGPoint mouseCursorPosition, CGMouseButton mouseButton);
    void CGEventPost(int tap, CGEventRef event);
", "/System/Library/Frameworks/ApplicationServices.framework/ApplicationServices");

        $point = $ffi->new("CGPoint");
        $point->x = floatval($x);
        $point->y = floatval($y);

        $eventDown = $ffi->CGEventCreateMouseEvent(null, $kCGEventMouseDown, $point, $button->value);
        if ($eventDown === null) {
            throw new Exception("Failed to create mouse down event");
        }

        $eventUp = $ffi->CGEventCreateMouseEvent(null, $kCGEventMouseUp, $point, $button->value);
        if ($eventUp === null) {
            throw new Exception("Failed to create mouse down event");
        }

        $ffi->CGEventPost(0, $eventDown);
        $ffi->CGEventPost(0, $eventUp);
    }

    public function move(int $x = 0, int $y = 0): void
    {
        $scaleFactor = $this->getMacScaleFactor();
        $scaledX = (int)($x * $scaleFactor);
        $scaledY = (int)($y * $scaleFactor);

        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
        void CGDisplayMoveCursorToPoint(int display, struct CGPoint { double x; double y; } point);
        ","/System/Library/Frameworks/ApplicationServices.framework/ApplicationServices");

        $point = $ffi->new("struct CGPoint");
        $point->x = $scaledX;
        $point->y = $scaledY;

        $ffi->CGDisplayMoveCursorToPoint(0, $point);
    }

    private function getMacScaleFactor(): float
    {
        $output = [];
        exec("system_profiler SPDisplaysDataType | grep 'Resolution'", $output);

        if (empty($output)) {
            return 1.0; // Return default scale factor
        }

        // Example line: "Resolution: 2560 x 1600 (Retina)"
        if (preg_match('/Resolution:\s*(\d+)\s*x\s*(\d+)\s*\(Retina\)/', $output[0], $matches)) {
            return 2.0; // If Retina display, return scale factor 2.0
        }

        return 1.0;
    }

    public function getPosition(): array
    {
        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
        typedef struct {
            double x;
            double y;
        } CGPoint;

        typedef void* CGEventRef;

        CGEventRef CGEventCreate(int source);
        CGPoint CGEventGetLocation(CGEventRef event);
    ", "/System/Library/Frameworks/ApplicationServices.framework/ApplicationServices");

        // Створюємо подію, щоб отримати позицію курсора
        $event = $ffi->CGEventCreate(0);
        $position = $ffi->CGEventGetLocation($event);

        return [
            'x' => $position->x,
            'y' => $position->y
        ];
    }
}