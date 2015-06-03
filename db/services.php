<?php

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
 * @package    block_teamplay
 * @copyright  2015 gtn gmbh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'block_teamplay_get_gestures' => array(
                'classname'   => 'block_teamplay_external',
                'methodname'  => 'get_gestures',
                'classpath'   => 'blocks/teamplay/externallib.php',
                'description' => 'Returns a list of available gestures',
                'type'        => 'read'
        ),
        'block_teamplay_get_users' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'get_users',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Returns a list of available users',
        		'type'        => 'read'
        ),
        'block_teamplay_send_new_gesture' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'send_new_gesture',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Sends a new gesture',
        		'type'        => 'write'
        ),
        'block_teamplay_get_my_confirmations' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'get_my_confirmations',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Gets a list of pending confirmations',
        		'type'        => 'read'
        ),
        'block_teamplay_confirm' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'confirm',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Confirms a gesture/request',
        		'type'        => 'write'
        ),
        'block_teamplay_send_new_request' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'send_new_request',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Sends a new request',
        		'type'        => 'write'
        ),
        'block_teamplay_get_requests' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'get_requests',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Returns a list of pending requests',
        		'type'        => 'read'
        ),
        'block_teamplay_take_request' => array(
        		'classname'   => 'block_teamplay_external',
        		'methodname'  => 'take_request',
        		'classpath'   => 'blocks/teamplay/externallib.php',
        		'description' => 'Takes a certain request for a user',
        		'type'        => 'write'
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'teamplayservices' => array(
                'functions' => array (
                		'block_teamplay_get_gestures',
                	'block_teamplay_get_users',
                	'block_teamplay_send_new_gesture',
                	'block_teamplay_get_my_confirmations',
                	'block_teamplay_confirm',
                	'block_teamplay_send_new_request',
                	'block_teamplay_get_requests',
                	'block_teamplay_take_request'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);