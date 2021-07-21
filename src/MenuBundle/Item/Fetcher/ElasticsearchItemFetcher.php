<?php

namespace Rabble\MenuBundle\Item\Fetcher;

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Service\IndexService;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Rabble\ContentBundle\Persistence\Document\ContentDocument;
use Rabble\ContentBundle\Persistence\Document\StructuredDocument;
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

        return $this->buildMenuStructure($results['hits']['hits']);
    }

    private function buildMenuStructure(array $hits, ?MenuItem $parent = null): array
    {
        if (null === $parent) {
            $rootContent = $this->contentManager->find(StructuredDocument::ROOT_NODE);
            $rootContent = $rootContent instanceof StructuredDocument ? $rootContent->getChildren() : [];
        } else {
            $rootContent = [$this->contentManager->find($parent->getId())];
        }
        $indexed = [];
        foreach ($hits as $hit) {
            $indexed[$hit['_id']] = true;
        }

        $parentHits = [];
        $menuItems = [];
        foreach ($hits as $hit) {
            $content = $this->contentManager->find($hit['_id']);
            if (!$content instanceof ContentDocument) {
                continue;
            }
            if (!in_array($content->getParent(), $rootContent, true) && null !== $content->getParent() && isset($indexed[$content->getParent()->getUuid()])) {
                $parentHits[] = $hit;

                continue;
            }
            if (null === $parent || (null !== $content->getParent() && $parent->getId() === $content->getParent()->getUuid())) {
                $menuItems[$content->getUuid()] = new MenuItem(
                    $content->getUuid(),
                    $content->getTitle(),
                    $this->router->generate($content->getUuid()),
                    $parent
                );
            }
        }
        foreach ($menuItems as $item) {
            $this->buildMenuStructure($parentHits, $item);
        }
        if (null !== $parent) {
            $parent->setChildren(array_values($menuItems));
        }

        return array_values($menuItems);
    }
}
