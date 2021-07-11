<?php

namespace Rabble\MenuBundle\RabbleMenu;

interface MenuManagerInterface
{
    public function addMenu(MenuDefinition $menuDefinition): void;

    /**
     * @return MenuDefinition[]
     */
    public function getMenus(): array;

    public function getMenu(string $name): MenuDefinition;

    public function removeMenu(string $name): void;
}
