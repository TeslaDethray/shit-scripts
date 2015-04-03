<?php

$articles = array();
$sections = array();
$parents = array();
$classes = array();
$csv = array();
$top_level_count = 0;


/** Pulling out the list of desirec content **/
require("db.php");
$options = array();

$query = "SELECT `id`, `migrate`, `drupal_type`, `method`, `complete` FROM `list`;";

if($result = $mysqli->query($query)) {
  /* fetch associative array */
  while ($row = $result->fetch_assoc()) {
    if($row['migrate'] == 'Y') $options[$row['id']] = $row;
  }

  /* free result set */
  $result->free();
}

$mysqli->close();
/** end **/

$mysqli = new mysqli("localhost", "root", "amethyst", "cesr");

/* check connection */
if($mysqli->connect_errno) {
  printf("Connect failed: %s\n", $mysqli->connect_error);
  exit();
}

$query = "SELECT `id`, `title`, `type`, `datecreated`, `class`, `pageorder`, `publish`, `test`, `custom2`, `custom3` FROM `articles`;";

if($result = $mysqli->query($query)) {
  /* fetch associative array */
  while ($row = $result->fetch_assoc()) {
    if(isset($options[$row['id']]) && ($row['custom3'] == 'English')) $articles[$row['id']] = $row;
  }

  /* free result set */
  $result->free();
}

$query = "SELECT `id`, `class` FROM `class`;";

if($result = $mysqli->query($query)) {
  /* fetch associative array */
  while ($row = $result->fetch_assoc()) {
    $classes[$row['id']] = $row;
  }

  /* free result set */
  $result->free();
}

$query = "SELECT `id`, `type`, `timestamp`, `description`, `parent` FROM `articletype` ORDER BY `parent`;";

if($result = $mysqli->query($query)) {
  /* fetch associative array */
  while ($row = $result->fetch_assoc()) {
    $sections[$row['id']] = array(
      'id' => $row['id'],
      'type' => $row['type'],
      'timestamp' => $row['timestamp'],
      'description' => $row['description'],
      'parent' => $row['parent'],
      'subsections' => array()
    );
    if($row['parent'] == 0) $top_level_count++;
  }

  /* free result set */
  $result->free();
}

/* close connection */
$mysqli->close();

$array = array(
  'Article ID',
  'Date Created',
  'Title',
  'Parent Section ID',
  'Parent Section Title',
  'Class',
  'Drupal Content Type',
  'Translated Article ID',
  'Published',
  'Order',
  'Text Empty?',
  'Migrate?',
  'Method',
  'Complete'
);
$csv[] = '"' . implode('","', $array) . '"';

foreach($articles as $id => $article) {
  $type_id = 'N/A';
  $type = 'N/A';
  if(isset($article['type'])) {
    $type_id = $article['type'];
    if(isset($sections[$type_id])) $type = $sections[$type_id]['type'];
  }

  $array = array(
    $id,
    $article['datecreated'],
    str_replace('"', '\"', $article['title']),
    $type_id,
    $type,
    str_replace('"', '\"', $classes[$article['class']]['class']),
    $options[$id]['drupal_type'],
    $article['custom2'],
    ($article['publish']) ? "Y" : "N",
    $article['pageorder'],
    ($article['test'] == '') ? "Y" : "N",
    $options[$id]['migrate'],
    $options[$id]['method'],
    $options[$id]['complete']
  );
  $csv[] = '"' . implode('","', $array) . '"';
}

foreach($csv as $line) {
  echo $line . '
';
}

?>
