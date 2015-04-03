#!/usr/bin/php
<?php

require("db.php");

$domain = 'http://coolcalifornia.org';
date_default_timezone_set('America/Los_Angeles');
$acceptable_nodes = array('article', 'banner', 'carousel_video', 'case_study', 'fact', 'highlight', 'page', 'tip', 'tout', 'webform');

$content = array();

if($mysqli->connect_errno) {
  printf("Connect failed: %s\n", $mysqli->connect_error);
  exit();
}

$query = "SELECT * FROM `node`;";
if($result = $mysqli->query($query)) {
  while ($row = $result->fetch_assoc()) {
    if(in_array($row['type'], $acceptable_nodes)) {
      $content[$row['nid']] = array(
        'id' => $row['nid'],
        'type' => $row['type'],
        'title' => str_replace('"', '\"', $row['title']),
        'date' => date('Y-m-d H:i:s', $row['created']),
        'url' => $domain . '/node/' . $row['nid']
      );
    }
  }

  /* free result set */
  $result->free();
}

$query = "SELECT * FROM `blocks`;";

if($result = $mysqli->query($query)) {
  while ($row = $result->fetch_assoc()) {
    $content['b' . $row['bid']] = array(
      'id' => $row['bid'],
      'type' => 'block',
      'title' => str_replace('"', '\"', $row['title']),
      'date' => 'N/A',
      'url' => 'N/A'
    );
  }

  /* free result set */
  $result->free();
}

$query = "SELECT * FROM `views_display`;";

if($result = $mysqli->query($query)) {
  while ($row = $result->fetch_assoc()) {
    $serialized_data = unserialize($row['display_options']);
    $url = 'N/A';
    if(isset($serialized_data['path'])) $url = $domain . '/' . $serialized_data['path'];

    $content[$row['vid']] = array(
      'id' => $row['vid'],
      'type' => 'view',
      'title' => str_replace('"', '\"', $row['display_title']),
      'date' => 'N/A',
      'url' => $url
    );
  }

  /* free result set */
  $result->free();
}

$mysqli->close();

foreach($content as $id => $item) {
  echo '"' . implode('","', $item) . '"' . "\n";
}

?>
