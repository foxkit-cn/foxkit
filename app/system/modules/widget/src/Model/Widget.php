<?php

namespace Foxkit\Widget\Model;

use Foxkit\Database\ORM\ModelTrait;
use Foxkit\System\Model\DataModelTrait;
use Foxkit\User\Model\AccessModelTrait;

/**
 * @Entity(tableClass="@system_widget")
 */
class Widget implements \JsonSerializable
{
    use AccessModelTrait, DataModelTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column */
    public $title = '';

    /** @Column(type="string") */
    public $type;

    /** @Column(type="integer") */
    public $status = 1;

    /** @Column(type="simple_array") */
    public $nodes = [];
}
