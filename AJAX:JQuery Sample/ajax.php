<?php

	$request_type = filter_input(INPUT_POST, "requestType", FILTER_SANITIZE_STRING);
	if (empty( $request_type )) {
		$request_type = filter_input(INPUT_GET, "requestType", FILTER_SANITIZE_STRING);
	}
	if ( empty( $request_type ) ) {
		echo 'Missing requestType.';
		die();
	}

	require_once "../config.php";
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	if ($mysqli->errno) {
		print($mysqli->error);
		die();
	}
		
	switch ( $request_type ) {
		case "checkName":
			//Get the POST data
			$firstName = strip_tags(filter_input(INPUT_POST, "firstName", FILTER_SANITIZE_STRING));
			$lastName = strip_tags(filter_input(INPUT_POST, "lastName", FILTER_SANITIZE_STRING));
			$school = strip_tags(filter_input(INPUT_POST, "school", FILTER_SANITIZE_STRING));

			//Build and execute the query
			$query = "SELECT * FROM Wizards WHERE firstName='$firstName' AND lastName='$lastName' AND school='$school'";
			$result = $mysqli->query($query);

			if( !$result ) {
				echo 'Query error';
				die();
			}

			//Return a message indicating whether there are matching wizards
			if ( $result->num_rows == 0) {
				echo 'NoDuplicates';
			} else {
				echo 'Duplicates';
			}
			die();
			break;
		
			
		case "submitName":
			$firstName = trim(htmlspecialchars(strip_tags(filter_input(INPUT_POST, "firstName", FILTER_SANITIZE_STRING))));
			$lastName = trim(htmlspecialchars(strip_tags(filter_input(INPUT_POST, "lastName", FILTER_SANITIZE_STRING))));
			$school = trim(htmlspecialchars(strip_tags(filter_input(INPUT_POST, "school", FILTER_SANITIZE_STRING))));
			
			//Server-Side Validation
			if (empty($firstName) || empty($lastName) || empty($school)) {
				echo 'Please do not leave any fields empty.';
				die();
			}
			
			if (!ctype_alpha($firstName) || !ctype_alpha($lastName) || !ctype_alpha($school)) {
				echo 'Please use only alphabetical characters.';
				die();
			}
			
			if (strlen($firstName) > 30 || strlen($lastName) > 30 || strlen($school) > 30) {
				echo 'Please keep all fields below 30 characters.';
				die();
			}
			

			
			//Build and execute the query
			$query = "INSERT INTO Wizards (firstName, lastName, school) VALUES ('$firstName', '$lastName', '$school')";
			$result = $mysqli->query($query);
			
			if( !$result ) {
				echo 'error';
				die();
			}
			
			else {
				echo 'success';
			}
			
			break;

		case "chooseName":
			//Build and execute the query
			$query = "SELECT * FROM Wizards ORDER BY RAND() LIMIT 1";
			$result = $mysqli->query($query);
			
			$firstName = "";
			$lastName = "";
			$school = "";
			
			if(!$result ) {
				echo 'Query error';
				die();
			}
			
			else {
				while ($row = $result->fetch_assoc()) {
					$firstName = $row['firstName'];
					$lastName = $row['lastName'];
					$school = $row['school'];
				}
				$query = "DELETE FROM Wizards WHERE firstName = '$firstName' AND lastName = '$lastName' AND school = '$school'";
				$result = $mysqli->query($query);
				if ($result) {
					echo "$firstName $lastName of $school";
				}
				else {
					echo "Failed to Execute query.";
				}
			}
			
			break;
	
	
	}




?>