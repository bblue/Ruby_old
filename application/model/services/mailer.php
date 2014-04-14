<?php
namespace Model\Services;

use App\ServiceAbstract;

final class Mailer extends ServiceAbstract
{
    public $mail;

    private $FROM_ADDRESS 		= MAIL_FROM_ADDRESS;
    private $FROM_NAME			= MAIL_FROM_NAME;
    private $SERVER_USERNAME	= MAIL_SERVER_USERNAME;
    private $SERVER_PASSWORD	= MAIL_SERVER_PASSWORD;
    private $SERVER_ADDR_SSL	= MAIL_SERVER_ADDR_SSL;
    private $SERVER_PORT_SSL	= MAIL_SERVER_PORT_SSL;
    private $SERVER_ADDR		= MAIL_SERVER_ADDR;
    private $SERVER_PORT		= MAIL_SERVER_PORT;

    public function __construct()
    {
        require(ROOT_PATH . '/lib/PHPMailer/phpmailer.php');
        $this->mail = new \PhpMailer();

        $this->loadDefaultConfiguration();

        $this->configureNonSSL();

        return $this->mail;
    }

    public function send()
    {
        if(!$this->mail->send()) {
            throw new \Exception('Mailer Error: ' . $mailService->mail->ErrorInfo);
        } else {
            return true;
        }
    }

    private function loadDefaultConfiguration()
    {
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        // $this->mail->SMTPDebug = 2; //Show detailed messages

        $this->mail->Username = $this->SERVER_USERNAME;
        $this->mail->Password = $this->SERVER_PASSWORD;

        $this->CharSet = 'UTF-8';
        $this->mail->WordWrap = 900;

        $this->mail->From = $this->FROM_ADDRESS;
        $this->mail->FromName = $this->FROM_NAME;
    }

    private function configureSSL()
    {
        $this->mail->Host = $this->SERVER_ADDR_SSL;
        $this->mail->Port = $this->SERVER_PORT_SSL; // SMTP port
        $this->mail->SMTPSecure = 'ssl'; // 'ssl', 'tls' or ''
    }

    private function configureNonSSL()
    {
        $this->mail->Host = $this->SERVER_ADDR;
        $this->mail->Port = $this->SERVER_PORT; // SMTP port
    }
}