#!/usr/bin/php

<?php

//A simple script to transfer an uncomplicated Joomla site to an uncomplicated Drupal one
date_default_timezone_set('America/Los_Angeles');

define('DB_OLD_NAME', '');
define('DB_OLD_USER', '');
define('DB_OLD_PASS', '');
define('DB_OLD_HOST', '');

define('DB_NEW_NAME', '');
define('DB_NEW_USER', '');
define('DB_NEW_PASS', '');
define('DB_NEW_HOST', '');

$content = query('SELECT * FROM `jos_content`;', 'old');

//die(print_r($content,true));

foreach($content as $id => $item) {
  insert('node', array('nid', 'vid', 'type', 'language', 'title', 'uid', 'status', 'created', 'changed', 'comment', 'promote', 'sticky', 'tnid', 'translate'), array($id, $id, 'page', 'und', $item['title'], '1', '1', strtotime($item['created']), strtotime($item['modified']), '1', '0', '0', '0', '0'));
  insert('node_revision', array('nid', 'vid', 'uid', 'title', 'log', 'timestamp', 'status', 'comment', 'promote', 'sticky'), array($id, $id, '1', $item['title'], '', strtotime($item['created']), '1', '1', '0', '0'));
  insert('history', array('uid', 'nid', 'timestamp'), array('1', $id, strtotime($item['created'])));
  insert('node_comment_statistics', array('nid', 'cid', 'last_comment_timestamp', 'last_comment_name', 'last_comment_uid', 'comment_count'), array($id, '0', strtotime($item['created']), '', '1', '0'));
  insert('field_data_body', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'body_value', 'body_summary', 'body_format'), array('node', 'page', '0', $id, $id, 'und', '0', $item['introtext'], '', 'full_html'));
  insert('field_revision_body', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'body_value', 'body_summary', 'body_format'), array('node', 'page', '0', $id, $id, 'und', '0', $item['introtext'], '', 'full_html'));
}

function insert($table, $elements, $values) {
  echo "INSERT INTO $table (" . implode(', ', $elements) . ') VALUES("' . implode('", "', $values) . '");' . "\n";
}

function query($query, $which_db, $id_var_name = 'id') {
  $array = array();
  $which_db = strtoupper($which_db);

  $mysqli = new mysqli(
    constant('DB_' . $which_db . '_HOST'), 
    constant('DB_' . $which_db . '_USER'), 
    constant('DB_' . $which_db . '_PASS'), 
    constant('DB_' . $which_db . '_NAME')
  );
  if($mysqli->connect_error) {
    die("Connect error (" . $mysqli->connect_errno . ": " . $mysqli->connect_error . ")\n\n");
  }

  if($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) $array[$row[$id_var_name]] = preg_replace('/[\x00-\x1F\x80-\xFF]/','', str_replace('"', '\"', $row));
    $result->free();
  }
  $mysqli->close();

  if($result) return $array;
  return false;
}

?>
