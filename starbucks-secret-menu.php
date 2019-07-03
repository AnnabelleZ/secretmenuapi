<?php
/*
 This is a web service for retrieving Starbucks secret menu items
 from the manduann_starbuckssecretmenu database. We provide 3 services:
 fetch random drink, fetch by category, and search a drink.
*/
  include 'common.php';

  # Use a get_PDO() function defined in common.php here.
  echo "hello";
  $db = get_PDO();
  echo "world";
  if (isset($db)) {
    $output = fetch_random_drink($db);

    if (isset($output)) {
      header("Content-Type: application/json");

      # encode the output array as json
      echo json_encode($output);
    }
  }

  /**
   *  Return all of the entries currently in the WPL queue.
   *  @param {object} $db - the PDO object representing the db connection
   *  @return {array} of the queue table rows with all of the information
   *  in each row
   */
  function fetch_random_drink($db) {
    # select all rows from the queue table in the wpl database.
    $output = null;

    try {
      # This is the PHP/SQL connection! :)
      $record = $db->query("SELECT * FROM SecretMenu ORDER BY RAND() LIMIT 1;");
    }
    catch (PDOException $ex) {
      error_db_message("Can not query the database.");
    }

    $output = array();

    # loop through each row of data from the select
    # adding it to the output array
    #foreach($rows as $row){
    $output["name"] = $record["name"];
    $output["recipe"] = $record["recipe"];
    $output["category"] = $record["category"];
    $output["images"] = $record["images"];
    #}

    return $output;

  }
?>
