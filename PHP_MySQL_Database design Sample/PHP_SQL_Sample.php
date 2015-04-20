<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Amma's Kitchen - Recipes</title>
	<link href='http://fonts.googleapis.com/css?family=Bad+Script' rel='stylesheet' type='text/css'>
        <link rel = "stylesheet" type = "text/css" href = "../css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="../js/recipes.js"></script>
    </head>
    
    <body>
        
        <?php require '../navigation.php' ?>
	<?php require '../config.php' ?>

	
	<h1 class = "inner_h1">Recipes</h1>
	<h3 class = "inner_h3">View our collection of tantalizing recipes. The recipes will
	be broken up by album, each of which will describe the recipe group. For example "Curries,"
	or "Dessert."</h3>
	        
        <?php
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	//Show all album titles
	$result = $mysqli->query("SELECT album_ID, title FROM album");
	$title = "";
	while (($row = $result->fetch_assoc()) && !isset($_GET["album_ID"])) {
	    echo "<h2>";
	    echo "<a class = 'album_click' id = '" . $row['album_ID'] . "' href = '?album_ID=" .
		$row['album_ID'] . "'>";
	    echo $row['title'];
	    echo "</a></h2>";
	    $title = $row['title'];
	}
	
	//Show album details
	if (isset($_GET["album_ID"])) {
	    $album_ID = strip_tags(htmlspecialchars($_GET["album_ID"]));
	    $result = $mysqli->query("SELECT image_ID, image_url, caption, title FROM album INNER
				     JOIN (SELECT DISTINCT album_ID, image.image_ID, image_url, caption
				     FROM image INNER JOIN  (SELECT * FROM imageInalbum WHERE album_ID
				     = $album_ID) AS TempTable ON image.image_ID = TempTable.image_ID)
				     AS TempTable2 ON album.album_ID = TempTable2.album_ID");
	    if ($result) {
		$row = $result->fetch_row();
		echo "<div><h2 id = 'breadcrumb'><a href = 'recipes.php'>Recipes</a> > " . $row[3] . "</h2></div>";
		mysqli_data_seek($result, 0); //set result pointer back to 0
		$i = 0;
		while ($row = $result->fetch_assoc()) {
		    $i += 1;
		    if ($row != null) {
			echo "<div class = 'entry'>";
			echo "<img src = '" . $row['image_url'] . "' alt = 'food' height = '200' width ='250'>" . "<br>";
			echo $row['caption'];
			echo "</div>";
		    }
		}
		if ($i == 0) {
		    echo "<div class = 'no_entry'><h3> There are no photos in this album. </h3> </div>";
		}
	    }
	}
	
	?>
	
    </body>
</html>
	
	
	