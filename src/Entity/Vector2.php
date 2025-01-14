<?php

namespace M4W\LibIO\Entity;

class Vector2
{
    public function __construct(public float $x, public float $y) {}

    public static function zero(): Vector2
    {
        return new Vector2(0, 0);
    }

    public function moveDown(float $pixels): self
    {
        $this->x += $pixels;
        return $this;
    }

    public function moveUp(float $pixels): self
    {
        $this->x -= $pixels;
        return $this;
    }

    public function moveLeft(float $pixels): self
    {
        $this->y -= $pixels;
        return $this;
    }

    public function moveRight(float $pixels): self
    {
        $this->y += $pixels;
        return $this;
    }

    public function distance(Vector2 $point = new Vector2(0,0)): float
    {
        return sqrt(pow($this->x - $point->x, 2) + pow($this->y - $point->y, 2));
    }

    public function normalize(): self
    {
        $length = sqrt($this->x ** 2 + $this->y ** 2);
        if ($length == 0) {
            return $this;
        }
        $this->x /= $length;
        $this->y /= $length;
        return $this;
    }

    public function addPoint(Vector2 $point): self
    {
        $this->x += $point->x;
        $this->y += $point->y;
        return $this;
    }

    public function scale(float $scale): self
    {
        $this->x *= $scale;
        $this->y *= $scale;
        return $this;
    }

    public function reverse(): self
    {
        return $this->scale(-1);
    }

    public function moveInDirection(Vector2 $direction, float $length): self
    {

        return $this->addPoint($direction->normalize()->scale($length));
    }

    public static function smoothLine(Vector2 $from, Vector2 $to): array
    {
        $result[] = $from;
        $vector = new Vector2($to->x - $from->x, $to->y - $from->y)->normalize();

        $currentPosition = new Vector2($from->x, $from->y);

        $steps = 5;
        $shortLengthOne = 0.5;
        $shortLength = $steps * $shortLengthOne;
        $longLength = $from->distance($to) - ($shortLength * 2);

        for ($i = 0; $i < $steps; $i++) {
            $result[] = clone $currentPosition->moveInDirection($vector, $shortLengthOne);
        }

        $result[] = clone $currentPosition->moveInDirection($vector, $longLength);

        for ($i = 0; $i < $steps; $i++) {
            $result[] = clone $currentPosition->moveInDirection($vector, $shortLengthOne);
        }

        return $result;
    }

    public function __toString(): string
    {
        return "x: {$this->x}, y: {$this->y}";
    }
}