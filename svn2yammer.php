<?php
/**
 * Subversion post-commit script to announce commits in a Yammer group.
 *
 * Intended to be called by the Subversion post-commit hook, with the following arguments:
 *   svn2yammer.php {repository path} {revision}
 *
 * See https://www.yammer.com/company/email for more about the Yammer email gateway.
 *
 * @author    Jason Grimes <jg@proz.com>
 * @link      https://github.com/jasongrimes/svn2yammer
 * @copyright Copyright (c) 2011 ProZ.com
 */

/////////  Config  ///////////////

// The email address of the account sending the Yammer message. Must be a confirmed email at Yammer.
// Consider creating a separate Yammer account for SVN, ex. svn@example.com.
$from_email = 'svn@example.com';

// The email address of the group to which the Yammer message should be sent.
$to_email = 'developers+example.com@yammer.com';

// The base URL of your WebSVN server, for clicking through to review commmits.
// The revision and repository parameters are appended automatically.
$websvn_baseurl = 'http://dev.example.com/svn';

// The full path to the svnlook command, installed with Subversion.
$svnlook = '/usr/bin/svnlook';

//////// End config /////////////

// Parse command line args.
$repository = isset($argv[1]) ? $argv[1] : '';
$revision = isset($argv[2]) ? $argv[2] : '';

// Look up info about this commit.
$author = exec($svnlook . ' author ' . $repository);

$output = array();
exec($svnlook . ' changed ' . $repository, $output);
$changed = implode("\n", $output);

$output = array();
exec($svnlook . ' log ' . $repository, $output);
$log = implode("\n", $output);

// Make the Yammer message.
$msg = $author . ' committed ' . $websvn_baseurl . '/revision.php?repname=' . basename($repository) . '&rev=' . $revision;
$msg .= "\n\n";
$msg .= $log . "\n\n" . $changed;

// Send the message.
mail($to_email, "", $msg, 'From: ' . $from_email);

