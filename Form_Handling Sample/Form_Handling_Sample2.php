<?php
    /**Checks if $field is set in the form. If $field is set, returns value of $field. Otherwise, returns empty string.*/
    function get_if_set($field) {
      if (isset($_POST[$field])) {
        return ($_POST[$field]);
      }
      else {
        return "";
      }
    }
?>
<!DOCTYPE html>
<html>

    <?php require '../navigation.php'; ?>
    
    <body>
        <?php
	    require '../config.php';
	    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	?>
	
	<?php
	    $album_success = FALSE;

	    
	    $title_error = "";
	    $title = strip_tags(htmlspecialchars(get_if_set('album_title')));
	    
	    $caption_error = "";
	    $caption = strip_tags(htmlspecialchars(get_if_set('caption')));
	    
	    $date_taken_error = "";
	    $date_taken = strip_tags(htmlspecialchars(get_if_set('date_taken')));
	    $date_taken = date('Y-m-d', strtotime(str_replace('-', '/', $date_taken))); 
	    
	    $album_error =  "";
	    $album = strip_tags(htmlspecialchars(get_if_set('foodtype')));
	    
	    $price_error = "";
	    $price = strip_tags(htmlspecialchars(get_if_set('price')));
	    
	    $time_error = "";
	    $time = strip_tags(htmlspecialchars(get_if_set('time')));
	    
	    $ingredients_error = "";
	    $ingredients = strip_tags(htmlspecialchars(get_if_set('ingredients')));
	    
	    $recipe_error = "";
	    $recipe = strip_tags(htmlspecialchars(get_if_set('recipe')));
	    
	    $album_list = array();
	    $result = $mysqli->query("SELECT title FROM album");
	    while ($row = $result->fetch_assoc()) {
		array_push($album_list, $row['title']);
	    }
	?>
	
	<?php
	    //Handle an album addition
	    if (isset($_POST['album_submit'])) {
		$title = strip_tags(htmlspecialchars(trim(get_if_set('album_title'))));
		$album_success = TRUE;
		
		if (strlen($title) > 35) {
		    $title_error = "*Please provide an album title 35 characters or less.";
		}
		
		if (in_array($title, $album_list)) {
		    $title_error = "*The album title you have provided already exists. Please provide another title.";			
		}
		
		if (empty($title)) {
		    $title_error = "*Please tell us the album's title.";
		}
		
	    }
	    
	    if ($album_success && empty($title_error)) {
		$result = $mysqli->query("INSERT INTO album (title, date_created, date_modified) VALUES ('$title', CURDATE(), CURDATE())");
	    }
	    
	    //Handle a photo addition
	    if (isset($_POST['photo_submit'])) {
		$temp = explode(".", $_FILES["newphoto"]["name"]);
		$extension = end($temp);

		if (($_FILES["newphoto"]["type"] == "image/gif" || $_FILES["newphoto"]["type"] == "image/jpeg"
		     || $_FILES["newphoto"]["type"] == "image/png")
		     && ($extension == "gif" || $extension == 'png' || $extension == 'jpg')) {
		    if ($_FILES["newphoto"]["error"] > 0) {
			echo "Error: ".$_FILES["newphoto"]["error"]."<br>";
		    }
		    else { 
			if (!empty($_FILES['newphoto'])) {
			    $newPhoto = $_FILES['newphoto'];
			    $originalName = $newPhoto['name'];
			    if ($newPhoto['error'] == 0) {
				$tempName = $newPhoto['tmp_name'];
				move_uploaded_file($tempName, "../images/$originalName");
				$image_url = "../images/$originalName";
				$result = $mysqli->query("INSERT INTO image (image_url, caption, date_taken,
							 foodtype, price, time, ingredients, recipe) VALUES ('$image_url',
							 '$caption', '$date_taken', '$album', '$price', '$time', '$ingredients', '$recipe')");
				
				$image_ID = $mysqli->insert_id;
				$album_ID = "";
				$result = $mysqli->query("SELECT album_ID FROM album WHERE Title = '$album'");		
				while ($row = $result->fetch_assoc()) {
				    $album_ID = $row['album_ID'];
				}
				$result = $mysqli->query("INSERT INTO imageInalbum VALUES ('$image_ID', '$album_ID')");
			    }
			}
			else {
			    print("Error: The file $originalName was not uploaded successfully.");
			}
		    }
		}
		else { 
		    echo "Invalid file"; 
		}
	    }

	?>
	
	<ul id = "navigation">
	    <li><a href = "../index.php">Home</a></li>
	    <li><a href = "recipes.php">Recipes</a></li>
	    <li><a href = "signIn.php">Sign In</a></li>
	    <li><a href = "add.php">Add</a></li>
	</ul>
	
	<h1 class = "inner_h1">Add</h1>
	<h3 class = "inner_h3"> Add a recipe to our collection of recipes. In order to
        do this, you must have an image of the recipe. You will fill out a simple form with an image of
        the recipe, a caption, and any other information you would like to supply.</h3>
        
	<div id = "album_add">
	    <h2>Add an Album: </h2>
	    <form action = "add.php" method="POST">
		Album Title: <input type = "text" maxlength="35" name="album_title">
		<?php if (!empty($title_error)) { echo("<div class = 'error'>" . $title_error . "</div>"); } ?>
		<br> <input type="submit" value="Upload Album" name="album_submit">
	    </form>
	</div>
	
	<div id = "image_add">
	    <h2>Add a recipe:</h2>
	    <form action="add.php" method="post" enctype="multipart/form-data">
		Recipe photo: <input type="file" name="newphoto"> <br>
		Caption: <input type = "text" maxlength="200" name="caption"> <br>
		Date Taken: <input type="date" name="date_taken"> <br>
		Recipe Album:<select name="foodtype"><br>
		    <?php
			foreach ($album_list as $key => $value){
			    echo "<option value='" . $value . "'>" . $value . "</option>";
			}
		    ?>
		</select> <br>
		Price Estimate (USD):<input type="number" name="price"> <br>
		Cook Time (in hours): <input type="number" name="time"> <br>
		Ingredients: <textarea maxlength="600" name = "ingredients"></textarea> <br>
		Recipe: <textarea maxlength="2000" name = "recipe"></textarea> <br>
		<input type="submit" value="Upload photo" name = "photo_submit"/>
	    </form> 
	</div>
    </body>
</html>