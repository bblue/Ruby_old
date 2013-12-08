<?php
namespace Model\Domain\User;
use Model\EntityCollection;

final class Collection extends EntityCollection
{
    protected $_entityClass = 'User';
}  