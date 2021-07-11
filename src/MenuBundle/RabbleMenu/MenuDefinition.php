<?php

namespace Rabble\MenuBundle\RabbleMenu;

class MenuDefinition
{
    private string $name;
    private int $maxDepth;
    private array $attributes;

    public function __construct(string $name, int $maxDepth, array $attributes)
    {
        $this->name = $name;
        $this->maxDepth = $maxDepth;
        $this->attributes = $attributes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute(string $name): void
    {
        unset($this->attributes[$name]);
    }
}
