<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings.
 *
 * @package     factor_message
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['message:accident'] = 'If you didn\'t request the message, click continue to invalidate the login attempt. If you clicked the link by accident, click cancel, and no action will be taken.';
$string['message:browseragent'] = 'The browser details for this request are: \'{$a}\'';
$string['message:geoinfo'] = 'This request appears to have originated from approximately:';
$string['message:greeting'] = 'Hello {$a} &#128075;';
$string['message:ipinfo'] = 'Login request details:';
$string['message:link'] = 'verification link';
$string['message:loginlink'] = 'Or, if you\'re on the same device, use this {$a}.';
$string['message:message'] = 'Here\'s your verification code for {$a->sitename} ({$a->siteurl}).';
$string['message:originatingip'] = 'This login request was made from \'{$a}\'';
$string['message:revokelink'] = 'If this wasn\'t you, you can {$a}.';
$string['message:revokesuccess'] = 'This code has been successfully revoked. All sessions for {$a} have been ended.
    message will not be usable as a factor until account security has been verified.';
$string['message:subject'] = 'Here\'s your verification code';
$string['message:stoploginlink'] = 'stop this login attempt';
$string['message:uadescription'] = 'Browser identity for this request:';
$string['message:validity'] = 'The code can only be used once and is valid for {$a}.';
$string['error:badcode'] = 'Code was not found. This may be an old link, a new code may have been messageed, or the login attempt with this code was successful.';
$string['error:parameters'] = 'Incorrect page parameters.';
$string['error:wrongverification'] = 'Wrong code. Try again.';
$string['event:unauthmessage'] = 'Unauthorised message received';
$string['info'] = 'You are using message {$a} to authenticate. This has been set up by your site administrator.';
$string['logindesc'] = 'We\'ve just sent a 6-digit code to your phone: {$a}';
$string['loginoption'] = 'Have a code messageed to you';
$string['loginskip'] = "I didn't receive a code";
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = "Verify it's you by message";
$string['managefactor'] = 'Manage message';
$string['manageinfo'] = '\'{$a}\' is being used to authenticate. This has been set up by your administrator.';
$string['pluginname'] = 'message';
$string['privacy:metadata'] = 'The message factor plugin does not store any personal data';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:suspend'] = 'Suspend unauthorised accounts';
$string['settings:suspend_help'] = 'Check this to suspend user accounts if an unauthorised message verification is received.';
$string['setupfactor'] = 'Set up message';
$string['summarycondition'] = 'has valid message setup';
$string['unauthloginattempt'] = 'The user with ID {$a->userid} made an unauthorised login attempt using message verification from
IP {$a->ip} with browser agent {$a->useragent}.';
$string['unauthmessage'] = 'Unauthorised message';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'A verification code has been sent to your message.';
//$string['text_message'] = 'Thank you for choosing Aequs Private Limited! OTP to complete the sign up process is {$a->otp}. It is valid for 10 minutes.&format=json';
$string['text_message'] = 'Thank you for signing into PraShikshaa from Aequs Private Limited! OTP to complete the sign-up process is {$a->otp}. It is valid for 10 minutes.';
//$string['api_key'] = '7qJ7yUsZvQjthm3O';
$string['text_message_signup'] = 'A new account has been created for you at \'Aequs Private Limited\'

Your current login details are:
username: {$a->username}
password: {$a->password}';

$string['api_key'] = 'AWdwXbqDIc3stnH2';

