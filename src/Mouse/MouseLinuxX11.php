<?php

namespace M4W\LibIO\Mouse;

use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\MouseInterface;
use FFI;
use RuntimeException;

class MouseLinuxX11 implements MouseInterface
{
    private FFI|FFIInterface $ffi;
    private ?object $display;
    private ?object $rootWindow;

    public function __construct()
    {
        $this->ffi = FFI::cdef(<<<'CDEF'
        typedef struct _XDisplay Display;
        typedef unsigned long Window;
        
        Display* XOpenDisplay(char* display_name);
        void XCloseDisplay(Display* display);
        Window DefaultRootWindow(Display* display);
        void XWarpPointer(Display* display, Window src_w, Window dest_w, int src_x, int src_y, unsigned int src_width, unsigned int src_height, int dest_x, int dest_y);
        int XQueryPointer(Display* display, Window w, Window* root_return, Window* child_return, int* root_x_return, int* root_y_return, int* win_x_return, int* win_y_return, unsigned int* mask_return);
        CDEF
            , "libX11.so");

        $this->display = $this->ffi->XOpenDisplay(null);
        if ($this->display === null) {
            throw new RuntimeException("Unable to open X display");
        }
        $this->rootWindow = $this->ffi->DefaultRootWindow($this->display);
    }

    public function __destruct()
    {
        if ($this->display !== null) {
            $this->ffi->XCloseDisplay($this->display);
        }
    }

    public function move(int $x = 0, int $y = 0): void
    {
        $this->ffi->XWarpPointer($this->display, 0, $this->rootWindow, 0, 0, 0, 0, $x, $y);
        $this->ffi->XFlush($this->display);
    }

    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void
    {
        if ($x !== null && $y !== null) {
            $this->move($x, $y);
        }

        $buttonMask = match ($button) {
            MouseButton::Left => 1,
            MouseButton::Middle => 2,
            MouseButton::Right => 3,
        };

        $this->sendButtonEvent($buttonMask, true);  // Button press
        usleep(10000); // Short delay to simulate click
        $this->sendButtonEvent($buttonMask, false); // Button release
    }

    public function getPosition(): array
    {
        $rootX = FFI::new('int');
        $rootY = FFI::new('int');
        $winX = FFI::new('int');
        $winY = FFI::new('int');
        $mask = FFI::new('unsigned int');
        $childReturn = FFI::new('Window');
        $rootReturn = FFI::new('Window');

        $this->ffi->XQueryPointer($this->display, $this->rootWindow, FFI::addr($rootReturn), FFI::addr($childReturn), FFI::addr($rootX), FFI::addr($rootY), FFI::addr($winX), FFI::addr($winY), FFI::addr($mask));

        return ['x' => $rootX->cdata, 'y' => $rootY->cdata];
    }

    private function sendButtonEvent(int $button, bool $isPress): void
    {
        $eventType = $isPress ? 4 : 5; // 4 = ButtonPress, 5 = ButtonRelease

        $event = $this->ffi->new('XEvent');
        $event->type = $eventType;
        $event->xbutton->button = $button;
        $event->xbutton->same_screen = true;

        $this->ffi->XSendEvent($this->display, $this->rootWindow, true, 0xFFFFFF, FFI::addr($event));
        $this->ffi->XFlush($this->display);
    }
}
