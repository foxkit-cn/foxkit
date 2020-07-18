<?php

namespace Foxkit\Site\Model;

use Foxkit\Database\ORM\ModelTrait;
use Foxkit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@system_page")
 */
class Page implements \JsonSerializable
{
    use DataModelTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column */
    public $content = '';
}
