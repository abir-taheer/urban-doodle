<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $src_dir = "composer"; //start by assuming that we are in the root directory of the app
    public $to= array();
    public $bcc = array();
    public $cc = array();
    public $subject = "";
    public $body = "";

    public function __construct(){
        //count how many directory levels deep we are in right now
        $dir_count = count(explode(DIRECTORY_SEPARATOR, __dir__));
        $x = 0;

        //keep trying to go into a higher directory to look for the file and stop at highest directory
        while( ! file_exists($this->src_dir) and $x < $dir_count ){
            $this->src_dir = "../".$this->src_dir;
            $x++;
        }

        //if after the searching the file still could not be found, echo the error and exit script execution
        if( ! file_exists($this->src_dir) ){
            echo "Exception: COULD NOT LOCATE 'composer' DIRECTORY.\n";
            exit;
        }
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