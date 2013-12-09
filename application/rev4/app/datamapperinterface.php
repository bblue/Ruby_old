<?php
namespace App;

interface DataMapperInterface
{
	public function fetch(AbstractEntity $entity);
	
    public function findById($id, $entity = null);

    public function findAll();

    public function find(array $aCriterias, $entity);

    public function insert(AbstractEntity $entity);

    public function update(AbstractEntity $entity);

    public function delete(AbstractEntity $entity);
}