<?php
namespace Model\Domain\Recipe;
use App\AbstractEntity;
use App\CollectionProxy;

final class Recipe extends AbstractEntity
{
	protected $_allowedFields = array(
		'id',
		'title',
		'author',
		'author_id',
		'abstract',
		'r_id',
		'status',
		'time_estimate',
		'rating',
		'updateTime',
		'submitTime',
		'initTime',
		'status',
		'main_photo',
		'source',
		'source_type',
		'recipe-source-input',
		'iRecipeID'
   	);

	/**
	 * Set the title
	 */
	public function setTitle($title)
	{
		if (strlen($title) < 2) {
			$this->setError('The specified title is invalid');
			$this->_values['title'] = 'sanitized title';
		} else {
			$this->_values['title'] = $title;
		}
		return $this;
	}

	public function setAuthor($author)
	{
		if(!$author instanceof CollectionProxy || !$author instanceof User) {
			throw new \Exception('Author object is of wrong class');
		}
		$this->_values['author'] = $author;
		return $this;
	}

	public function getAuthor()
	{
		if(is_object($this->_values['author'])) {
			return ($this->_values['author'] instanceof User) ? $this->_values['author'] : $this->_values['author']->getEntity();
		} else {
			throw new \Exception('Author is not set for authorID=' . $this->author_id);
		}
	}
}