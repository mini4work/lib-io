<?php

namespace M4W\LibIO\IO;

use Exception;
use FFI;
use M4W\LibIO\Entity\MouseButtonState;
use M4W\LibIO\Entity\Vector2;
use M4W\LibIO\Entity\Windows\Interfaces\InputEventInterface;
use M4W\LibIO\Entity\Windows\KeyboardInputEvent;
use M4W\LibIO\Entity\Windows\MouseInputEvent;
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

    /**
     * @throws Exception
     */
    public function press(KeyCode|MouseButton $key): void
    {
        $this->down($key);
        usleep(10_000);
        $this->up($key);
    }

    /**
     * @throws Exception
     */
    public function down(KeyCode|MouseButton $key): void
    {
        $this->sendEvent([
            match (get_class($key)) {
                KeyCode::class => new KeyboardInputEvent($key, true),
                MouseButton::class => $this->createMouseEvent(buttonState: $this->getPressedMouseButtons()->setButtonState($key, true)),
            }
        ]);
    }

    /**
     * @throws Exception
     */
    public function up(KeyCode|MouseButton $key): void
    {
        $getMouseState = $this->getPressedMouseButtons();
        $this->sendEvent([
            match (get_class($key)) {
                KeyCode::class => new KeyboardInputEvent($key, false),
                MouseButton::class => $this->createMouseEvent(buttonState: $this->getPressedMouseButtons()->setButtonState($key, false)),
            }
        ]);
    }

    /**
     * @throws Exception
     */
    public function isKeyPressed(KeyCode|MouseButton $key): bool
    {
        if ($key instanceof MouseButton) {
            $keyCode = match ($key) {
                MouseButton::Left => 0x01,
                MouseButton::Right => 0x02,
                MouseButton::Middle => 0x04,
            };
        } elseif ($key instanceof KeyCode) {
            $keyCode = $key->getCode();
        } else {
            throw new Exception("Unsupported key pressed");
        }
        $state = $this->ffi->GetAsyncKeyState($keyCode);
        return ($state & 0x8000) !== 0;
    }

    /**
     * @throws Exception
     */
    public function click(?MouseButton $button = null, ?Vector2 $point = null): void
    {
        if (is_null($button)) {
            $button = MouseButton::Left;
        }

        $this->move($point ?? $this->getPosition());
        $this->down($button);

        usleep(10_000);
        $this->up($button);
        usleep(10_000);
    }

    /**
     * @throws Exception
     */
    public function move(Vector2 $point): void
    {
        $this->sendEvent([
            new MouseInputEvent($point, $this->getPressedMouseButtons(), $this->getPressedMouseButtons())
        ]);
    }

    /**
     * @throws Exception
     */
    public function drag(MouseButton $button, Vector2 $to, ?Vector2 $from = null): void
    {
        $this->move($from);

        $this->down($button);

        // Smooth move
        $pointArray = Vector2::smoothLine($from, $to);

        foreach ($pointArray as $point) {
            $this->move($point);
        }
        // Smooth move END

        $this->move($to);

        $this->up($button);
    }

    /**
     * @throws Exception
     */
    public function getPosition(): Vector2
    {
        $point = $this->ffi->new("POINT");
        $result = $this->ffi->GetCursorPos(FFI::addr($point));

        if ($result === 0) {
            throw new Exception("Failed to get cursor position");
        }

        $point = new Vector2($point->x+1, $point->y+1);

        return $point;
    }

    public function getScreenSize(): Vector2
    {
        return new Vector2(
            $this->ffi->GetSystemMetrics(0),
            $this->ffi->GetSystemMetrics(1)
        );
    }

    /**
     * @param InputEventInterface[] $events
     * @return void
     * @throws Exception
     */
    private function sendEvent(array $events): void
    {
        $inputs = $this->ffi->new("INPUT[" . count($events) . "]");

        foreach ($events as $i => $event) {
            $event->mapToCData($inputs[$i], $this->getScreenSize());
        }

        $result = $this->ffi->SendInput(count($events), FFI::addr($inputs[0]), FFI::sizeof($inputs[0]));
        if ($result === 0) {
            throw new Exception("Failed to send input events");
        }
        usleep(10_000);
    }
    public function getPressedMouseButtons(): MouseButtonState
    {
        $vkCodes = [
            MouseButton::Left->value => 0x01,
            MouseButton::Right->value => 0x02,
            MouseButton::Middle->value => 0x04,
        ];

        $leftPressed = ($this->ffi->GetAsyncKeyState($vkCodes[MouseButton::Left->value]) & 0x8000) !== 0;
        $rightPressed = ($this->ffi->GetAsyncKeyState($vkCodes[MouseButton::Right->value]) & 0x8000) !== 0;
        $middlePressed = ($this->ffi->GetAsyncKeyState($vkCodes[MouseButton::Middle->value]) & 0x8000) !== 0;

        return new MouseButtonState($leftPressed, $rightPressed, $middlePressed);
    }

    /**
     * @throws Exception
     */
    private function createMouseEvent(?Vector2 $point = null, ?MouseButtonState $buttonState = null, ?MouseButtonState $currentState = null): MouseInputEvent
    {
        return new MouseInputEvent(
            $point ?? $this->getPosition(),
                $buttonState ?? $this->getPressedMouseButtons(),
                $currentState ?? $this->getPressedMouseButtons()
        );
    }
}