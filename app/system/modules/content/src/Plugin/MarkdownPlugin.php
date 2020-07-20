<?php

namespace Foxkit\Content\Plugin;

use Foxkit\Application as App;
use Foxkit\Content\Event\ContentEvent;
use Foxkit\Event\EventSubscriberInterface;

class MarkdownPlugin implements EventSubscriberInterface
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        if (!$event['markdown']) {
            return;
        }

        $content = $event->getContent();
        $content = App::markdown()->parse($content, is_array($event['markdown']) ? $event['markdown'] : []);

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'content.plugins' => ['onContentPlugins', 5]
        ];
    }
}
