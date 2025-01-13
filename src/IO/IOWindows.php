<?php

namespace M4W\LibIO\IO;

use Exception;
use FFI;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\IOInterface;

class IOWindows implements IOInterface
{
    private FFI|FFIInterface $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef(
            file_get_contents(realpath('src/') . '/CHeaders/user32.h'),
            "user32.dll"
        );
    }

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

        $scaledX = (int)(($x / $this->getScreenWidth()) * 65535) + 1;
        $scaledY = (int)(($y / $this->getScreenHeight()) * 65535) + 1;

        $scaledX = min(max($scaledX, 0), 65535);
        $scaledY = min(max($scaledY, 0), 65535);

        $inputs = $this->ffi->new("INPUT[2]");

        $inputs[0]->type = 0; // INPUT_MOUSE
        $inputs[0]->mi->dx = $scaledX;
        $inputs[0]->mi->dy = $scaledY;
        $inputs[0]->mi->mouseData = 0;
        $inputs[0]->mi->dwFlags = match ($button) {
            MouseButton::Left => 0x0002 | 0x8000,   // MOUSEEVENTF_LEFTDOWN | MOUSEEVENTF_ABSOLUTE
            MouseButton::Right => 0x0008 | 0x8000,  // MOUSEEVENTF_RIGHTDOWN | MOUSEEVENTF_ABSOLUTE
            MouseButton::Middle => 0x0020 | 0x8000, // MOUSEEVENTF_MIDDLEDOWN | MOUSEEVENTF_ABSOLUTE
        };
        $inputs[0]->mi->time = 0;
        $inputs[0]->mi->dwExtraInfo = 0;

        $inputs[1]->type = 0; // INPUT_MOUSE
        $inputs[1]->mi->dx = $scaledX;
        $inputs[1]->mi->dy = $scaledY;
        $inputs[1]->mi->mouseData = 0;
        $inputs[1]->mi->dwFlags = match ($button) {
            MouseButton::Left => 0x0004 | 0x8000,   // MOUSEEVENTF_LEFTUP | MOUSEEVENTF_ABSOLUTE
            MouseButton::Right => 0x0010 | 0x8000,  // MOUSEEVENTF_RIGHTUP | MOUSEEVENTF_ABSOLUTE
            MouseButton::Middle => 0x0040 | 0x8000, // MOUSEEVENTF_MIDDLEUP | MOUSEEVENTF_ABSOLUTE
        };
        $inputs[1]->mi->time = 0;
        $inputs[1]->mi->dwExtraInfo = 0;

        $inputs[0]->mi->dwFlags |= 0x0001; // MOUSEEVENTF_MOVE
        $inputs[1]->mi->dwFlags |= 0x0001; // MOUSEEVENTF_MOVE

        $result = $this->ffi->SendInput(2, $inputs, FFI::sizeof($inputs[0]));

        if ($result === 0) {
            throw new Exception("Failed to send mouse input");
        }
    }

    public function move(int $x = 0, int $y = 0): void
    {
        $this->ffi->SetCursorPos($x, $y);
    }

    /**
     * @throws Exception
     */
    public function getPosition(): array
    {
        $point = $this->ffi->new("POINT");
        $result = $this->ffi->GetCursorPos(FFI::addr($point));

        if ($result === 0) {
            throw new Exception("Failed to get cursor position");
        }

        return ['x' => $point->x, 'y' => $point->y];
    }

    private function getScreenWidth(): int
    {
        return $this->ffi->GetSystemMetrics(0); // 0: SM_CXSCREEN
    }

    private function getScreenHeight(): int
    {
        return $this->ffi->GetSystemMetrics(1); // 1: SM_CYSCREEN
    }
}