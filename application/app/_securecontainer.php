<?php
namespace App;
final class SecureContainer
{
    protected $target = null;
    protected $acl = null;

    public function __construct($target, AccessControlList $acl)
    {
        $this->target = $target;
        $this->acl = $acl;
    }

	public function __call($method, $arguments)
	{
		if(!method_exists($this->target, $method))
		{
			throw new \Exception('Command not recognized');
		}
		
		if(!$this->acl->isAllowed(get_class($this->target), $method))
		{
			throw new \Exception('Command not permitted');
        }
        return call_user_func_array(array($this->target, $method), $arguments);
    }
}