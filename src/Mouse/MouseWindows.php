<?php

namespace M4W\LibIO\Mouse;

use Exception;
use FFI;
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\MouseInterface;

class MouseWindows implements MouseInterface
{
    private FFI|FFIInterface $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef("
            typedef unsigned int DWORD;
            typedef unsigned short WORD;
            typedef unsigned long ULONG_PTR;
            typedef int LONG;
            typedef unsigned int UINT;

            typedef struct {
                LONG dx;
                LONG dy;
                DWORD mouseData;
                DWORD dwFlags;
                DWORD time;
                ULONG_PTR dwExtraInfo;
            } MOUSEINPUT;

            typedef struct {
                DWORD type;
                union {
                    MOUSEINPUT mi;
                };
            } INPUT;

            typedef struct {
                LONG x;
                LONG y;
            } POINT;

            UINT SendInput(UINT nInputs, INPUT* pInputs, int cbSize);
            int GetCursorPos(POINT* lpPoint);
            int SetCursorPos(int X, int Y);
            int GetSystemMetrics(int nIndex);
        ", "user32.dll");
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

        $scaledX = (int) (($x / $this->getScreenWidth()) * 65535);
        $scaledY = (int) (($y / $this->getScreenHeight()) * 65535);

        // Створюємо масив із двох структур для натискання та відпускання кнопки миші
        $inputs = $this->ffi->new("INPUT[2]");

        // Налаштовуємо першу структуру — натискання кнопки миші
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

        // Налаштовуємо другу структуру — відпускання кнопки миші
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

        // Викликаємо SendInput із масивом структур
        $result = $this->ffi->SendInput(2, $inputs, FFI::sizeof($inputs[0]));
        FFI::free($inputs);

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
            FFI::free($point);
            throw new Exception("Failed to get cursor position");
        }

        $position = ['x' => $point->x, 'y' => $point->y];
        FFI::free($point);

        return $position;
    }

    private function getScreenWidth(): int
    {
        return $this->ffi->GetSystemMetrics(0); // 0: SM_CXSCREEN (ширина основного екрану)
    }

    private function getScreenHeight(): int
    {
        return $this->ffi->GetSystemMetrics(1); // 1: SM_CYSCREEN (висота основного екрану)
    }
}
