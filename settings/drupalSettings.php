<?php
//Drupal fields to translate into AMP

$schema = array(
  //NODE
  'node' => array(
    'nid' => '[AMP_ID]',
    'vid' => '[AMP_ID]',
    'type' => '[ENTITY_TYPE]',
    'language' => '[AMP_CUSTOM3]',
    'title' => '[AMP_TITLE]',
    'uid' => 1,
    'status' => '[AMP_PUBLISH]',
    'created' => '[AMP_DATECREATED]',
    'changed' => '[AMP_UPDATED]',
    'comment' => '[AMP_COMMENTS]',
    'promote' => 0,
    'sticky' => 0,
    'tnid' => '[AMP_CUSTOM2]',
    'translate' => 0
  ),
  'node_comment_statistics' => array(
    'nid' => '[AMP_ID]',
    'cid' => 0,
    'last_comment_timestamp' => '[AMP_DATECREATED]',
    'last_comment_name' => '',
    'last_comment_uid' => 1,
    'comment_count' => 0
  ),
  'node_revision' => array(
    'nid' => '[AMP_ID]',
    'vid' => '[AMP_ID]',
    'uid' => 1,
    'title' => '[AMP_TITLE]',
    'log' => '',
    'timestamp' => '[AMP_UPDATED]',
    'status' => 1,
    'comment' => '[AMP_COMMENTS]',
    'promote' => 0,
    'sticky' => 0
  ),
  //AMP ID
  'field_data_field_amp_id' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => 1,
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'field_amp_id_value' => '[AMP_ID]',
    'field_amp_id_format' => ''
  ),
  //AMP SECTION
  'field_data_field_amp_section' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => '[AMP_ID]',
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'field_amp_section_value' => '[AMP_SECTION]',
    'field_amp_section_format' => ''
  ),
  //ATTACHED DOCUMENT & IMAGE
  'file_managed' => array(
    'uid' => 1,
    'filename' => '[AMP_DOC]',
    'uri' => '[AMP_DOC_URL]',
    'filemime' => '[AMP_DOC_MIME]',
    'filesize' => '[AMP_DOC_FILESIZE]',
    'status' => 1,
    'timestamp' => '[AMP_DATECREATED]',
    'type' => '[AMP_DOC_TYPE]'
  ),
  'file_usage' => array(
    'fid' => '[DRUPAL_FID]',
    'module' => 'file',
    'type' => 'node',
    'id' => 1,
    'count' => 1
  ),
  'image_dimensions' => array(
    'fid' => '[DRUPAL_FID]',
    'width' => '[AMP_DOC_WIDTH]',
    'height' => '[AMP_DOC_HEIGHT]'
  ),
  //ATTACHED DOCUMENT
  'field_data_field_attached_document' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => 1,
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'field_attached_document_fid' => '[DRUPAL_FID]',
    'field_attached_document_display' => 1,
    'field_attached_document_description' => ''
  ),
  //AUTHOR
  'field_data_field_author' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => 1,
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'field_author_value' => '[AMP_AUTHOR]',
    'field_author_format' => ''
  ),
  //ATTACHED IMAGE
  'field_data_field_image' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => 1,
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'field_image_fid' => '[DRUPAL_FID]',
    'field_image_alt' => '[AMP_ALTTAG]',
    'field_image_title' => '[AMP_PICCAP]',
    'field_image_width' => '[AMP_DOC_WIDTH]',
    'field_image_height' => '[AMP_DOC_HEIGHT]',
  ),
  //BODY
  'field_data_body' => array(
    'entity_type' => 'node',
    'bundle' => '[ENTITY_TYPE]',
    'deleted' => 0,
    'entity_id' => '[AMP_ID]',
    'revision_id' => '[AMP_ID]',
    'language' => '[AMP_CUSTOM3]',
    'delta' => 0,
    'body_value' => '[AMP_TEST]',
    'body_summary' => '[AMP_SHORTDESC]',
    'body_format' => 'full_html'
/*  ),
  //MULTILINGUAL
  'entity_translation' => array(
    'entity_type' => 'node',
    'entity_id' => '[AMP_ID]',
    'revision_id' => '[AMP_ID]',
    'language' => '[AMP_CUSTOM3]',
    'source' => '[AMP_CUSTOM3]',
    'uid' => 1,
    'status' => 1,
    'translate' => 0,
    'created' => '[AMP_DATECREATED]',
    'changed' => '[AMP_UPDATED]'
  ),
  'entity_translation_revision' => array(
    'entity_type' => 'node',
    'entity_id' => '[AMP_ID]',
    'revision_id' => '[AMP_ID]',
    'language' => '[AMP_CUSTOM3]',
    'source' => 'en',
    'uid' => 1,
    'status' => 1,
    'translate' => 0,
    'created' => '[AMP_DATECREATED]',
    'changed' => '[AMP_UPDATED]'
 */  )
);
