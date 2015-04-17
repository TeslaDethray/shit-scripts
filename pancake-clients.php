<?php
//Moves client data from GTD to Pancake app

require("db.php");

$db_gtd = new db('gtd');
$companies = $db_gtd->query('SELECT * FROM `company`;');

$db_pancake = new db('pancake');
foreach($companies as $company) {
  $company_info = [
    'id' => $company['id'],
    'first_name' => $company['first_name'],
    'last_name' => $company['last_name'],
    'title' => ($company['alias'] == '') ? '' : $company['alias'],
    'email' => $company['email'],
    'company' => $company['name'],
    'address' => $company['address'],
    'phone' => $company['phone'],
    'fax' => '',
    'mobile' => '',
    'website' => '',
    'language' => 'english',
    'business_identity' => 1,
    'can_create_support_tickets' => 1,
    'profile' => $company['notes'],
    'unique_id' => random_string(), 
    'passphrase' => '',
    'created' => ($company['date_started'] == '0000-00-00') ? date('Y-m-d H:i:s') : $company['date_started'] . ' 00:00:00',
    'support_user_id' => 1,
    'modified' => date('Y-m-d H:i:s'),
    'owner_id' => 5
  ];
  $tax_info = [
    'client_id' => $company['id'],
    'tax_id' => 1,
    'tax_registration_id' => ''
  ];

  echo insert_query($company_info, 'pancake_clients') . "\n";
  echo insert_query($tax_info, 'pancake_clients_taxes') . "\n";
}

function insert_query($array, $table_name) {
  return str_replace("<br>", "\n", 'INSERT INTO `' . $table_name . '` (`' . implode('`, `', array_keys($array)) . '`) VALUES ("' . implode('", "', $array) . '");');
}

function random_string($num_chars = 8) {
  $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $randstring = '';
  for ($i = 0; $i < $num_chars; $i++) {
    $randstring .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $randstring;
}
