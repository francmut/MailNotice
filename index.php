<?php

require 'vendor/autoload.php';
require 'vendor/africastalking/AfricasTalkingGateway.php';

// Africas Talking Params
$username = '';
$apikey = '';

// Mailbox Params
$imap_server = 'imap-mail.outlook.com';
$imap_port = '993';
$folder = 'inbox';
$connection_string = '{'.$imap_server.':'.$imap_port.'/imap/ssl}'.$folder;
$email = '';
$password = '';
$filter = 'UNSEEN';

// Phone Numbers e.g. +254722001001
$recepients = '';

// Text Message
$message = "Mailnotice \nFound a message";

// Init AT's API
$gateway = new AfricasTalkingGateway($username, $apikey);

// Connect to Mailbox
$mailbox = new PhpImap\Mailbox($connection_string, $email, $password, __DIR__);

// Read all messaged into an array:
$mailsIds = $mailbox->searchMailbox($filter);
if(!$mailsIds) {
    die("\n Mailbox is empty \n");
}

echo "\n";

echo 'Found ' . count($mailsIds) . ' email(s)' . "\n";
echo '----------------------------------------------------' . "\n";

$mails = array_reverse($mailsIds);

for ($i=0; $i<count($mails); $i++) {

	if ($i <= 10) {
		
		$mail = $mailbox->getMail($mails[$i], True);

		echo 'Subject: ' . $mail->subject . "\n";
		echo 'From: ' . $mail->fromAddress . "\n";
		echo 'Date: ' . $mail->date . "\n";
		echo 'Mail_id: ' . $mail->id . "\n";
		echo '----------------------------------------------------' . "\n";

		try {
			
			// send SMS
			$results = $gateway->sendMessage($recepients, $message);

		} catch (AfricasTalkingGatewayException $e) {

			echo $e->getMessage();

		}
		
	} else {
		break;
	}

}


/*
 * Cron Setup - Run every 10 minutes
 */
/*
cron -e 10 * * * * php /var/www/mailnotice/index.php
*/