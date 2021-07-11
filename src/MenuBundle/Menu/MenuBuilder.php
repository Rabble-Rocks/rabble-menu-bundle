<?php

namespace Rabble\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Rabble\MenuBundle\Item\Fetcher\ItemFetcherInterface;
use Rabble\MenuBundle\Item\MenuItem;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private FactoryInterface $factory;
    private ItemFetcherInterface $itemFetcher;
    private RequestStack $requestStack;
    private string $menuName;

    public function __construct(
        FactoryInterface $factory,
        ItemFetcherInterface $itemFetcher,
        RequestStack $requestStack,
        string $menuName
    ) {
        $this->factory = $factory;
        $this->itemFetcher = $itemFetcher;
        $this->requestStack = $requestStack;
        $this->menuName = $menuName;
    }

    public function build(): ItemInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $menu = $this->factory->createItem('root');
        if (null === $request) {
            return $menu;
        }
        $rootItems = $this->itemFetcher->fetch($this->menuName, $request->getLocale());
        foreach ($rootItems as $menuItem) {
            $this->addItem($menuItem, $menu);
        }

        return $menu;
    }

    private function addItem(MenuItem $menuItem, ItemInterface $compiledItem): void
    {
        $child = $compiledItem->addChild($menuItem->getLabel(), [
            'uri' => $menuItem->getUri(),
            'linkAttributes' => null === $menuItem->getTarget() ? [] : ['target' => $menuItem->getTarget()],
            'label' => $menuItem->getLabel(),
        ]);
        foreach ($menuItem->getChildren() as $childItem) {
            $this->addItem($childItem, $child);
        }
    }
}
