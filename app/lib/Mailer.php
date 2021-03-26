<?php

namespace app\lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected $instance;
    protected $errors = [];

    public function __construct()
    {

        //Instantiation and passing `true` enables exceptions
        $this->instance = new PHPMailer(true);
        $config = require_once ROOT.'/config/mail.php';

        try {
            //Server settings
            $this->instance->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->instance->isSMTP();
            $this->instance->Host       = $config['host'];
            $this->instance->SMTPAuth   = true;
            $this->instance->Username   = $config['user'];
            $this->instance->Password   = $config['pwd'];
            $this->instance->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->instance->Port       = $config['port'];
        } catch (Exception $e) {
            $this->errors[] = (PRODUCTION === false) ? $e->getMessage() : 'could not be sent';
        }
    }

    /**
     * Get the errors
     * @return array An array with the errors. If there are non, an empty array is returned
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Send an email
     * @param array $recipient An indexed array containing the email address and the username of the recipient
     * @param string $subject The subject
     * @param string $body The HTML message as string
     * @param string $altBody The alternative (non HTML) message
     * @param array $from [optional] An indexed array containing the email address and name of the sender
     * @param array $replyTo [optional] An indexed array containing the email address and name to which to reply
     * @return bool TRUE on success, FALSE on failure
     */
    public function send(array $recipient, string $subject, string $body, string $altBody, array $from=
    ['messageboard@test.com', 'reinimax\' Messageboard'], array $replyTo=['messageboard@test.com', 'reinimax\' Messageboard'])
    {
        try {
            $this->instance->setFrom($from[0], $from[1]);
            $this->instance->addAddress($recipient[0], $recipient[1]);
            $this->instance->addReplyTo($replyTo[0], $replyTo[1]);

            //Content
            $this->instance->isHTML(true);
            $this->instance->Subject = $subject;
            $this->instance->Body    = $body;
            $this->instance->AltBody = $altBody;

            $this->instance->send();
            return true;
        } catch (Exception $e) {
            $this->errors[] = (PRODUCTION === false) ? $this->instance->ErrorInfo : 'could not be sent';
            return false;
        }
    }
}
