<?php

namespace M4W\LibIO\Keyboard;

use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\KeyboardInterface;
use M4W\LibIO\Enums\KeyCode;
use FFI;
use RuntimeException;

class KeyboardLinuxX11 implements KeyboardInterface
{
    private FFI|FFIInterface $ffi;
    private ?object $display;

    public function __construct()
    {
        $this->ffi = FFI::cdef(<<<'CDEF'
        typedef struct _XDisplay Display;
        typedef unsigned long Window;
        typedef unsigned int KeySym;
        typedef unsigned int KeyCode;

        Display* XOpenDisplay(char* display_name);
        void XCloseDisplay(Display* display);
        Window DefaultRootWindow(Display* display);
        KeyCode XKeysymToKeycode(Display* display, KeySym keysym);
        void XTestFakeKeyEvent(Display* display, unsigned int keycode, int is_press, unsigned long delay);
        CDEF
            , "libX11.so");

        $this->display = $this->ffi->XOpenDisplay(null);
        if ($this->display === null) {
            throw new RuntimeException("Unable to open X display");
        }
    }

    public function __destruct()
    {
        if ($this->display !== null) {
            $this->ffi->XCloseDisplay($this->display);
        }
    }

    public function down(KeyCode $key): void
    {
        $this->sendKeyEvent($key, true);
    }

    public function up(KeyCode $key): void
    {
        $this->sendKeyEvent($key, false);
    }

    public function press(KeyCode $key): void
    {
        $this->down($key);
        usleep(50000); // 50 ms delay to simulate key press
        $this->up($key);
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        // X11 does not provide a simple way to check key state, so this method may require additional logic.
        throw new RuntimeException("Method isKeyPressed is not implemented for Linux");
    }

    private function sendKeyEvent(KeyCode $key, bool $isPress): void
    {
        $keyCode = $this->ffi->XKeysymToKeycode($this->display, $key->getCode());
        if ($keyCode === 0) {
            throw new RuntimeException("Invalid KeyCode: {$key->name}");
        }

        $this->ffi->XTestFakeKeyEvent($this->display, $keyCode, $isPress ? 1 : 0, 0);
        $this->ffi->XFlush($this->display);
    }
}
