<?php

namespace Rabble\MenuBundle\Item;

class MenuItem
{
    private string $id;
    private string $label;
    private string $uri;
    private ?MenuItem $parent;
    /** @var MenuItem[] */
    private array $children;
    private ?string $target;

    /**
     * @param MenuItem[] $children
     */
    public function __construct(string $id, string $label, string $uri, ?MenuItem $parent = null, array $children = [], ?string $target = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->uri = $uri;
        $this->parent = $parent;
        $this->children = $children;
        $this->target = $target;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param MenuItem[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }
}
