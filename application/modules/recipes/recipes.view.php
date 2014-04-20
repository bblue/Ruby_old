<?php
namespace Modules;

use App\AbstractView,
	App\Template;

final class RecipesView extends AbstractView
{
	public function executeManagemyrecipes()
	{
		$sTemplateFile = 'managemyrecipes';

		/** Load required scripts */
		$this->presentationObjectFactory
		->build('scripttags', true)
		->assignData($sTemplateFile);

		/** Load breadcrumbs */
		$this->presentationObjectFactory
		->build('breadcrumbs', true)
		->assignData(array(
		    array(
		        'title'	=> 'Recipes',
		    ),
		    array(
		        'url'	=> 'recipes/managemyrecipes',
		        'title'	=> 'Manage recipes',
		    )
		));
		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/recipes/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');

		return true;
	}

	public function executeView()
	{
	    $sTemplateFile = 'recipes/view';

	    /** Load required scripts */
	    $this->presentationObjectFactory
	    ->build('scripttags', true)
	    ->assignData($sTemplateFile);

	    /** Load breadcrumbs */
	    $this->presentationObjectFactory
	    ->build('breadcrumbs', true)
	    ->assignData(array(
	        array(
	            'title'	=> 'Recipes',
	        ),
	        array(
	            'url'	=> 'recipes/view',
	            'title'	=> 'View Recipe',
	        )
	    ));

	    $this->display('custom/header.htm');
	    $this->display('custom/sidebar.htm');
	    $this->display('custom/rightbar.htm');
	    $this->display('custom/' . $sTemplateFile . '.htm');
	    $this->display('custom/footer.htm');
	}

	public function executeAdd()
	{
		if(!is_object($this->mControllerResponse) || $this->mControllerResponse instanceof Model\Domain\Recipe\Recipe) {
			throw new \Exception('Controller response can not be handled by this view');
		}

		$recipe = $this->mControllerResponse;

		if(isset($recipe->id)) {
			echo 'Recipe added successfully';
		}

		$sTemplateFile = 'recipes/add';

		/** Load required scripts */
		$this->presentationObjectFactory
			->build('scripttags', true)
			->assignData($sTemplateFile);

		/** Load breadcrumbs */
		$this->presentationObjectFactory
			->build('breadcrumbs', true)
			->assignData(array(
				array(
					'title'	=> 'Recipes',
				),
				array(
					'url'	=> 'recipes/add',
					'title'	=> 'Add New Recipe',
				)
			));

		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');
	}

	public function executeValidate()
	{
		header('Content-type: application/json');
		echo json_encode(true);
	}

	protected function executeGetcategories()
	{
		header('Content-type: application/json');
		echo json_encode(array('red', 'green', 'blue', 'yellow'));
	}
}