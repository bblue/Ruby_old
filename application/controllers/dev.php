<?php
namespace Controllers;

use App\AbstractController;

final class Dev extends AbstractController
{	
	protected function executeIndexaction()
	{
		if($this->rbac->Check('VIEW_DEV_AREA', $this->visitor->user_id)) {
			return true;
		} else {
			return $this->load('set403error');
		}
	}

	protected function executeResetrbactofactorysettings()
	{
		if($this->rbac->Check('RESET_RBAC_TO_FACTORY_SETTINGS', $this->visitor->user_id)) {
			if($this->rbac->reset(true)) {
				$this->log->createLogEntry("RBAC reset to factory settings", $this->visitor, 'success', true);
				return true;	
			}
		} else {
			return $this->load('set403error');
		}		
	}
	
	protected function executeResetrbactorubysettings()
	{
		if($this->rbac->Check('RESET_RBAC_TO_RUBY_SETTINGS', $this->visitor->user_id)) {
			

			$this->rbac->reset(true);$this->log->createLogEntry("RBAC reset to factory settings", $this->visitor, 'success', true);
			
			$recipe_permission_base	= $this->rbac->Permissions->Add($t='RECIPES', 				$m='Do all recipe functions'); $this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			
			$edit_any_recipe		= $this->rbac->Permissions->Add($t='EDIT_ANY_RECIPE', 		$m='Edit any recipe', $recipe_permission_base); $this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			$edit_published_recipes	= $this->rbac->Permissions->Add($t='EDIT_PUBLISHED_RECIPES', 	$m='Edit all published recipe', $edit_published_recipes);$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			$edit_own_recipe		= $this->rbac->Permissions->Add($t='EDIT_OWN_RECIPE', 		$m='Edit own recipe', $edit_published_recipes);$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			
			$view_any_recipe 		= $this->rbac->Permissions->Add($t='VIEW_DRAFT_RECIPES', 		$m='Also view draft recipes', $recipe_permission_base);$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			$view_published_recipes	= $this->rbac->Permissions->Add($t='VIEW_PUBLISHED_RECIPES', 	$m='View any recipe that is published', $view_any_recipe);$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			$view_own_recipes 		= $this->rbac->Permissions->Add($t='VIEW_OWN_RECIPES', 		$m='View own recipe', $view_published_recipes);$this->log->createLogEntry("Permission $t added: $m", $this->visitor, 'success', true);
			
			$admin 				= $this->rbac->Roles->Add($t='ADMIN', 				$m='The top tier admin', $this->rbac->Roles->TitleID('root'));$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
			$recipe_admin 		= $this->rbac->Roles->Add($t='RECIPE_ADMIN', 			$m='The recipe administrator', $admin);$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
			$recipe_moderator 	= $this->rbac->Roles->Add($t='RECIPE_MODERATOR', 		$m='The role for recipe moderators', $recipe_admin);$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
			$recipe_contributor = $this->rbac->Roles->Add($t='RECIPE_CONTRIBUTOR',	$m='The role for recipe providers', $recipe_moderator);$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
			$recipe_viewer 		= $this->rbac->Roles->Add($t='RECIPE_VIEWER', 		$m='The role for Guests', $recipe_contributor);$this->log->createLogEntry("Role $t added: $m", $this->visitor, 'success', true);
			
			$this->rbac->Roles->Assign($r=$recipe_admin, 			$p=$recipe_permission_base); $this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
			$this->rbac->Roles->Assign($r=$recipe_moderator, 		$p=$edit_published_recipes);$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
			$this->rbac->Roles->Assign($r=$recipe_contributor,	$p=$view_own_recipes);$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
			$this->rbac->Roles->Assign($r=$recipe_contributor,	$p=$edit_own_recipe);$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
			$this->rbac->Roles->Assign($r=$recipe_viewer, 		$p=$view_published_recipes);$this->log->createLogEntry("Role $r assigned to: $p", $this->visitor, 'success', true);
	
			$this->rbac->Users->Assign($r=$recipe_viewer, $id=0);	$this->log->createLogEntry("User_id $id assigned to: $r", $this->visitor, 'success', true);
			$this->rbac->Users->Assign($r=$this->rbac->Roles->TitleID('ADMIN'), $id=1);$this->log->createLogEntry("User_id $id assigned to: $r", $this->visitor, 'success', true);

			$dash_permission_base = $this->rbac->Permissions->Add('DASHBOARD', 'Do all actions on the dashboard');
			$view_dash = $this->rbac->Permissions->Add('VIEW_DASHBOARD', 'View the dashboard', $dash_permission_base);
			
			$dash_admin = $this->rbac->Roles->Add('DASHBOARD_ADMIN', 'Administrator of the dashboard', $this->rbac->Roles->TitleID('ADMIN'));	
			$dash_viewer = $this->rbac->Roles->Add('DASHBOARD_VIEWER', 'Viewer role for dashboard', $dash_admin);

			$dev_area_permission_base = $this->rbac->Permissions->Add('DEV_AREA', 'Do all actions in the dev area');
			$view_dev_area = $this->rbac->Permissions->Add('VIEW_DEV_AREA', 'View the development area', $dev_area_permission_base);
			
			$rbac = $this->rbac->Permissions->Add('RBAC', 'Parent permission for all rbac permissions', $dev_area_permission_base);
			$reset_rbac_factory = $this->rbac->Permissions->Add('RESET_RBAC_TO_FACTORY_SETTINGS', 'Permission to reset the rbac', $rbac);
			$reset_rback_ruby = $this->rbac->Permissions->Add('RESET_RBAC_TO_RUBY_SETTINGS', 'Permission to clean the rbac', $reset_rbac_factory);
			
			$dev_area_admin = $this->rbac->Roles->Add('DEV_AREA_ADMIN', 'Administrator of the development area', $this->rbac->Roles->TitleID('ADMIN'));	
			$dev_area_viewer = $this->rbac->Roles->Add('DEV_AREA_VIEWER', 'Viewer role for development area', $dev_area_admin);

			return true;
		} else {
			return $this->load('set403error');
		}
	}
	
	
	protected function executeAddrbacroles()
	{
		if($this->rbac->Check('DEV_AREA', $this->visitor->user_id)) {
			$dev = $this->serviceFactory->build('dev');
			
			$array[] = array(
				'title'			=> 'Guest',
				'description'	=> 'This is the guest role',
				'parent_id'		=> null
			);
			
			$visitor = $this->serviceFactory->build('recognition', true)->getCurrentVisitor();
			
			return $dev->addRbacRoles($array);
	
		} else {
	    	return $this->load('set403error');
		}
	}
	
	protected function executeAddrbacpermissions()
	{
		if($this->rbac->Check('DEV_AREA', $this->visitor->user_id)) {
			$dev = $this->serviceFactory->build('dev');
			
			$array[] = array(
				'title'			=> 'Permission',
				'description'	=> 'This is a permission',
				'parent_id'		=> $this->request->parent_id
			);
			
			$dev->addRbacPermissions($array);
			
			return true;			
		} else {
	    	return $this->load('set403error');
		}
	}
}