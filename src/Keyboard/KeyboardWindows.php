<?php

namespace M4W\LibIO\Keyboard;

use FFI;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\KeyboardInterface;

class KeyboardWindows implements KeyboardInterface
{
    public function press(KeyCode $key): void
    {
        $this->down($key);
        usleep(10000); // 10ms
        $this->up($key);
    }

    public function down(KeyCode $key): void
    {
        $keyCode = $key->getCode(); // Отримуємо платформозалежний код клавіші

        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
        typedef struct {
            DWORD type;
            union {
                struct {
                    WORD wVk;
                    WORD wScan;
                    DWORD dwFlags;
                    DWORD time;
                    ULONG_PTR dwExtraInfo;
                } ki;
            };
        } INPUT;

        UINT SendInput(UINT nInputs, INPUT* pInputs, int cbSize);
    ", "user32.dll");

        $input = $ffi->new("INPUT");
        $input->type = 1; // INPUT_KEYBOARD
        $input->ki->wVk = $keyCode;
        $input->ki->dwFlags = 0; // KEYEVENTF_KEYDOWN

        $ffi->SendInput(1, FFI::addr($input), FFI::sizeof($input));
    }

    public function up(KeyCode $key): void
    {
        $keyCode = $key->getCode(); // Отримуємо платформозалежний код клавіші

        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
        typedef struct {
            DWORD type;
            union {
                struct {
                    WORD wVk;
                    WORD wScan;
                    DWORD dwFlags;
                    DWORD time;
                    ULONG_PTR dwExtraInfo;
                } ki;
            };
        } INPUT;

        UINT SendInput(UINT nInputs, INPUT* pInputs, int cbSize);
    ", "user32.dll");

        $input = $ffi->new("INPUT");
        $input->type = 1; // INPUT_KEYBOARD
        $input->ki->wVk = $keyCode;
        $input->ki->dwFlags = 2; // KEYEVENTF_KEYUP

        $ffi->SendInput(1, FFI::addr($input), FFI::sizeof($input));
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        $keyCode = $key->getCode(); // Отримуємо платформозалежний код клавіші

        /** @var FFI|FFIInterface $ffi */
        $ffi = FFI::cdef("
        SHORT GetAsyncKeyState(int vKey);
    ", "user32.dll");

        $state = $ffi->GetAsyncKeyState($keyCode);
        return ($state & 0x8000) !== 0;
    }
}
