<?php

namespace Rabble\MenuBundle\Item\Fetcher;

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Service\IndexService;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Rabble\ContentBundle\Persistence\Manager\ContentManagerInterface;
use Rabble\MenuBundle\Item\MenuItem;
use Symfony\Component\Routing\RouterInterface;

class ElasticsearchItemFetcher implements ItemFetcherInterface
{
    private ArrayCollection $indexes;
    private RouterInterface $router;
    private ContentManagerInterface $contentManager;

    public function __construct(
        ArrayCollection $indexes,
        RouterInterface $router,
        ContentManagerInterface $contentManager
    ) {
        $this->indexes = $indexes;
        $this->router = $router;
        $this->contentManager = $contentManager;
    }

    public function fetch(string $menuName, string $locale): array
    {
        /** @var IndexService $index */
        $index = $this->indexes['content-'.$locale];
        $search = $index->createSearch();
        $search->addQuery($bool = new BoolQuery());
        $bool->add(new TermQuery('properties.menu.keyword', $menuName, ['case_insensitive' => true]));
        $results = $index->search($search->toArray());
        $menuItems = [];
        foreach ($results['hits']['hits'] as $hit) {
            $content = $this->contentManager->find($hit['_id']);
            if (null === $content) {
                continue;
            }
            $menuItems[] = new MenuItem(
                $content->getTitle(),
                $this->router->generate($content->getUuid())
            );
        }

        return $menuItems;
    }
}
