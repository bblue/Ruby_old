<?php
namespace App;

use App\Factories\Listener as ListenerFactory;

final class ListenerProxy
{
	private $listenerFactory;
	private $sListener;

	function __construct(ListenerFactory $listenerFactory, $sListener)
	{
		$this->listenerFactory = $listenerFactory;
		$this->sListener = $sListener;
	}

	function __call($method, $args)
	{
		return $this->listenerFactory->createObject($this->sListener)->$method($args[0]);
	}
}