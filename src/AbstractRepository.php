<?php
 
 namespace Yoop;
  
 abstract class AbstractRepository
 {
    protected $entity; 

    protected function getEntityManager($classe)
    {
        $this->entity = $classe;
        return DatabaseMySql::getDb();
    }
}
 