<?php

namespace Rabble\MenuBundle\EventListener;

use Rabble\ContentBundle\Event\DefaultFieldTypesEvent;
use Rabble\FieldTypeBundle\FieldType\ChoiceType;
use Rabble\MenuBundle\RabbleMenu\MenuManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultFieldTypesSubscriber implements EventSubscriberInterface
{
    private MenuManagerInterface $menuManager;
    private TranslatorInterface $translator;

    public function __construct(MenuManagerInterface $menuManager, TranslatorInterface $translator)
    {
        $this->menuManager = $menuManager;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DefaultFieldTypesEvent::class => 'registerDefaultFields',
        ];
    }

    public function registerDefaultFields(DefaultFieldTypesEvent $event): void
    {
        $choices = [];
        foreach ($this->menuManager->getMenus() as $menu) {
            if ($menu->hasAttribute('label_'.$this->translator->getLocale())) {
                $label = $menu->getAttribute('label_'.$this->translator->getLocale());
            } elseif ($menu->hasAttribute('translation_domain')) {
                $label = $this->translator->trans('menu.'.$menu->getName(), [], $menu->getAttribute('translation_domain'));
            } else {
                $label = $menu->getName();
            }
            $choices[$label] = $menu->getName();
        }
        $event->addFieldType(new ChoiceType([
            'name' => 'menu',
            'label' => 'content.menu',
            'choices' => $choices,
            'component' => 'menu',
            'required' => false,
            'translatable' => true,
            'translation_domain' => 'RabbleMenuBundle',
            'choice_translation_domain' => false,
        ]));
    }
}
