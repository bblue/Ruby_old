<?php
namespace Modules;

use App\AbstractController;

final class UsersController extends AbstractController
{
	public function executeIndexAction()
	{
		return $this->load('view');
	}

	public function executeLogin()
	{
		$this->serviceFactory
			->build('recognition', true)
			->authenticate($this->request->username, $this->request->password);
		return true;
	}

	public function executeLogout()
	{
		$recognition = $this->serviceFactory->build('recognition', true);
		$recognition->logoutVisitor($recognition->getCurrentVisitor());
		return true;
	}

	public function executeView()
	{
		if(!empty($this->request->u_id)) {
			// Get user ID
		} else {
			// Get all users based on filter and search criteria

		}
		return true;
	}

	public function executeRegister()
	{
		return true;
	}

	public function executePasswordreset()
	{
		return true;
	}

	public function executeInbox()
	{
		return true;
	}

	public function executeTimeline()
	{
		return true;
	}

	public function executeAdduser()
	{
		return true;
	}
}