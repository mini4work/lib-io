<?php

namespace M4W\LibIO\Keyboard;

use Exception;
use FFI;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\KeyboardInterface;

class KeyboardWindows implements KeyboardInterface
{
    private FFI|FFIInterface $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef(<<<'CDEF'
            typedef unsigned int DWORD;
            typedef unsigned short WORD;
            typedef unsigned short SHORT;
            typedef unsigned int UINT;
            typedef unsigned long long ULONG_PTR;
        
            typedef struct {
                WORD wVk;            // 2 bytes
                WORD wScan;          // 2 bytes
                DWORD dwFlags;       // 4 bytes
                DWORD time;          // 4 bytes
                ULONG_PTR dwExtraInfo; // 8 bytes
            } KEYBDINPUT;
        
            typedef struct {
                DWORD type;          // 4 bytes
                KEYBDINPUT ki;       // 24 bytes
                DWORD padding[2];    // 8 bytes padding (FOR PADDING)
            } INPUT;
        
            UINT SendInput(UINT nInputs, INPUT* pInputs, int cbSize);
            SHORT GetAsyncKeyState(int vKey);
        CDEF, "user32.dll");
    }

    /**
     * @throws Exception
     */
    public function press(KeyCode $key): void
    {
        $this->down($key);
        usleep(10000); // Sleep for 10 milliseconds to simulate a key press
        $this->up($key);
    }

    /**
     * @throws Exception
     */
    public function down(KeyCode $key): void
    {
        $keyCode = $key->getCode();

        $input = $this->ffi->new("INPUT[1]");
        $input[0]->type = 1; // INPUT_KEYBOARD
        $input[0]->ki->wVk = $keyCode; // Set wVk to 0 when using scan code
        $input[0]->ki->wScan = 0; // Use scan code instead
        $input[0]->ki->dwFlags = 0x0000; // KEYEVENTF_SCANCODE

        $result = $this->ffi->SendInput(1, FFI::addr($input[0]), FFI::sizeof($input[0]));
        if ($result === 0) {
            throw new Exception("Failed to send keyboard input (key down)");
        }
    }

    /**
     * @throws Exception
     */
    public function up(KeyCode $key): void
    {
        $keyCode = $key->getCode();

        $input = $this->ffi->new("INPUT[1]");
        $input[0]->type = 1; // INPUT_KEYBOARD
        $input[0]->ki->wVk = $keyCode; // Set wVk to 0 when using scan code
        $input[0]->ki->wScan = 0; // Use scan code instead
        $input[0]->ki->dwFlags = 0x0002; // KEYEVENTF_SCANCODE | KEYEVENTF_KEYUP

        $result = $this->ffi->SendInput(1, FFI::addr($input[0]), FFI::sizeof($input[0]));
        if ($result === 0) {
            throw new Exception("Failed to send keyboard input (key up)");
        }
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        $keyCode = $key->getCode();

        $state = $this->ffi->GetAsyncKeyState($keyCode);
        return ($state & 0x8000) !== 0; // Check if the most significant bit is set
    }
}
