<?php
/*
 * gmail_backer_upper
 * By Sean McCleary <sean@seanmccleary.info>
 * 
 * This is freeware.  Do what you will with it.  
 * I make no promises. Don't come a-cryin'.
 */
ini_set('memory_limit', '-1');

$usage = "Usage: php {$_SERVER['PHP_SELF']} [-c CONFIG_FILE] FILE(S)";

// Copy these to another variable because we'll start removing some
// as we're finished with them.
$params = $argv;

// In fact, we don't need the first one, which is the name of the executing script.
unset($params[0]);

// OK first try and fish out whether or not there was a config file specified
if( $flag_index = array_search('-c', $params) ) {
	$config_file = $params[$flag_index + 1];
	
	if( !file_exists($config_file) ) {
		fwrite(STDERR, "Uh, couldn't find {$params[2]} ... you sure it exists?\n\n"
			. "$usage\n");
		exit(1);
	}
	
	unset($params[$flag_index + 1]);
	unset($params[$flag_index]);	
}
else if( file_exists('gmail_backer_upper.ini') ) {
	$config_file = 'gmail_backer_upper.ini';
}
else {
	fwrite(STDERR, "Whoa there, you didn't specify a config file, and I couldn't find one in the current directory!\n\n"
		. "$usage\n");
	exit(1);	
}

// Make sure they've told us which files to back up
if( count($params) == 0 ) {
	fwrite(STDERR, "You gotta tell us which files to back up, here, pal! (Wildcards accepted!)\n\n"
		. "$usage\n");
	exit(1);
}

// Make sure the PEAR package for MIME emails is installed
if( !@include_once('Mail/mime.php') ) { 
	fwrite(STDERR, "Oh, my! I'm sorry, this program requires the Mail_Mime PEAR package!\n"
		. "If you're the kind of person who wields that kind of power, you\n"
		. "could install it with a simpe: sudo pear install Mail_Mime\n\n");
	exit(1);	
};

// Make sure the PEAR package for Mail is installed
if( !@include_once('Mail.php') ) { 
	fwrite(STDERR, "D'oh! This program requires the Mail PEAR package!\n"
		. "If you're the kind of person who wields that kind of power, you\n"
		. "could install it with a simpe: sudo pear install Mail\n\n");
	exit(1);	
};

// Make sure the PEAR package for SMTP is installed
if( !@include_once('Net/SMTP.php') ) { 
	fwrite(STDERR, "D'oh! This program requires the Net_SMTP PEAR package!\n"
		. "If you're the kind of person who wields that kind of power, you\n"
		. "could install it with a simpe: sudo pear install Net_SMTP\n\n");
	exit(1);	
};

// Now let's get down to the real meat of the script.

$date = date(DATE_RFC850);

print "*** PROGRAM STARTED AT $date\n";
$config = parse_ini_file($config_file);

print "Creating the email...\n";

$backup_files = array();
foreach($params as $param) {
	
	if( is_dir($param) ) { 
		$param .= '/*';
	}
	
	$backup_files = array_merge($backup_files, glob($param));
}

$body = "Your backed up files from $date\n\n";

$mime = new Mail_mime();

foreach( $backup_files as $backup_file ) {
	
	print "\t...attaching {$backup_file}...";
	
	$mime->addAttachment($backup_file);
	
	$body .= "\t{$backup_file}\n";
	
	print "done\n";
}
$mime->setTXTBody($body);
$body = $mime->get();

// OK set up our headers
$headers = array(
	'From' => $config['emails_from'],
	'To' => $config['emails_to'],
	'Subject' => sprintf($config['email_title'], $date)
);
$headers = $mime->headers($headers);

// OK, set up our connection info array
$connection_info['driver'] = 'smtp';
$connection_info['host'] = $config['smtp_server'];
$connection_info['port'] = $config['smtp_port'];
if( isset($config['smtp_username']) ) { 
	$connection_info['username'] = $config['smtp_username'];
}
if( isset($config['smtp_password']) ) {
	$connection_info['password'] = $config['smtp_password'];
}
$connection_info['auth'] = ( isset($config['smtp_ssl']) && $config['smtp_ssl'] );

$mail = &Mail::factory('smtp', $connection_info);

print "Sending...";
$result = $mail->send($config['emails_to'], $headers, $body);


if( PEAR::isError($result) ) {
	// Uh oh, couldn't send the email for some reason.
	print "\n";
	fwrite(STDERR, 'Unable to send email:' . $result->getMessage() . "\n");
	exit(1);
}
else {
	print "done\n";
}

print '*** PROGRAM ENDED AT ' . date(DATE_RFC850) . "\n";
exit(0);
