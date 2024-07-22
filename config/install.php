<?php

include_once './database.php';

$enabled = false;

if ($enabled) {
  // get database connection
  $database = new Database();
  $db = $database->getConnection();

  $db_name = $database->db_name;



  // USERS *********************************
  $query_install_users = "CREATE TABLE `" . $db_name . "`.`users` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `username` VARCHAR(32) NOT NULL , `firstname` VARCHAR(32) NOT NULL , `lastname` VARCHAR(32) NOT NULL , `email` VARCHAR(64) NOT NULL , `phone` VARCHAR(32) NOT NULL , `password` VARCHAR(2048) NOT NULL , `role` VARCHAR(32) NOT NULL DEFAULT 'USER' , `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`))";


  $stmtUsers = $db->prepare($query_install_users);

  if ($stmtUsers->execute()) {
    echo "INSTALLED USERS\n";
  } else {
    echo "ERROR USERS\n";
  }



  // TURNS *********************************
  $query_install_turns = "CREATE TABLE `" . $db_name . "`.`turns` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `client_id` INT(11) NOT NULL , `client_name` VARCHAR(64) NOT NULL , `description` TEXT NOT NULL , `editedBy` VARCHAR(64) NOT NULL , `cost` DECIMAL(20,2) NOT NULL DEFAULT '0' , `item_ids` TEXT NOT NULL , `day` DATE NOT NULL , `startTime` TIME NOT NULL , `duration` VARCHAR(8) NOT NULL , `location` VARCHAR(16) NOT NULL , `status` VARCHAR(16) NOT NULL , PRIMARY KEY (`id`))";

  $stmtTurns = $db->prepare($query_install_turns);

  if ($stmtTurns->execute()) {
    echo "INSTALLED TURNS\n";
  } else {
    echo "ERROR TURNS\n";
  }

  // CLIENTS *********************************
  $query_install_clients = "CREATE TABLE `" . $db_name . "`.`clients` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL , `birthday` DATE NOT NULL , `dni` VARCHAR(10) NOT NULL , `phone` VARCHAR(32) NOT NULL , `phone_contact` VARCHAR(32) NOT NULL , `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`))";

  $stmtClients = $db->prepare($query_install_clients);

  if ($stmtClients->execute()) {
    echo "INSTALLED CLIENTS\n";
  } else {
    echo "ERROR CLIENTS\n";
  }

  // ITEMS ********************************
  $query_install_items = "CREATE TABLE `" . $db_name . "`.`items` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL , `type` VARCHAR(16) NOT NULL , `categories` TEXT NOT NULL , `description` TEXT NOT NULL , `cost` DECIMAL(20,2) NOT NULL DEFAULT '0' , `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`))";

  $stmtServices = $db->prepare($query_install_items);

  if ($stmtServices->execute()) {
    echo "INSTALLED ITEMS\n";
  } else {
    echo "ERROR ITEMS\n";
  }

  // CHARGES ********************************
  $query_install_charges = "CREATE TABLE `" . $db_name . "`.`charges` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `client_id` INT(11) NOT NULL , `client_name` VARCHAR(64) NOT NULL , `description` TEXT NOT NULL , `turn_id` INT(11) NOT NULL , `cost` DECIMAL(20,2) NOT NULL DEFAULT '0' , `status` VARCHAR(16) NOT NULL ,`date_created` DATETIME NOT NULL , PRIMARY KEY (`id`))";

  $stmtCharges = $db->prepare($query_install_charges);

  if ($stmtCharges->execute()) {
    echo "INSTALLED CHARGES\n";
  } else {
    echo "ERROR CHARGES\n";
  }
} else {
  http_response_code(404);

  // show error message
  echo json_encode(array("message" => "404 Not found."));
}
