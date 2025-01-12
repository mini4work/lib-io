<?php

namespace M4W\LibIO\Keyboard;

use Exception;
use FFI;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Interfaces\FFIInterface;
use M4W\LibIO\Interfaces\KeyboardInterface;
use RuntimeException;

class KeyboardMac implements KeyboardInterface
{
    /** @var FFI|FFIInterface */
    private FFI|FFIInterface $ffi;

    public function __construct()
    {
        /** @var FFI|FFIInterface $ffi */
        $this->ffi = FFI::cdef("
            typedef void* CGEventRef;
            typedef unsigned short CGKeyCode;
            typedef void* CGEventSourceRef;

            CGEventRef CGEventCreateKeyboardEvent(CGEventSourceRef source, CGKeyCode virtualKey, int keyDown);
            void CGEventPost(int tap, CGEventRef event);
            int CGEventSourceKeyState(CGEventSourceRef source, CGKeyCode keyCode);
        ", "/System/Library/Frameworks/ApplicationServices.framework/ApplicationServices");
    }

    public function press(KeyCode $key): void
    {
        $this->down($key);
        usleep(10000); // 10ms
        $this->up($key);
    }

    public function down(KeyCode $key): void
    {
        $this->sendKeyEvent($key, true);
    }

    public function up(KeyCode $key): void
    {
        $this->sendKeyEvent($key, false);
    }

    public function isKeyPressed(KeyCode $key): bool
    {
        $keyCode = $key->getCode(); // Отримуємо платформозалежний код клавіші
        $state = $this->ffi->CGEventSourceKeyState(null, $keyCode); // null як джерело подій
        return $state === 1;
    }

    /**
     * Send a key event (down or up).
     *
     * @param KeyCode $key The key to send the event for.
     * @param bool $isDown True for key down, false for key up.
     * @return void
     * @throws RuntimeException If the event creation fails.
     * @throws Exception
     */
    private function sendKeyEvent(KeyCode $key, bool $isDown): void
    {
        $virtualKey = $key->getCode();
        $event = $this->ffi->CGEventCreateKeyboardEvent(null, $virtualKey, $isDown ? 1 : 0);

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
