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

namespace factor_message;

use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * message factor class.
 *
 * @package     factor_message
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /** @var string Factor icon */
    protected $icon = 'fa-phone';

    /**
     * E-Mail Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition($mform): \MoodleQuickForm {
        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * E-Mail Factor implementation.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition_after_data( $mform): \MoodleQuickForm {
        $this->generate_and_message_code();
        return $mform;
    }

    

    /**
     * E-Mail Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation( $data): array {
        global $USER;
        $return = [];

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_message');
        }

        return $return;
    }

    /**
     * E-Mail Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    // public function get_all_user_factors(stdClass $user): array {
public function get_all_user_factors($user): array{
        global $DB;

        $records = $DB->get_records('tool_mfa', [
            'userid' => $user->id,
            'factor' => $this->name,
            // 'label' => $USER->phone1,
        ]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'label' => $USER->phone,
            'createdfromip' => $user->lastip,
            'timecreated' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        if (self::is_ready()) {
            return true;
        }
        return true;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        // shree ram
        if (!self::is_ready()) {
            // return \tool_mfa\plugininfo\factor::STATE_PASS;
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }
      
        return parent::get_state();
    }

    /**
     * Checks whether user message is correctly configured.
     *
     * @return bool
     */
    private static function is_ready(): bool {
        global $DB, $USER;

        if (empty($USER->phone1)) {
            return false;
        }
        if (!validate_email($USER->email)) {
            return false;
        }
        if (over_bounce_threshold($USER)) {
            return false;
        }

        // If this factor is revoked, set to not ready.
        if ($DB->record_exists('tool_mfa', ['userid' => $USER->id, 'factor' => 'message', 'revoked' => 1])) {
            return false;
        }
        return true;
    }

    /**
     * Generates and messages the code for login to the user, stores codes in DB.
     *
     * @return void
     */
    private function generate_and_message_code(): void
    {
        global $PAGE, $DB, $USER, $CFG;
        
        // Get instance that isnt parent message type (label check).
        // This check must exclude the main singleton record, with the label as the message.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
               AND NOT label = ?';

        $record = $DB->get_record_sql($sql, [$USER->id, 'message', $USER->email]);
        $duration = get_config('factor_message', 'duration');
        $newcode = random_int(100000, 999999); 
       
        if (empty($record)) {  
            // No code active, generate new code.
            $instanceid = $DB->insert_record('tool_mfa', [
                'userid' => $USER->id,
                'factor' => 'message',
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ], true);


            $site = get_site();
            if($USER->phone1){
            $data = new stdClass();
            $data->otp = $newcode;
            $data->productname  = $site->fullname;
            $body =  get_string('text_message', 'factor_message', $data);
            // CURL request to send message...  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://buzzify.in/V2/http-api.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'apikey' => get_string('api_key', 'factor_message'),
                'number' => $USER->phone1,
                'message' => $body,
                'senderid' => 'AEQUSS'

            ));
            debugging('DEBUG: API Key is -> '.$apikey, DEBUG_DEVELOPER);
	//    echo $body; die;
	    // Bypass SSL verification (Not recommended for production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            // Return the response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


	    // echo   get_string('api_key', 'factor_message') . " - ".  'number' . $USER->phone1 . " - ". $body;
	    // die;


            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
               print_r( $response);
            }
            die("inside this");
            curl_close($ch);
            }
        } else if ($record->timecreated + $duration < time()) {
            
             // Old code found. Keep id, update fields.
            $DB->update_record('tool_mfa', [
                'id' => $record->id,
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ]);
            $instanceid = $record->id;


            $site = get_site();
            if($USER->phone1){
            $data = new stdClass();
            $data->otp = $newcode;
            $data->productname  = $site->fullname;
            $body =  get_string('text_message', 'factor_message', $data);

            // CURL request to send message...  Shree Ram
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://buzzify.in/V2/http-api.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'apikey' => get_string('api_key', 'factor_message'),
                'number' => $USER->phone1,
                'message' => $body,
                'senderid' => 'AEQUSS'
            ));
            
            // Bypass SSL verification (Not recommended for production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            // Return the response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                // echo $response;
            }
            curl_close($ch);
            }
        }

          $debugmsg = "MFA Message Debug: OTP={$newcode}, phone={$USER->phone1}";
           debugging($debugmsg, DEBUG_DEVELOPER);


            $postfields = [
                    'apikey'   => get_string('api_key','factor_message', ),
                    'number'   => $USER->phone1,
                     'message'  => $body,
                    'senderid' => 'AEQUSS'
                   ];

          debugging('MFA Postfields: ' . json_encode($postfields), DEBUG_DEVELOPER);



    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @param string $enteredcode
     * @return bool
     */
    private function check_verification_code(string $enteredcode): bool {
        global $DB, $USER;
        $duration = get_config('factor_message', 'duration');

        // Get instance that isnt parent message type (label check).
        // This check must exclude the main singleton record, with the label as the message.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
               AND NOT label = ?';
        $record = $DB->get_record_sql($sql, [$USER->id, 'message', $USER->phone1]);

        if ($enteredcode == $record->secret) {
            if ($record->timecreated + $duration > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cleans up message records once MFA passed.
     *
     * {@inheritDoc}
     */
    public function post_pass_state(): void {
        global $DB, $USER;
        // Delete all message records except base record.
        $selectsql = 'userid = ?
                  AND factor = ?
              AND NOT label = ?';
        $DB->delete_records_select('tool_mfa', $selectsql, [$USER->id, 'message', $USER->phone1]);

        // Update factor timeverified.
        parent::post_pass_state();
    }

    /**
     * message factor implementation.
     * message page must be safe to authorise session from link.
     *
     * {@inheritDoc}
     */
    public function get_no_redirect_urls(): array {
        $message = new \moodle_url('/admin/tool/mfa/factor/message/message.php');
        return [$message];
    }

    /**
     * message factor implementation.
     *
     * @param stdClass $user
     */
    public function possible_states($user) {
        // message can return all states.
        return [
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }

    /**
     * Obscure an message address by replacing all but the first and last character of the local part with a dot.
     * So the users full message isn't displayed during login.
     *
     * @param string $message The message address to obfuscate.
     * @return string
     * @throws \coding_exception
     */
    protected function obfuscate_message(string $message): string {
        // Split the message address at the '@' symbol.
        $parts = explode('@', $message);

        if (count($parts) != 2) {
            throw new \coding_exception('Invalid message format');
        }

        $local = $parts[0];
        $domain = $parts[1];

        // Obfuscate all but the first and last character of the local part.
        $length = strlen($local);
        $middledot = "\u{00B7}";
        if ($length > 2) {
            $local = $local[0] . str_repeat($middledot, $length - 2) . $local[$length - 1];
        }

        // Put the message address back together and return it.
        return $local . '@' . $domain;
    }

    /**
     * Get the login description associated with this factor.
     * Override for factors that have a user input.
     *
     * @return string The login option.
     */
    public function get_login_desc(): string {
        global $USER;
        $message = $USER->phone1;
        
        return get_string('logindesc', 'factor_' . $this->name, $message);
    }
}
