<?php

namespace Listeners;

use App\Event as Event;

final class RecipeMailer extends AbstractListener
{
    public function onAddRecipe(Event $event)
    {
    	$mailService = $this->serviceFactory->build('mailer');

    	$recognition = $this->serviceFactory->build('recognition');
		$author = $recognition->getUser($event->recipe->author_id);

        $body  = "{USER}, \n";
        $body .= "\n";
		$body .= 'Brukeren '.$author->Username.' har lagt til en ny oppskrift i databasen p� '.$_SERVER['SERVER_NAME']."\n";
		$body .= "\n";
		$body .= "Oppskriften er tilgjengelig p� lenken under:\n";
		$body .= $_SERVER['SERVER_NAME'].'/recipes/view/'.$event->recipe->id."\n";
		$body .= "\n";
		$body .= "\n";
		$body .= "Med vennlig hilsen,\n";
		$body .= $_SERVER['SERVER_NAME']."\n";
		$body .= "\n";
		$body .= "\n";
		$body .= "\n";
		$body .= "-----------------\n";
		$body .= "Om du ikke �nsker � motta disse varslene trykk p� lenken under:\n";
		$body .= $_SERVER['SERVER_NAME'].'/users/ucp/{USER_ID}';

		$mailService->mail->Body = $body;
		$mailService->mail->Subject = 'En ny oppskrift har blitt lagt til: ' . $event->recipe->title;

        $aCriterias['timestamp'][] = array(
        	'operator'	=> '>',
        	'value'		=> time() - (60*5)
        );

        $aUsers = $recognition->getUsers();

		foreach($aUsers as $user) {
			$mailService->mail->addAddress($user->email, $user->Firstname .' '. $user->Lastname);
		}

        $mailService->send();
    }

    public function onDeleteRecipe()
    {

    }

    public function onEditRecipe()
    {

    }
}