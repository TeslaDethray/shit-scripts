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

define('OLD_DOMAIN', '');

//$content = query('SELECT * FROM `jos_content`;', 'old', 'id');
//$labels = query('SELECT jos_jxlabels_maps.item_id, jos_jxlabels_labels.title FROM jos_jxlabels_maps, jos_jxlabels_labels WHERE jos_jxlabels_maps.label_id = jos_jxlabels_labels.label_id;', 'old');
$attachments = query('SELECT * FROM `jos_attachments`;', 'old', 'id');
$files_array = query('SELECT * FROM `file_managed`;', 'new', 'fid');
$files = [];
$done_ids = [];

foreach ($files_array as $fid => $file) $files[$fid] = $file['filename'];

foreach($attachments as $id => $attachment) {
  if(isset($done_ids[$attachment['article_id']])) continue;
  $done_ids[$attachment['article_id']] = $id;

  $fid = array_search($attachment['filename'], $files);
  if(!$fid) continue;
  $insert_array = array(
    'entity_type' => 'node',
    'bundle' => 'page',
    'deleted' => 0,
    'entity_id' => $attachment['article_id'],
    'revision_id' => 1,
    'language' => 'und',
    'delta' => 0,
    'field_attachment_fid' => $fid,
    'field_attachment_display' => 1,
    'field_attachment_description' => ''
  );

  insert('field_data_field_attachment', array_keys($insert_array), $insert_array, true);
  insert('field_revision_field_attachment', array_keys($insert_array), $insert_array, true);
}

/*
$filenames = array();
foreach($attachments as $id => $attachment) {
  if(!isset($filenames[$id])) {
    $filenames[$id] = $attachment['filename'];
    $insert_array = array(
      'uid' => 1,
      'filename' => $attachment['filename'],
      'uri' => 'public://' . $attachment['filename'],
      'filemime' => $attachment['file_type'],
      'filesize' => $attachment['file_size'],
      'status' => 1,
      'timestamp' => strtotime($attachment['create_date']),
      'type' => ($attachment['file_type']) ? 'image' : 'default'
    );
    insert('file_managed', array_keys($insert_array), $insert_array);
  }
}

$fids = query('SELECT `fid`, `filename` FROM `file_managed`;', 'new');

$fidlist = array();
foreach($fids as $fid => $data) {
  $insert_array = array(
    'fid' => $fid,
    'module' => 'file',
    'type' => 'node',
    'id' => 1,
    'count' => 1
  );
  insert('file_usage', array_keys($insert_array), $insert_array);
  $fidlist[$fid] = $data['filename'];
}

foreach($attachments as $id => $attachment) {
  $fid = array_search($attachment['filename'], $fidlist);
  if(!$fid) continue;
  $insert_array = array(
    'entity_type' => 'node',
    'bundle' => 'page',
    'deleted' => 0,
    'entity_id' => $attachment['article_id'],
    'revision_id' => 1,
    'language' => 'und',
    'delta' => 0,
    'field_attachment_fid' => $fid,
    'field_attachment_display' => 1,
    'field_attachment_description' => ''
  );

  insert('field_data_field_attachment', array_keys($insert_array), $insert_array);
  insert('field_revision_field_attachment', array_keys($insert_array), $insert_array);
}

foreach($content as $id => $item) {
  insert('node', array('nid', 'vid', 'type', 'language', 'title', 'uid', 'status', 'created', 'changed', 'comment', 'promote', 'sticky', 'tnid', 'translate'), array($id, $id, 'page', 'und', $item['title'], '1', '1', strtotime($item['created']), strtotime($item['modified']), '1', '0', '0', '0', '0'));
  insert('node_revision', array('nid', 'vid', 'uid', 'title', 'log', 'timestamp', 'status', 'comment', 'promote', 'sticky'), array($id, $id, '1', $item['title'], '', strtotime($item['created']), '1', '1', '0', '0'));
  insert('history', array('uid', 'nid', 'timestamp'), array('1', $id, strtotime($item['created'])));
  insert('node_comment_statistics', array('nid', 'cid', 'last_comment_timestamp', 'last_comment_name', 'last_comment_uid', 'comment_count'), array($id, '0', strtotime($item['created']), '', '1', '0'));
  insert('field_data_body', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'body_value', 'body_summary', 'body_format'), array('node', 'page', '0', $id, $id, 'und', '0', $item['introtext'], '', 'full_html'));
  insert('field_revision_body', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'body_value', 'body_summary', 'body_format'), array('node', 'page', '0', $id, $id, 'und', '0', $item['introtext'], '', 'full_html'));
}
$labels_list = array();
foreach($labels as $id => $row) {
  if(!isset($labels_list[$row['item_id']])) $labels_list[$row['item_id']] = array();
  $labels_list[$row['item_id']][] = $row['title'];
}

foreach($labels_list as $content_id => $label) {
  insert('field_data_field_labels_joomla', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'field_labels_joomla_value', 'field_labels_joomla_format'), array('node', 'page', 0, $content_id, 1, 'und', 0, implode(', ', $labels_list[$content_id]), ''), true);
  insert('field_revision_field_labels_joomla', array('entity_type', 'bundle', 'deleted', 'entity_id', 'revision_id', 'language', 'delta', 'field_labels_joomla_value', 'field_labels_joomla_format'), array('node', 'page', 0, $content_id, 1, 'und', 0, implode(', ', $labels_list[$content_id]), ''), true);
}
 */

function insert($table, $elements, $values, $echo = false) {
  $query = "INSERT INTO $table (" . implode(', ', $elements) . ') VALUES("' . implode('", "', $values) . '");' . "\n";
  if(!$echo) query($query, 'new');
  echo $query;
}

function query($query, $which_db, $id_var_name = false) {
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
    if($result->num_rows == 0) {
      if(strpos(strtolower($query), 'select') === false) return false;
      return true;
    }
    while ($row = $result->fetch_assoc()) {
      if(!$id_var_name) {
        $array[] = preg_replace('/[\x00-\x1F\x80-\xFF]/','', str_replace('"', '\"', $row));
      } else {
        $array[$row[$id_var_name]] = preg_replace('/[\x00-\x1F\x80-\xFF]/','', str_replace('"', '\"', $row));
      }
    }
    $result->free();
  }
  $mysqli->close();

  if($result) return $array;
  return false;
}
