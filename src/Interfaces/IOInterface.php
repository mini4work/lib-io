<?php

namespace M4W\LibIO\Interfaces;

use M4W\LibIO\Entity\Vector2;
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\Enums\MouseButton;

interface IOInterface
{
    /**
     * Press a key (down + up).
     *
     * @param KeyCode|MouseButton $key The key to press.
     * @return void
     */
    public function press(KeyCode|MouseButton $key): void;

    /**
     * Press down a key.
     *
     * @param KeyCode|MouseButton $key The key to press down.
     * @return void
     */
    public function down(KeyCode|MouseButton $key): void;

    /**
     * Release a key.
     *
     * @param KeyCode|MouseButton $key The key to release.
     * @return void
     */
    public function up(KeyCode|MouseButton $key): void;

    /**
     * Checks if a specific key is currently pressed.
     *
     * @param KeyCode|MouseButton $key The key to check.
     * @return bool True if the key is pressed, false otherwise.
     */
    public function isKeyPressed(KeyCode|MouseButton $key): bool;

    /**
     * Simulates a mouse click.
     *
     * @param MouseButton $button The mouse button to click (left by default).
     * @param Vector2|null $point Vector2 object
     * @return void
     */
    public function click(MouseButton $button = MouseButton::Left, ?Vector2 $point = null): void;

    /**
     * Moves the mouse cursor to a specified position.
     *
     * @param Vector2 $point
     * @return void
     */
    public function move(Vector2 $point): void;

    /**
     * Drag the mouse with pressed mouse key from Vector2 to Vector2
     *
     * @param MouseButton $button
     * @param Vector2 $to
     * @param Vector2|null $from
     * @return void
     */
    public function drag(MouseButton $button, Vector2 $to, ?Vector2 $from = null): void;

    /**
     * Gets the current mouse cursor position.
     *
     * @return Vector2 An object with 'x' and 'y' fields representing the cursor position.
     */
    public function getPosition(): Vector2;

    /**
     * Gets the current screen size as a Vector2 with maximum X and maximum Y
     *
     * @return Vector2 An object with 'x' and 'y' fields
     */
    public function getScreenSize(): Vector2;
}
