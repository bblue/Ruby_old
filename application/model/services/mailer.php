<?php
namespace Model\Services;

use App\ServiceAbstract;

final class Mailer extends ServiceAbstract
{
    public $mail; 
    
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
        
        $this->mail->Username = 'webmaster@intensjon.no';
        $this->mail->Password = '1afroide2';
        
        $this->CharSet = 'UTF-8';
        $this->mail->WordWrap = 900;
        
        $this->mail->From = 'webmaster@intensjon.no';
        $this->mail->FromName = 'beta.intensjon.no';
    }
    
    private function configureSSL()
    {
        $this->mail->Host = 'v1.golarge.net';
        $this->mail->Port = 465; // SMTP port
        $this->mail->SMTPSecure = 'ssl'; // 'ssl', 'tls' or ''
    }
    
    private function configureNonSSL()
    {
        $this->mail->Host = 'mail.intensjon.no';
        $this->mail->Port = 26; // SMTP port
    }
}