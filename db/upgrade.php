<?php
function xmldb_block_teamplay_upgrade($oldversion) {
	global $DB;
	$dbman = $DB->get_manager();
	
	if ($oldversion < 2015060200) {
	
		// Define field text2 to be added to block_teamplaygestures.
		$table = new xmldb_table('block_teamplaygestures');
		$field = new xmldb_field('text2', XMLDB_TYPE_TEXT, null, null, null, null, null, 'valid');
	
		// Conditionally launch add field text2.
		if (!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
		}
	
		// Teamplay savepoint reached.
		upgrade_block_savepoint(true, 2015060200, 'teamplay');
	}
}