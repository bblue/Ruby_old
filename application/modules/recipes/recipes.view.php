<?php
namespace Modules;

use App\AbstractView,
	App\Template;

use Model\Domain\Recipe\Recipe as RecipeEntity;
use Model\Domain\Recipe\Collection as RecipeCollection;

final class RecipesView extends AbstractView
{
	public function executeManagemyrecipes($recipes)
	{
		// Ensure we can handle response
		if(!$recipes instanceof RecipeEntity && !$recipes instanceof RecipeCollection) {
			throw new \Exception('Controller response can not be handled by this view');
		}

		$sTemplateFile = 'managemyrecipes';

		/** Load recipe list into template variables */
		$this->presentationObjectFactory
		->build('recipes', true)
		->assignData($recipes, $this->serviceFactory->build('recipe', true)->getCount());

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

	public function executeView($recipe)
	{
		// Ensure we can handle response
		if(!$recipe instanceof RecipeEntity) {
			throw new \Exception('Controller response can not be handled by this view');
		}

		if(!isset($recipe->id)) {
			die('no recipe selected');
		}

	    $sTemplateFile = 'recipes/view';

	    /** Load recipe data into template variables */
	    $this->presentationObjectFactory
	    ->build('recipe', true)
	    ->setTemplatePrefix('RECIPE')
	    ->assignData($recipe);

	    /** Load required scripts */
	    $this->presentationObjectFactory
	    ->build('scripttags', true)
	    ->assignData($sTemplateFile);

	    /** Load breadcrumbs */
	    $this->presentationObjectFactory
	    ->build('breadcrumbs', true)
	    ->assignData(array(
	        array('title'	=> 'Recipes'),
	    	array('url'	=> 'recipes/view/'.$recipe->id, 'title'	=> $recipe->title,)
	    ));

	    $this->display('custom/header.htm');
	    $this->display('custom/sidebar.htm');
	    $this->display('custom/rightbar.htm');
	    $this->display('custom/' . $sTemplateFile . '.htm');
	    $this->display('custom/footer.htm');
	}

	public function executeAdd($recipe)
	{
		// Ensure we can handle response
		if(!is_object($recipe) || !$recipe instanceof RecipeEntity) {
			throw new \Exception('Controller response can not be handled by this view');
		}

		if(isset($recipe->id)) {
			// Show success page
			echo 'Recipe added successfully';
		}

		// Check for errors in entity
		if($recipe->hasError()) {
			// Produce errors
			echo 'There are errors in entity';
		}

		$sTemplateFile = 'recipes/add';

		/** Load recipe data into template variables */
		$this->presentationObjectFactory
			->build('recipe', true)
			->setTemplatePrefix('RECIPE')
			->assignData($recipe);

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

	public function executeCheckTitleIsAvailable()
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