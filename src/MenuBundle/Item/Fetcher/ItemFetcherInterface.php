<?php

namespace Rabble\MenuBundle\Item\Fetcher;

use Rabble\MenuBundle\Item\MenuItem;

interface ItemFetcherInterface
{
    /**
     * @return MenuItem[]
     */
    public function fetch(string $menuName, string $locale): array;
}
