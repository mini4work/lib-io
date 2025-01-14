<?php

namespace M4W\LibIO\Entity;

use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\Enums\OS;
use M4W\LibIO\OSDetector;

class MouseButtonState
{
    private array $state = [
        MouseButton::Left->value => false,
        MouseButton::Right->value => false,
        MouseButton::Middle->value => false,
    ];

    public function __construct(bool $leftPressed = false, bool $rightPressed = false, bool $middlePressed = false)
    {
        $this->state[MouseButton::Left->value] = $leftPressed;
        $this->state[MouseButton::Right->value] = $rightPressed;
        $this->state[MouseButton::Middle->value] = $middlePressed;
    }

    public static function leftClick(): self
    {
        return new self(leftPressed: true);
    }

    public static function rightClick(): self
    {
        return new self(rightPressed: true);
    }

    public static function middleClick(): self
    {
        return new self(middlePressed: true);
    }

    public function getState(MouseButtonState $currentState): int
    {
        return match (OSDetector::detect()) {
            OS::Windows => $this->getWindowsState($currentState),
            default => 0x0001,
        };
    }

    public function setButtonState(MouseButton $button, bool $isPressed): self
    {
        $this->state[$button->value] = $isPressed;
        return $this;
    }

    public function resetState(): self
    {
        foreach ($this->state as &$button) {
            $button = false;
        }
        return $this;
    }

    private function getWindowsState(MouseButtonState $currentState): int
    {
        $result = 0x0001 | 0x8000;

        if ($currentState->state[MouseButton::Left->value] != $this->state[MouseButton::Left->value]) {
            if ($this->state[MouseButton::Left->value]) {
                $result |= 0x0002;
            } else {
                $result |= 0x0004;
            }
        }

        if ($currentState->state[MouseButton::Right->value] != $this->state[MouseButton::Right->value]) {
            if ($this->state[MouseButton::Right->value]) {
                $result |= 0x0008;
            } else {
                $result |= 0x0010;
            }
        }

        if ($currentState->state[MouseButton::Middle->value] != $this->state[MouseButton::Middle->value]) {
            if ($this->state[MouseButton::Middle->value]) {
                $result |= 0x0020;
            } else {
                $result |= 0x0040;
            }
        }

        return $result;
    }

    public function __toString(): string
    {
        $string = [];

        $map = [
            'LMB' => ($this->state[MouseButton::Left->value]?'Pressed':'Released'),
            'RMB' => ($this->state[MouseButton::Right->value]?'Pressed':'Released'),
            'MMB' => ($this->state[MouseButton::Middle->value]?'Pressed':'Released')
        ];

        foreach ($map as $value => $isPressed) {
            $string[] = $value.': '.$isPressed.';';
        }

        return implode(' ', $string);

    }
}
