<?php
namespace App\Factories;

use App\Factory;

final class Listener extends Factory
{
    private $serviceFactory;

    public function __construct(Service $serviceFactory)
    {
        $this->serviceFactory= $serviceFactory;
    }

    protected function construct($sListener)
    {
        $sListener = 'listeners\\' . $sListener;
        return ((!class_exists($sListener)) ? false : new $sListener($this->serviceFactory));
    }
}