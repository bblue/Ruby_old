<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use App\AbstractEntity;

final class EntityErrors extends AbstractPresentationObject
{
	public function assignData(AbstractEntity $entity)
	{
		if(!$entity->hasError()) {
			return null;
		}

		foreach($entity->getErrors() as $error) {
			$this->template->assign_block_vars('entity_errors', array(
				'TEXT'		=> $error
			));

		}

		$this->template->set_filenames(array('entityErrors' => 'blocks/entityErrors.htm'));
		$this->template->assign_display('entityErrors', $this->getTemplatePrefix().'ENTITY_ERRORS', true);
	}
}