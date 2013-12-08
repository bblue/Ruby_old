<?php
namespace Lib\Boot\Db;
interface DatabaseAdapterInterface
{
    function connect();
   
    function disconnect(); 
   
    function query($query);
   
    function fetch(); 
   
    function select(array $aTables, $conditions, $fields, $order, $limit, $offset);
   
    function insert(array $aTables, array $data);
   
    function update(array $aTables, array $data, $conditions);
   
    function delete(array $aTables, $conditions);
   
    function getInsertId();
   
    function countRows();
   
    function getAffectedRows();
}