#!/usr/bin/php
<?php
//Counts the number of articles under each section and its subsections.

$articles = array();
$sections = array();
$parents = array();
$top_level_count = 0;

require("db.php");

/* check connection */
if($mysqli->connect_errno) {
  printf("Connect failed: %s\n", $mysqli->connect_error);
  exit();
}

$query = "SELECT `id`, `title`, `type`, `datecreated` FROM `articles`;";

if($result = $mysqli->query($query)) {
  /* fetch associative array */
  while ($row = $result->fetch_assoc()) {
    $articles[$row['id']] = $row;
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
      'subsections' => array(),
      'article_count' => 0
    );
    if($row['parent'] == 0) $top_level_count++;
  }

  /* free result set */
  $result->free();
}

/* close connection */
$mysqli->close();

$parents = gather_parents($sections);

while(count($sections) > $top_level_count) {
  foreach($sections as $id => $section) {
    if(!isset($parents[$id])) { //If this section is not the parent of another
      $section['articles'] = array();
      foreach($articles as $article) {
        if($article['type'] == $id) $section['articles'][$article['id']] = $article;
      }
      $section['article_count'] += count($section['articles']);
      unset($section['articles']);
      if(isset($section['parent'])) {
        $sections[$section['parent']]['subsections'][$id] = $section;
        $sections[$section['parent']]['article_count'] += $section['article_count'];
      }
      unset($sections[$id]);
    }
  }
  $parents = gather_parents($sections);
}
 // die(print_r($sections,true));

function gather_parents($sections) {
  $parent_array = array();
  foreach($sections as $id => $section) {
    if(isset($section['parent']) && !isset($parent_array[$section['parent']])) $parent_array[$section['parent']] = $section['parent'];
  }
  return $parent_array;
}

print_r($sections);
