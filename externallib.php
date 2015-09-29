<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * External Web Services
 *
 * @copyright 2015 gtn gmbh
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ($CFG->libdir . "/externallib.php");
class block_teamplay_external extends external_api {
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_gestures_parameters() {
		return new external_function_parameters ( array () );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_gestures() {
		global $USER,$CFG;
		
		
		$string = file_get_contents($CFG->wwwroot . '/blocks/teamplay/gestures.json');
		$result = json_decode($string, true);
		
		$gestures = array();
		
		foreach($result as $r) {
			$gesture = array("text" => $r["text"],"text2"=>$r["text2"]);	
			$gestures[] = $gesture;
		}
		
		return $gestures;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_gestures_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'text' => new external_value ( PARAM_TEXT, 'gesture text' ),
				'text2' => new external_value ( PARAM_TEXT, 'gesture text' )
		) ) );
	}
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_requests_parameters() {
		return new external_function_parameters ( array () );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_requests() {
		global $USER, $DB;
		
		$requests = $DB->get_records ( 'block_teamplayrequests', array (
				'valid' => 0 
		) );
		
		$returndata = array ();
		foreach ( $requests as $request ) {
			if ($request->sender != $USER->id) {
				$returndataObject = new stdClass ();
				$returndataObject->id = $request->id;
				$returndataObject->text = $request->text;
				$returndataObject->from = fullname ( $DB->get_record ( 'user', array (
						'id' => $request->sender 
				) ) );
				
				$returndata [] = $returndataObject;
			}
		}
		
		return $returndata;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_requests_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'from' => new external_value ( PARAM_TEXT, 'username' ),
				'text' => new external_value ( PARAM_TEXT, 'request text' ),
				'id' => new external_value ( PARAM_INT, 'requestid' ) 
		) ) );
	}
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_users_parameters() {
		return new external_function_parameters ( array () );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_users() {
		global $CFG, $DB, $USER;
		
		$students = $DB->get_records ( 'user' );
		$returndata = array ();
		
		foreach ( $students as $student ) {
			if ($student->id == $USER->id)
				continue;
			
			$returndataObject = new stdClass ();
			$returndataObject->name = fullname ( $student );
			$returndataObject->userid = $student->id;
			$returndata [] = $returndataObject;
		}
		return $returndata;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_users_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'userid' => new external_value ( PARAM_INT, 'id of user' ),
				'name' => new external_value ( PARAM_TEXT, 'name of user' ) 
		) ) );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function send_new_gesture_parameters() {
		return new external_function_parameters ( array (
				'to' => new external_value ( PARAM_INT, 'userid' ),
				'text' => new external_value ( PARAM_TEXT, 'gesture text' ),
				'text2' => new external_value ( PARAM_TEXT, 'gesture text' )
		) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function send_new_gesture($to, $text, $text2) {
		global $USER, $DB;
		
		$DB->insert_record ( "block_teamplaygestures", array (
				'sender' => $USER->id,
				'reciever' => $to,
				'text' => $text,
				'text2' => $text2
		) );
		
		block_teamplay_external::updateScore($USER->id, 1);
		
		return "success";
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function send_new_gesture_returns() {
		return new external_value ( PARAM_TEXT, 'success' );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_my_confirmations_parameters() {
		return new external_function_parameters ( array () );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_my_confirmations() {
		global $CFG, $DB, $USER;
		
		$returndata = array ();
		
		$gestures = $DB->get_records ( 'block_teamplaygestures', array (
				'reciever' => $USER->id,
				'valid' => 0 
		) );
		
		foreach ( $gestures as $gesture ) {
			$returndataObject = new stdClass ();
			$returndataObject->from = fullname ( $DB->get_record ( 'user', array (
					'id' => $gesture->sender 
			) ) );
			$returndataObject->text = $gesture->text;
			if(isset($gesture->text2))
				$returndataObject->text2 = $gesture->text2;
			else
				$returndataObject->text2 = "";
			
			$returndataObject->id = $gesture->id;
			$returndataObject->isGesture = true;
			$returndata [] = $returndataObject;
		}
		
		$gestures = $DB->get_records ( 'block_teamplayrequests', array (
				'sender' => $USER->id,
				'valid' => 0 
		) );
		
		foreach ( $gestures as $gesture ) {
			if (! $gesture->reciever || $gesture->reciever == 0)
				continue;
			
			$returndataObject = new stdClass ();
			$returndataObject->from = fullname ( $DB->get_record ( 'user', array (
					'id' => $gesture->reciever 
			) ) );
			$returndataObject->text = $gesture->text;
			$returndataObject->text2 = "";
			$returndataObject->id = $gesture->id;
			$returndataObject->isGesture = false;
			$returndata [] = $returndataObject;
		}
		return $returndata;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_my_confirmations_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'from' => new external_value ( PARAM_TEXT, 'name of user' ),
				'isGesture' => new external_value ( PARAM_BOOL, 'true if gesture, false if request' ),
				'text' => new external_value ( PARAM_TEXT, 'text' ),
				'text2' => new external_value ( PARAM_TEXT, 'text2' ),
				'id' => new external_value ( PARAM_INT, 'id of gesture or request' ) 
		) ) );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function confirm_parameters() {
		return new external_function_parameters ( array (
				'id' => new external_value ( PARAM_INT, 'gesture or request id' ),
				'isGesture' => new external_value ( PARAM_BOOL, ' true if gesture, false if request' ),
				'confirmed' => new external_value ( PARAM_BOOL, 'gesture/request is confirmed or not' ) 
		) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function confirm($id, $isGesture, $confirmed) {
		global $USER, $DB;
		
		if ($isGesture) {
			$gesture = $DB->get_record ( 'block_teamplaygestures', array (
					'id' => $id 
			) );
			if ($gesture->reciever != $USER->id) {
				return "error";
			}
			$gesture->valid = ($confirmed) ? 1 : 2;
			
			if($confirmed)
				block_teamplay_external::updateScore($gesture->sender, 10);
				
			$DB->update_record ( 'block_teamplaygestures', $gesture );
		} else {
			$request = $DB->get_record ( 'block_teamplayrequests', array (
					'id' => $id 
			) );
			if ($request->sender != $USER->id) {
				return "error";
			}
			$request->valid = ($confirmed) ? 1 : 2;
			
			if($confirmed)
				block_teamplay_external::updateScore($request->receiver, 10);
				
			$DB->update_record ( 'block_teamplayrequests', $request );
		}
		
		block_teamplay_external::updateScore($USER->id, 3);
		
		return "success";
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function confirm_returns() {
		return new external_value ( PARAM_TEXT, 'success' );
	}
	
	/*
	 * @return external_function_parameters
	 */
	public static function take_request_parameters() {
		return new external_function_parameters ( array (
				'id' => new external_value ( PARAM_INT, 'gesture or request id' ) 
		) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function take_request($id) {
		global $USER, $DB;
		
		$request = $DB->get_record ( 'block_teamplayrequests', array (
				'id' => $id 
		) );
		if ($request->reciever != 0) {
			return "error";
		}
		$request->reciever = $USER->id;
		$DB->update_record ( 'block_teamplayrequests', $request );
		
		return "success";
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function take_request_returns() {
		return new external_value ( PARAM_TEXT, 'success' );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function send_new_request_parameters() {
		return new external_function_parameters ( array (
				'text' => new external_value ( PARAM_TEXT, 'gesture text' ) 
		) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function send_new_request($text) {
		global $USER, $DB;
		
		$DB->insert_record ( "block_teamplayrequests", array (
				'sender' => $USER->id,
				'text' => $text 
		) );
		
		block_teamplay_external::updateScore($USER->id, 1);
		
		return "success";
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function send_new_request_returns() {
		return new external_value ( PARAM_TEXT, 'success' );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_my_pending_actions_parameters() {
		return new external_function_parameters ( array () );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_my_pending_actions() {
		global $CFG, $DB, $USER;
	
		$returndata = array ();
	
		$gestures = $DB->get_records ( 'block_teamplaygestures', array (
				'sender' => $USER->id,
				'valid' => 0
		) );
	
		foreach ( $gestures as $gesture ) {
			$returndataObject = new stdClass ();
			$returndataObject->to = fullname ( $DB->get_record ( 'user', array (
					'id' => $gesture->reciever
			) ) );
			$returndataObject->text = $gesture->text;
			if(isset($gesture->text2))
				$returndataObject->text2 = $gesture->text2;
			else
				$returndataObject->text2 = "";
				
			$returndataObject->id = $gesture->id;
			$returndataObject->isGesture = true;
			$returndata [] = $returndataObject;
		}
	
		$gestures = $DB->get_records ( 'block_teamplayrequests', array (
				'reciever' => $USER->id,
				'valid' => 0
		) );
	
		foreach ( $gestures as $gesture ) {
			$returndataObject = new stdClass ();
			$returndataObject->to = fullname ( $DB->get_record ( 'user', array (
					'id' => $gesture->sender
			) ) );
			$returndataObject->text = $gesture->text;
			$returndataObject->text2 = "";
			$returndataObject->id = $gesture->id;
			$returndataObject->isGesture = false;
			$returndata [] = $returndataObject;
		}
		return $returndata;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_my_pending_actions_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'to' => new external_value ( PARAM_TEXT, 'name of user' ),
				'isGesture' => new external_value ( PARAM_BOOL, 'true if gesture, false if request' ),
				'text' => new external_value ( PARAM_TEXT, 'text' ),
				'text2' => new external_value ( PARAM_TEXT, 'text2' ),
				'id' => new external_value ( PARAM_INT, 'id of gesture or request' )
		) ) );
	}
	

	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function cancel_parameters() {
		return new external_function_parameters ( array (
				'id' => new external_value ( PARAM_INT, 'gesture or request id' ),
				'isGesture' => new external_value ( PARAM_BOOL, ' true if gesture, false if request' )
		) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function cancel($id, $isGesture) {
		global $USER, $DB;
	
		if ($isGesture) {
			$gesture = $DB->get_record ( 'block_teamplaygestures', array (
					'id' => $id,
					'valid' => 0
			) );
			if ($gesture->sender != $USER->id) {
				return "error";
			}
			$DB->delete_records ( 'block_teamplaygestures', array('id' => $id) );
		} else {
			$request = $DB->get_record ( 'block_teamplayrequests', array (
					'id' => $id,
					'valid' => 0
			) );
			if ($request->reciever != $USER->id) {
				return "error";
			}
			$request->reciever = 0;
			
			$DB->update_record ( 'block_teamplayrequests', $request );
		}
	
		block_teamplay_external::updateScore($USER->id, -3);
		
		return "success";
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function cancel_returns() {
		return new external_value ( PARAM_TEXT, 'success' );
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_highscore_parameters() {
		return new external_function_parameters ( array ( ) );
	}
	
	/**
	 * Returns welcome message
	 *
	 * @return string welcome message
	 */
	public static function get_highscore() {
		global $USER, $DB;
	
		$score = $DB->get_record('block_teamplayhighscore', array('userid' => $USER->id));
	
		return $score->score;
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_highscore_returns() {
		return new external_value ( PARAM_INT, 'score' );
	}
	
	private static function updateScore($userid, $score) {
		global $DB;
		
		$scoreRecord = $DB->get_record('block_teamplayhighscore', array("userid" => $userid));
		if(!$scoreRecord) {
			$DB->insert_record('block_teamplayhighscore', array('userid' => $userid, 'score' => ($score > 0) ? $score : 0));
		} else {
			$scoreRecord->score += $score;
			if($scoreRecord->score < 0)
				$scoreRecord->score = 0;
			
			$DB->update_record('block_teamplayhighscore', $scoreRecord);
		}
	}
}