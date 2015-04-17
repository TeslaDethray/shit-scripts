<?php
//Moves an AMP site to Drupal

require("db.php");
require("settings/drupalSettings.php");
$csv = array();
$image_data = array();
$file_locations = array('doc' => '/docs/', 'picture' => '/images/');
$local_file_locations = array('doc' => '/Users/tesladethray/Work/scripts/doc/', 'picture' => '/Users/tesladethray/Work/scripts/image/');

$types = array(
  'page' => array(
    'node',
    'node_comment_statistics',
    'node_revision',
    'entity_translation',
    'entity_translation_revision',
    'field_data_field_attached_document',
    'field_data_field_image',
    'field_data_field_amp_id',
    'field_data_field_amp_section',
    'field_data_field_author',
    'field_data_body'
  )
);
$types['blog_post'] = $types['page'];

/** Pulling out the list of desired content **/
$db_pared = new db('cesr_pared');
$function = function($row) {$return = $row['migrate'] ? $row : false; return $return;};
$options = $db_pared->query("SELECT * FROM `list`;",  $function);

/** Pulling out the section names **/
$db_old = new db('cesr');
$query_old = "SELECT `id`, `type` FROM `articletype`;";
$sections = $db_old->query($query_old);

/** Pulling out the articles **/
$query_content = "SELECT * FROM `articles`;";
$articles = $db_old->query($query_content);

$db = new db('cesr_drupal');
process_database($options, $articles);
process_database($options, $articles, 'translated_id');

function process_database($options, $articles, $id_var = 'id') {
  global $db, $types, $schema;
  foreach($options as $id => $option) {
    if(isset($types[$option['drupal_type']])) {
      foreach($types[$option['drupal_type']] as $table_name) {
        if($table_name == 'field_data_field_attached_document') {
          $articles[$option[$id_var]]['doc_fid'] = ($articles[$option[$id_var]]['doc'] != '') ? extract_docs($articles[$option[$id_var]]) : false;
        } elseif($table_name == 'field_data_field_image') {
          $articles[$option[$id_var]]['img_fid'] = ($articles[$option[$id_var]]['picture'] != '') ? extract_images($articles[$option[$id_var]]) : false;
        } elseif(isset($option[$id_var]) && isset($articles[$option[$id_var]])) {
          $insert_array = pair_keys($schema[$table_name], $articles[$option[$id_var]], $option);
          $revision_table = str_replace('_data_', '_revision_', $table_name);

          $db->query(insert_query($insert_array, $table_name));
          if($revision_table != $table_name) $db->query(insert_query($insert_array, $revision_table));
        }
      }
    }
  }
}

function extract_docs($article) {
  global $db, $schema;
  $fid = process_attachments($article, 'doc');
  $type = 'default';

  $overlapping_array = array(
    'field_attached_document_fid' => $fid,
  );
  $insert_array = pair_keys(array_merge($schema['field_data_field_attached_document'], $overlapping_array), $article, $type);
  $db->query(insert_query($insert_array, 'field_data_field_attached_document'));
  $db->query(insert_query($insert_array, 'field_revision_field_attached_document'));

  return $fid;
}

function extract_images($article) {
  global $db, $schema, $local_file_locations;
  $type = 'picture';

  $fid = process_attachments($article, $type);
  if(!$fid) return false;

  $image_dimensions = getimagesize($local_file_locations[$type] . $article[$type]);
  $overlapping_array = array(
    'fid' => $fid,
    'width' => $image_dimensions[0],
    'height' => $image_dimensions[1]
  );
  $insert_array = array_merge($schema['image_dimensions'], $overlapping_array);
  $db->query(insert_query($insert_array, 'image_dimensions'));

  $overlapping_array = array(
    'field_image_fid' => $fid,
    'field_image_width' => $image_dimensions[0],
    'field_image_height' => $image_dimensions[1]
  );
  $insert_array = pair_keys(array_merge($schema['field_data_field_image'], $overlapping_array), $article, $type);
  $db->query(insert_query($insert_array, 'field_data_field_image'));
  $db->query(insert_query($insert_array, 'field_revision_field_image'));

  return $fid;
}

function insert_query($array, $table_name) {
  return 'INSERT INTO `' . $table_name . '` (`' . implode('`, `', array_keys($array)) . '`) 
    VALUES ("' . implode('", "', $array) . '");';
}

function pair_keys($insert_array, $article, $option) {
  if(is_array($option)) {$option = $option['drupal_type'];}
  foreach($insert_array as $column => $value) {
    $insert_array[$column] = $value;
    if((strpos($value, '[') !== false) && (strpos($value, ']') !== false)) {
      $insert_array[$column] = pair_key($article, trim($value, "[]"), $option);
    }
  }
  return $insert_array;
}

function pair_key($article, $key, $entity) {
  global $sections;
  $keys = array(
    'ENTITY_TYPE' => $entity
  );
  if(isset($article['doc_fid'])) $keys['DRUPAL_FID'] = $article['doc_fid'];
  if(isset($article['img_fid'])) $keys['DRUPAL_FID'] = $article['img_fid'];
  if(isset($keys[$key])) return $keys[$key];

  $item_name = explode('_', $key);
  if(count($item_name) == 2) {
    if(($item_name[1] == 'DATECREATED') || ($item_name[1] == 'UPDATED')) return strtotime($article[strtolower($item_name[1])]);
    if(($item_name[1] == 'TEST') || ($item_name[1] == 'SHORTDESC')) return $article[strtolower($item_name[1])];
    if($item_name[1] == 'SECTION') return $sections[$article['type']]['type'];
    if($item_name[1] == 'CUSTOM3') {
      if($article[strtolower($item_name[1])] == 'Spanish') return 'es';
      return 'en';
    }

    return $article[strtolower($item_name[1])];
  }
}

function process_attachments($article, $type) {
  global $db, $schema, $file_locations, $local_file_locations;
  if(!file_exists($local_file_locations[$type] . $article[$type])) return false;

  $overlapping_array = array(
    'filename' => $article[$type],
    'uri' => 'public://' . $file_locations[$type] . $article[$type],
    'filemime' => mime_content_type($local_file_locations[$type] . $article[$type]),
    'filesize' => filesize($local_file_locations[$type] . $article[$type]),
    'timestamp' => strtotime($article['datecreated']),
    'type' => ($type == 'picture') ? 'image' : 'default'
  );
  $insert_array = array_merge($schema['file_managed'], $overlapping_array);

  $db->query(insert_query($insert_array, 'file_managed'));
  $db->setIDVar('fid');
  $fid = $db->query("SELECT `fid` FROM `file_managed` ORDER BY `fid` DESC LIMIT 1;");
  $db->setIDVar('id');
  $fid = array_shift($fid);
  
  $insert_array = $schema['file_usage'];
  $insert_array['fid'] = $fid['fid'];
  $db->query(insert_query($insert_array, 'file_usage'));

  return $fid['fid'];
}

function test($content) {die(print_r($content, true));}
