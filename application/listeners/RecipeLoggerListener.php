<?php

namespace Listeners;

use App\Event as Event;

final class RecipeLoggerListener extends AbstractListener
{
    public function onAddRecipe(Event $event)
    {
        //$recipeService = $this->serviceFactory->build('recipe');
        //$recipe = $event->recipe;
        //$eventHandler = $this->serviceFactory->build('eventHandler', true);
    }
    
    public function onDeleteRecipe()
    {
        
    }
}