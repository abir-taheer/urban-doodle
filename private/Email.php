<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $src_dir = app_root."/composer"; //start by assuming that we are in the root directory of the app
    public $to= array();
    public $bcc = array();
    public $cc = array();
    public $subject = "";
    public $body = "";

    /**
     * Email constructor.
     * Locates the relative path to the directory containing the composer files from the file that the class was instantiated and stores path
     */
    public function __construct(){

    }

    /**
     * Sends the email.
     * The public variables $body, $subject, and any of $to, $cc, $bcc must be set
     * If email is successfully sent, returns true
     * Returns false otherwise
     * @return bool
     */
    public function send() : bool {
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/Exception.php";
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/PHPMailer.php";
        require $this->src_dir."/vendor/phpmailer/phpmailer/src/SMTP.php";
        require $this->src_dir."/vendor/autoload.php";
        $client_email = new PHPMailer(true);
        try {
            $client_email->isSMTP();
            $client_email->Host = smtp_host;
            $client_email->SMTPAuth = smtp_auth;
            $client_email->Username = smtp_username;
            $client_email->Password = smtp_password;
            $client_email->SMTPSecure = smtp_secure_method;
            $client_email->Port = smtp_port;

            //Recipients
            $client_email->setFrom(smtp_from_email, smtp_from_name);

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