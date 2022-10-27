
Skip to content
Pulls
Issues
Marketplace
Explore
@claudios2022
claudios2022 /
DVWAXperts
Public
forked from FortiLatam/DVWAXperts

Code
Pull requests
Actions
Projects
Wiki
Security
Insights

    Settings

DVWAXperts/vulnerabilities/sqli/source/impossible.php /
@FortiLatam
FortiLatam first push
Latest commit 0891695 on 9 Sep
History
1 contributor
65 lines (53 sloc) 1.75 KB
<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Check Anti-CSRF token
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'index.php' );

	// Get input
	$id = $_GET[ 'id' ];

	// Was a number entered?
	if(is_numeric( $id )) {
		$id = intval ($id);
		switch ($_DVWA['SQLI_DB']) {
			case MYSQL:
				// Check the database
				$data = $db->prepare( 'SELECT first_name, last_name FROM users WHERE user_id = (:id) LIMIT 1;' );
				$data->bindParam( ':id', $id, PDO::PARAM_INT );
				$data->execute();
				$row = $data->fetch();

				// Make sure only 1 result is returned
				if( $data->rowCount() == 1 ) {
					// Get values
					$first = $row[ 'first_name' ];
					$last  = $row[ 'last_name' ];

					// Feedback for end user
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
				break;
			case SQLITE:
				global $sqlite_db_connection;

				$stmt = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1;' );
				$stmt->bindValue(':id',$id,SQLITE3_INTEGER);
				$result = $stmt->execute();
				$result->finalize();
				if ($result !== false) {
					// There is no way to get the number of rows returned
					// This checks the number of columns (not rows) just
					// as a precaution, but it won't stop someone dumping
					// multiple rows and viewing them one at a time.

					$num_columns = $result->numColumns();
					if ($num_columns == 2) {
						$row = $result->fetchArray();

						// Get values
						$first = $row[ 'first_name' ];
						$last  = $row[ 'last_name' ];

						// Feedback for end user
						$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
					}
				}

				break;
		}
	}
}

// Generate Anti-CSRF token
generateSessionToken();

?>
Footer
© 2022 GitHub, Inc.
Footer navigation

    Terms
    Privacy
    Security
    Status
    Docs
    Contact GitHub
    Pricing
    API
    Training
    Blog
    About

DVWAXperts/impossible.php at master · claudios2022/DVWAXperts
