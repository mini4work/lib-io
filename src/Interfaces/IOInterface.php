<?php

namespace M4W\LibIO\Interfaces;

use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Enums\MouseButton;

interface IOInterface
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
     * @param KeyCode $key The key to check.
     * @return bool True if the key is pressed, false otherwise.
     */
    public function isKeyPressed(KeyCode $key): bool;

    /**
     * Simulates a mouse click.
     *
     * @param MouseButton $button The mouse button to click (left by default).
     * @param int|null $x Optional X-coordinate to move before clicking.
     * @param int|null $y Optional Y-coordinate to move before clicking.
     * @return void
     */
    public function click(MouseButton $button = MouseButton::Left, ?int $x = null, ?int $y = null): void;

    /**
     * Moves the mouse cursor to a specified position.
     *
     * @param int $x The X-coordinate to move to.
     * @param int $y The Y-coordinate to move to.
     * @return void
     */
    public function move(int $x = 0, int $y = 0): void;

    /**
     * Gets the current mouse cursor position.
     *
     * @return array An associative array with 'x' and 'y' keys representing the cursor position.
     */
    public function getPosition(): array;
}
