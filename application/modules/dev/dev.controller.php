<?php
namespace Modules;

use App\AbstractController;

final class DevController extends AbstractController
{
	protected function executeIndexaction()
	{

	}

	public function getAppropriateHashCost()
	{
		require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'password_compat.php';
		$timeTarget = 0.2;
		$cost = 9;
		do {
			$cost++;
			$start = microtime(true);
			password_hash("test", PASSWORD_DEFAULT, array('cost' => $cost));
			$end = microtime(true);
		} while (($end - $start) < $timeTarget);

		echo "Appropriate Cost Found: " . $cost . "\n";
	}
}