<?php

namespace M4W\LibIO\Interfaces;

use M4W\LibIO\Enums\KeyCode;

interface KeyboardInterface
{
    /**
     * Press a key (down + up).
     *
     * @param KeyCode $key The key to press.
     * @return void
     */
    public function press(KeyCode $key): void;

    /**
     * Press down a key.
     *
     * @param KeyCode $key The key to press down.
     * @return void
     */
    public function down(KeyCode $key): void;

    /**
     * Release a key.
     *
     * @param KeyCode $key The key to release.
     * @return void
     */
    public function up(KeyCode $key): void;

    /**
     * Checks if a specific key is currently pressed.
     *
     * @param KeyCode $key
     * @return bool
     */
    public function isKeyPressed(KeyCode $key): bool;
}
