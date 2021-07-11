<?php

namespace Rabble\MenuBundle\RabbleMenu;

class MenuManager implements MenuManagerInterface
{
    /** @var MenuDefinition[] */
    private array $menus;

    public function addMenu(MenuDefinition $menuDefinition): void
    {
        $this->menus[$menuDefinition->getName()] = $menuDefinition;
    }

    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getMenu(string $name): MenuDefinition
    {
        return $this->menus[$name];
    }

    public function removeMenu(string $name): void
    {
        unset($this->menus[$name]);
    }
}
