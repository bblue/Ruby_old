<?php
namespace Modules;

use App\AbstractView,
	App\Template;

use Model\Domain\Recipe\Recipe as RecipeEntity;
use Model\Domain\Recipe\Collection as RecipeCollection;
use App\Exceptions\UnexpectedValueException;

final class RecipesView extends AbstractView
{
	public function executeManagemyrecipes($search)
	{
		$sTemplateFile = 'managemyrecipes';

		/** Load recipe list into template variables */
		$this->presentationObjectFactory
		->build('recipes', true)
		->assignData($search->getResult());

		/** Load search data into template */
		$this->presentationObjectFactory
		->build('search', true)
		->assignData($search);

		/** Load required scripts */
		$this->presentationObjectFactory
		->build('scripttags', true)
		->assignData($sTemplateFile);

	    /** Load breadcrumbs */
	    $this->presentationObjectFactory
	    ->build('breadcrumbs', true)
	    ->assignData(array(
	        array('title'=> 'Recipes'),
	    	array('url'	=> 'recipes/managemyrecipes/', 'title'=>'Manage')
	    ));

		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/recipes/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');

		return true;
	}

	public function executeImageUpload()
	{
		return true;
	}

	public function executeSearch($search)
	{
		$sTemplateFile = 'search';

		/** Load recipe list into template variables */
		$this->presentationObjectFactory
		->build('recipes', true)
		->assignData($search->getResult());

		/** Load search data into template */
		$this->presentationObjectFactory
		->build('search', true)
		->assignData($search);

		/** Load breadcrumbs */
		$this->presentationObjectFactory
		->build('breadcrumbs', true)
		->assignData(array(
			array('title'=> 'Recipes'),
			array('url'	=> 'recipes/search/', 'title'=>'Search')
		));

		/** Load required scripts */
		$this->presentationObjectFactory
		->build('scripttags', true)
		->assignData($sTemplateFile);

		$this->display('custom/header.htm');
		$this->display('custom/sidebar.htm');
		$this->display('custom/rightbar.htm');
		$this->display('custom/recipes/' . $sTemplateFile . '.htm');
		$this->display('custom/footer.htm');

		return true;
	}

	public function executeView($recipe)
	{
		if($recipe === null) {
			return $this->redirect('/recipes/managemyrecipes');
		}

		// Ensure we can handle response
		if(!$recipe instanceof RecipeEntity) {
			throw new UnexpectedValueException('Controller response can not be handled by this view', $recipe);
		}

		if(!isset($recipe->id)) {
			return $this->load('Set404error');
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
		if(!$recipe instanceof RecipeEntity) {
			throw new UnexpectedValueException('Controller response can not be handled by this view', $recipe);
		}

		if(isset($recipe->id)) {
			return $this->load('view');
		}

		$sTemplateFile = 'recipes/add';

		/** Load entity error data into template variables */
		$this->presentationObjectFactory
			->build('entityErrors', true)
			->setTemplatePrefix('RECIPE')
			->assignData($recipe);

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