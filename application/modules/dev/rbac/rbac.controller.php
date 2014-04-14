<?php
namespace Modules\Dev;

use App\AbstractController;

class RbacController extends AbstractController
{
	protected function executeIndexaction()
	{

	}

	private function executeResetrbactofactorysettings()
	{

	}

	private function executeResetrbactorubysettings()
	{
		/**
		$this->rbac->reset(true);$this->log->createLogEntry("RBAC reset to factory settings", $this->visitor, 'success', true);

		$iPermissionRoot 	= $this->rbac->Permissions->TitleID('root');
		$iRoleRoot			= $this->rbac->Roles->TitleID('root');

		$admin_permission			= $this->rbac->Permissions->Add($t='ADMIN', 				$m='Do all recipe functions', $iPermissionRoot); 				$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);

		$recipe_permission_base		= $this->rbac->Permissions->Add($t='RECIPES', 				$m='Do all recipe functions', $admin_permission); 				$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);

		$edit_any_recipe			= $this->rbac->Permissions->Add($t='EDIT_ANY_RECIPE', 		$m='Edit any recipe', $recipe_permission_base);					$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
		$edit_published_recipes		= $this->rbac->Permissions->Add($t='EDIT_PUBLISHED_RECIPES',$m='Edit all published recipe', $edit_any_recipe);				$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
		$edit_own_recipe			= $this->rbac->Permissions->Add($t='EDIT_OWN_RECIPE', 		$m='Edit own recipe', $edit_published_recipes);					$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);

		$view_any_recipe 			= $this->rbac->Permissions->Add($t='VIEW_DRAFT_RECIPES', 	$m='Also view draft recipes', $recipe_permission_base);			$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
		$view_published_recipes		= $this->rbac->Permissions->Add($t='VIEW_PUBLISHED_RECIPES',$m='View any recipe that is published', $view_any_recipe);		$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
		$view_own_recipes 			= $this->rbac->Permissions->Add($t='VIEW_OWN_RECIPES', 		$m='View own recipe', $view_published_recipes);					$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);

		$dev_area_permission_base 	= $this->rbac->Permissions->Add('DEV_AREA', 'Do all actions in the dev area', $admin_permission);
		$view_dev_area 				= $this->rbac->Permissions->Add('VIEW_DEV_AREA', 'View the development area', $dev_area_permission_base);

		$rbac 						= $this->rbac->Permissions->Add('RBAC', 'Parent permission for all rbac permissions', $dev_area_permission_base);
		$reset_rbac_factory 		= $this->rbac->Permissions->Add('RESET_RBAC_TO_FACTORY_SETTINGS', 'Permission to reset the rbac', $rbac);
		$reset_rback_ruby 			= $this->rbac->Permissions->Add('RESET_RBAC_TO_RUBY_SETTINGS', 'Permission to clean the rbac', $reset_rbac_factory);

		$dash_permission_base 		= $this->rbac->Permissions->Add('DASHBOARD', 'Do all actions on the dashboard', $admin_permission);
		$view_dash 					= $this->rbac->Permissions->Add('VIEW_DASHBOARD', 'View the dashboard', $dash_permission_base);

		$admin 						= $this->rbac->Roles->Add($t='ADMIN', 						$m='The top tier admin', $iRoleRoot);							$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
		$recipe_admin 				= $this->rbac->Roles->Add($t='RECIPE_ADMIN', 				$m='The recipe administrator', $admin);							$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
		$recipe_moderator 			= $this->rbac->Roles->Add($t='RECIPE_MODERATOR',	 		$m='The role for recipe moderators', $recipe_admin);			$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
		$recipe_contributor 		= $this->rbac->Roles->Add($t='RECIPE_CONTRIBUTOR',			$m='The role for recipe providers', $recipe_moderator);			$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
		$recipe_viewer 				= $this->rbac->Roles->Add($t='RECIPE_VIEWER', 				$m='The role for Guests', $recipe_contributor);					$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);

		$dash_admin 				= $this->rbac->Roles->Add('DASHBOARD_ADMIN', 'Administrator of the dashboard', $admin);
		$dash_viewer 				= $this->rbac->Roles->Add('DASHBOARD_VIEWER', 'Viewer role for dashboard', $dash_admin);

		$dev_area_admin 			= $this->rbac->Roles->Add('DEV_AREA_ADMIN', 'Administrator of the development area', $admin);
		$dev_area_viewer 			= $this->rbac->Roles->Add('DEV_AREA_VIEWER', 'Viewer role for development area', $dev_area_admin);

		$this->rbac->Roles->Assign($r=$recipe_admin, 		$p=$recipe_permission_base);		$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$recipe_moderator, 	$p=$edit_published_recipes);		$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$recipe_contributor,	$p=$view_own_recipes);				$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$recipe_contributor,	$p=$edit_own_recipe);				$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$recipe_viewer, 		$p=$view_published_recipes);		$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$dash_admin, 			$p=$dash_permission_base);		$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
		$this->rbac->Roles->Assign($r=$dev_area_admin, 		$p=$dev_area_permission_base);		$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);

		$this->rbac->Users->Assign($r=$recipe_viewer, $id=0);	$this->log->createLogEntry("User_id $id assigned to: $r", $this->visitor, 'success', true);
		$this->rbac->Users->Assign($r=$admin, $id=1);$this->log->createLogEntry("User_id $id assigned to: $r", $this->visitor, 'success', true);

		return true;
		*/
	}
}