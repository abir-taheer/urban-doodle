<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $src_dir = __dir__;
    public $to= array();
    public $bcc = array();
    public $cc = array();
    public $subject = "";
    public $body = "";

    public function __construct(){
        //locate where the composer directory is relative to where the class was initiated
        $curr_dir = explode("/",__DIR__);
        while( count($curr_dir) > 0 && ! file_exists("/".implode("/", $curr_dir)."/private/composer/") ){
            unset($curr_dir[count($curr_dir) - 1]);
        }
        if( ! file_exists("/".implode("/", $curr_dir)."/private/composer") ){
            echo "Exception: COULD NOT LOCATE 'composer' DIRECTORY.\n";
            exit;
        }
        $this->src_dir = "/".implode("/", $curr_dir)."/private/composer";
    }

    public function send() {
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/Exception.php";
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/PHPMailer.php";
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/SMTP.php";
        require $this->src_dir."/vendor/autoload.php";
        $client_email = new PHPMailer(true);
        try {
            $email_config = Config::getConfig()['smtp_config'];
            $client_email->isSMTP();
            $client_email->Host = $email_config['host'];
            $client_email->SMTPAuth = $email_config['smtp_auth'];
            $client_email->Username = $email_config['username'];
            $client_email->Password = $email_config['password'];
            if($email_config['secure_method'] !== "none"){
                $client_email->SMTPSecure = $email_config['secure_method'];
            }
            $client_email->Port = $email_config['port'];

            //Recipients
            $client_email->setFrom($email_config['from_email'], $email_config['from_name']);

            foreach( $this->to as $email ){
                $client_email->addAddress($email);
            }

            foreach( $this->cc as $email ){
                $client_email->addCC($email);
            }

            foreach( $this->bcc as $email ){
                $client_email->addBcc($email);
            }

            $client_email->isHTML(true);
            $client_email->Subject = $this->subject;
            $client_email->Body    =  $this->body;
            $client_email->AltBody = 'Please use an email client capable of displaying html email.';

            $client_email->send();
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

}