<?php
namespace Model\Domain\Recipe;
use Model\AbstractEntity;
use Model\CollectionProxy;

final class Recipe extends AbstractEntity
{  
    protected $_allowedFields = array('id', 'title', 'author_id', 'abstract', 'r_id', 'status', 'time_estimate', 'rating', 'updateTime', 'submitTime', 'initTime',
    'status', 'main_photo', 'source', 'source_type', 'recipe-source-input', 'iRecipeID');
   
    //@todo: fikse at det kun er allowed fields som blir lastet fra databasen (dette skaper problemer siden 'comments' vil være et av de godkjente feltene her)
    
    /**
     * Set the user's ID
     */
    public function setId($id)
    {
        if(!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999)))) {
            throw new \Exception('The specified ID is invalid.');
        }
        $this->_values['id'] = $id;
    }
   
    /**
     * Set the title
     */ 
    public function setTitle($title)
    {
        if (strlen($title) < 2) {
            throw new \Exception('The specified title is invalid.');
        }
        $this->_values['title'] = $title;
    }
}