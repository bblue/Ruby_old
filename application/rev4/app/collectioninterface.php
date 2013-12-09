<?php
namespace App;

interface CollectionInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    public function toArray();

    public function clear();

    public function reset();

    public function add($key, AbstractEntity $entity);
   
    public function get($key);

    public function remove($key);

    public function exists($key);
}