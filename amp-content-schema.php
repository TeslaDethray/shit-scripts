<?php
//Outputs AMP content for review by client to CSV

$articles = array();
$sections = array();
$parents = array();
$classes = array();
$csv = array();
$top_level_count = 0;


/** Pulling out the list of desirec content **/
require("db.php");
$db = new db('cesr_pared');
$function = function($row) {$return = ($row['migrate'] == 'Y') ? $row : false; return $return;};
$options = $db->query("SELECT * FROM `list`;",  $function);

$db = new db('cesr');
$query = "SELECT `id`, `title`, `type`, `datecreated`, `class`, `pageorder`, `publish`, `test`, `custom2`, `custom3` FROM `articles`;";
$function = function($row) {if(isset($extra_vars[$row['id']]) && ($row['custom3'] == 'English')) return $row;};
$articles = $db->query($query, $function, $options);


$query = "SELECT `id`, `class` FROM `class`;";
$classes = $db->query($query);

$query = "SELECT `id`, `type`, `timestamp`, `description`, `parent` FROM `articletype` ORDER BY `parent`;";
$function = function($row) {
  return array(
    'id' => $row['id'],
    'type' => $row['type'],
    'timestamp' => $row['timestamp'],
    'description' => $row['description'],
    'parent' => $row['parent'],
    'subsections' => array()
  );
};
$sections = $db->query($query, $function);
die(print_r($sections, true));

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
