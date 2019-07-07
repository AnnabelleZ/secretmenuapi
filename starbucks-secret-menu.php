<?php
/*
 This is a web service for retrieving Starbucks secret menu items
 from the manduann_starbuckssecretmenu database. We provide 3 services:
 fetch random drink, fetch by category, and search for drinks by keyword.
*/
  include 'common.php';

  # Use a get_PDO() function defined in common.php here.
  $db = get_PDO();

  if (isset($db)) {

	$output = null;

    if (isset($_GET["random"])) {
      if ($_GET["random"] == true) {
	    $output = fetch_random_drink($db);
	  } else {
		echo "Please remember to set random=true";
	  }
	} else if (isset($_GET["category"])) {
    if ($_GET["category"] == "all") {
      fetch_all_categories($db);
    } else {
	    $output = fetch_by_category($db, $_GET["category"]);
    }
	} else if (isset($_GET["keyword"])) {
	  $output = fetch_by_keyword($db, $_GET["keyword"]);
	} else {
	  echo "Please set either random, category, or keyword";
	}

    if (isset($output)) {
      header("Content-Type: application/json");

      # encode the output array as json
      echo json_encode($output);
    }
  }

  /**
  *  Print out all the categories in plain text
  *  @param {object} $db - the PDO object representing the db connection
  */
  function fetch_all_categories($db) {
    try {
      # This is the PHP/SQL connection! :)
      $rows = $db->query("SELECT DISTINCT category FROM SecretMenu ORDER BY category ASC;");

    }
    catch (PDOException $ex) {
      error_db_message("Can not query the database.");
    }

    header("Content-Type: text/plain");
    # loop through each row of data from the select
    # adding it to the output array
    foreach ($rows as $row){
      echo $row["category"] . "\n";
    }
  }

   /**
   *  Return all of the secret recipes whose name matches the keyword
   *  @param {object} $db - the PDO object representing the db connection
   *  @param {string} $keyword - the keyword used to query the db
   *  @return {array} of the secretmenu table rows with all of the information
   *  of the secret recipe in each row
   */
  function fetch_by_keyword($db, $keyword) {
  $output = null;

    try {
      # This is the PHP/SQL connection! :)
      $sql = "SELECT * FROM SecretMenu WHERE LOWER(name) LIKE ? ORDER BY name ASC;";
	  $stmt = $db->prepare($sql);
	  $stmt->execute(["%" . strtolower($keyword) . "%"]);
	  $rows = $stmt->fetchall();
    }
    catch (PDOException $ex) {
      error_db_message("Can not query the database.");
    }

    $output = array();

    # loop through each row of data from the select
    # adding it to the output array
    foreach ($rows as $row){
	  $record = array();
	  $record["name"] = $row["name"];
	  $record["recipe"] = $row["recipe"];
	  $record["category"] = $row["category"];
	  $record["images"] = $row["images"];
	  array_push($output, $record);
    }
    return $output;
  }

  /**
   *  Return all of the secret recipes of a category
   *  @param {object} $db - the PDO object representing the db connection
   *  @param {string} $categ - the category
   *  @return {array} of the secretmenu table rows with all of the information
   *  of the secret recipe in each row
   */
  function fetch_by_category($db, $categ) {
    # select all rows from the queue table in the wpl database.
    $output = null;

    try {
      # This is the PHP/SQL connection! :)
      $sql = "SELECT * FROM SecretMenu WHERE LOWER(category)=? ORDER BY name ASC;";
	  $stmt = $db->prepare($sql);
	  $stmt->execute([strtolower($categ)]);
	  $rows = $stmt->fetchall();
    }
    catch (PDOException $ex) {
      error_db_message("Can not query the database.");
    }

    $output = array();

    # loop through each row of data from the select
    # adding it to the output array
    foreach ($rows as $row){
	  $record = array();
	  $record["name"] = $row["name"];
	  $record["recipe"] = $row["recipe"];
	  $record["category"] = $row["category"];
	  $record["images"] = $row["images"];
	  array_push($output, $record);
    }
    return $output;
  }

  /**
   *  Return all of the entries currently in the WPL queue.
   *  @param {object} $db - the PDO object representing the db connection
   *  @return {array} of all of the information of a random secret drink
   */
  function fetch_random_drink($db) {
    $output = null;

    try {
      # This is the PHP/SQL connection! :)
      $rows = $db->query("SELECT * FROM SecretMenu ORDER BY RAND() LIMIT 1;");

    }
    catch (PDOException $ex) {
      error_db_message("Can not query the database.");
    }

    $output = array();

    # loop through each row of data from the select
    # adding it to the output array
    foreach ($rows as $row){
	  $output["name"] = $row["name"];
	  $output["recipe"] = $row["recipe"];
	  $output["category"] = $row["category"];
	  $output["images"] = $row["images"];
    }
    return $output;

  }
?>
