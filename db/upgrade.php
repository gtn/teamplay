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
	if ($oldversion < 2015092900) {
	
		// Define table block_teamplayhighscore to be created.
		$table = new xmldb_table('block_teamplayhighscore');
	
		// Adding fields to table block_teamplayhighscore.
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
		$table->add_field('score', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
	
		// Adding keys to table block_teamplayhighscore.
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
	
		// Conditionally launch create table for block_teamplayhighscore.
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
	
		// Teamplay savepoint reached.
		upgrade_block_savepoint(true, 2015092900, 'teamplay');
	}
}