<?php
namespace Model\Domain\Recipe;
use App\AbstractEntity;
use App\CollectionProxy;
use Model\Domain\User\User;

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
		'iRecipeID',
		'method',
		'ingredients',
		'portions',
		'relevance'
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
		if(!$author instanceof CollectionProxy && !$author instanceof User) {
			throw new \Exception('Author object is of wrong class');
		}
		$this->_values['author'] = $author;
		return $this;
	}

	public function getAuthor()
	{
		if(is_object($this->_values['author'])) {
			return ($this->_values['author'] instanceof User) ? $this->_values['author'] : $this->_values['author']->getEntity($this->_values['author_id']);
		} else {
			throw new \Exception('Author is not set for authorID=' . $this->author_id);
		}
	}

	public function setMethod($sMethod)
	{
		require_once (ROOT_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'HTMLPurifier'.DIRECTORY_SEPARATOR.'HTMLPurifier'.DIRECTORY_SEPARATOR.'Bootstrap.php');
		require_once (ROOT_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'HTMLPurifier'.DIRECTORY_SEPARATOR.'HTMLPurifier.autoload.php');

		$config = \HTMLPurifier_Config::createDefault();
		$config->set('HTML.TargetBlank', true);
		$config->set('HTML.Allowed', 'p,strong,em,s,ol,li,ul,blockquote,table,tbody,tr,td,a[href],i,iframe[width|height|src|frameborder]');
		$config->set('HTML.SafeIframe', true);
		$config->set('URI.SafeIframeRegexp','%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/)%'); //allow YouTube
		$purifier = new \HTMLPurifier($config);

		$this->_values['method'] = $purifier->purify($sMethod);
		return $this;
	}
}
