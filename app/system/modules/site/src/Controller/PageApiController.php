<?php

namespace Foxkit\Site\Controller;

use Foxkit\Application as App;
use Foxkit\Site\Model\Page;

/**
 * @Access("site: manage site")
 */
class PageApiController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(Page::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Page::find($id);
    }
}
