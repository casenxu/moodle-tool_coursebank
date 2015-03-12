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
 *
 * @package    tool_coursestore
 * @author     Adam Riddell <adamr@catalyst-au.net>
 * @copyright  2015 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

/**
 * Class that handles outgoing web service requests.
 * 
 */
class coursestore_ws_manager {
    private $curlhandle;

    /**
     * @param string  $url            Target URL
     * @param int     $conntimeout    Connection time out (seconds)
     * @param int     $timeout        Request time out (seconds)
     */
    function __construct($url, $conntimeout, $timeout) {
        $curlopts = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $conntimeout,
            CURLOPT_URL => $url
        );
        $this->curlhandle = curl_init(); 
        curl_setopt_array($this->curlhandle, $curlopts);
    }
    /**
     * Close the associated curl handler object
     */
    function close() {
        curl_close($this->curlhandle);
    }
    /**
     * Send a the provided data in JSON encoding as a POST request
     * 
     * @param array $data    Associative array of request data to send
     * @param int   $maxatt  Max number of attempts to make before failing
     */
    function send($data, $maxatt = 5) {
        $postdata = json_encode($data);
        curl_setopt($this->curlhandle, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($this->curlhandle, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
                'Content-Length: ' . strlen($postdata))
        );
        //Make $maxatt number of attempts to send request
        for($attempt=0; $attempt < $maxatt; $attempt++) {
            $result = curl_exec($this->curlhandle);
            $response = curl_getinfo($this->curlhandle);
            $httpcode = $response['http_code'];
            if($httpcode == '202' || $httpcode == '200') {
               return true;
            }
        }
        return false;
    }
    
}
