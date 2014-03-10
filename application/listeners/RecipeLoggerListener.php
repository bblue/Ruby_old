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
        $mailService = $this->serviceFactory->build('mailer');
        
        $mailService->mail->Subject  = 'Testsubject';
        $mailService->mail->Body     = 'Dette er en test';
        
        $mailService->mail->addAddress('aleksander.lanes@gmail.com', 'Aleksander Lanes');
        
        //$mailService->send();
    }
    
    public function onDeleteRecipe()
    {
        
    }
}