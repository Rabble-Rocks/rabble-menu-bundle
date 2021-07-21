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
    private ItemInterface $cached;

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
        if (isset($this->cached)) {
            return $this->cached;
        }
        $request = $this->requestStack->getCurrentRequest();
        $menu = $this->factory->createItem('root');
        if (null === $request) {
            return $menu;
        }
        $rootItems = $this->itemFetcher->fetch($this->menuName, $request->getLocale());
        foreach ($rootItems as $menuItem) {
            $this->addItem($menuItem, $menu);
        }

        return $this->cached = $menu;
    }

    private function addItem(MenuItem $menuItem, ItemInterface $compiledItem): void
    {
        $parentItem = $compiledItem->getParent();
        while (null !== $parentItem) {
            $routes = $parentItem->getExtra('routes');
            if (null === $routes) {
                $parentItem = $parentItem->getParent();

                continue;
            }
            $routes[] = $menuItem->getId();
            $parentItem->setExtra('routes', $routes);
            $parentItem = $parentItem->getParent();
        }
        $child = $compiledItem->addChild($menuItem->getLabel(), [
            'uri' => $menuItem->getUri(),
            'linkAttributes' => null === $menuItem->getTarget() ? [] : ['target' => $menuItem->getTarget()],
            'label' => $menuItem->getLabel(),
            'extras' => [
                'routes' => [$menuItem->getId()],
            ],
        ]);
        foreach ($menuItem->getChildren() as $childItem) {
            $this->addItem($childItem, $child);
        }
    }
}
