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

<?php
    $delimiter = ' | ';
    
    $title = strip_tags(trim(get_if_set("title")));
    $description = strip_tags(trim(get_if_set("description")));
    $date = strip_tags(trim(get_if_set("date")));
    $contact = strip_tags(trim(get_if_set("contact")));
    $email = strip_tags(trim(get_if_set("email")));
    
    $success = "";
    $title_error = "";
    $description_error = "";
    $date_error = "";
    $contact_error = "";
    $email_error = "";
    $error_free = "";
    
    if (isset($_POST['submit'])) {
        
        $success = TRUE;
        
        if (empty($title)) {
              $title_error = "*Please tell us the program's title.";
        }
        
        if (strlen($title) > 25) {
            $title_error = "*Please provide a program title 25 characters or less.";
        }
        
        if (empty($description)) {
            $description_error = "*Please provide a brief program description.";
        }
        
        if (strlen($description) > 140) {
            $description_error = "*Please provide a program description 140 characters or less.";
        }
        
        if (empty($email)) {
            $email_error = "*Please enter a valid e-mail address.";
        }
            
        if (empty($date)) {
            $date_error = "*Please enter the date of the program.";
        }
        
        if (strlen($email) > 35) {
                $email_error = "Please enter an e-mail less than or equal to 25 characters.";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = "*Please enter a valid e-mail address.";
        }
        
        if (empty($contact)) {
                $contact_error = "*Please select your position.";
        }
        
        if (strlen($contact) > 25) {
                $contact_error = "Please enter a contact less than or equal to 25 characters.";
        }
        
        if (is_numeric($contact)) {
              $contact_error = "*Please enter a valid contact without numeric digits.";
        }
        
        if (!(preg_match("/[A-Z][a-z]*\s[A-Z]'*[A-Z]*[a-z]+/", $contact))) {
                $contact_error = "*Please enter a valid contact of the form 'First Last'.";
        }
                
        if ($success && empty($title_error) && empty($description_error) && empty($date_error) && empty($email_error) && empty($contact_error)) {
            
            $file = fopen("data.txt", "a+");
        
            if (!$file) {
                echo("There was a problem opening the data.txt file");
            }
            
            else {
                $error_free = "Thank you for adding your event! You can now view the event under events roster.";
                
                //Write the event's title, description, date, contact, and e-mail to the file. 
                fputs($file, "$title$delimiter$description$delimiter$date$delimiter$contact$delimiter$email$delimiter\n");
                
                fclose($file);
            }
        }
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>CCP Events</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/events.js"></script>
    </head>
</html>

 
<body>
    <ul id = "navigation">
        <li><a href = "../index.php">HOME</a></li>
        <li><a href = "about.php">ABOUT</a></li>
        <li><a href = "events.php">EVENTS</a></li>
        <li><a href = "contact.php">CONTACT</a></li>
    </ul>  
  
    <h3>Search for a CCP event:</h3>
  <form action="" method="post">
    <label>Title:</label> <input type="text" name="ts"> <br>
    <label>Event Description:</label><textarea rows = "5" name="ds"></textarea> <br>
    <label>Date:</label><input type="date" name="dts"> <br>
    <label>Contact Person:</label><input type="text" name="cs"> <br>
    <label>E-mail:</label> <input type="text" name="es">
    
    <input type="submit" name="search" value="search"/>
  </form>
<?php
    if (isset($_POST['search'])) {
                
        $ts = trim(get_if_set("ts"));
        $ds = trim(get_if_set("ds"));
        $dts = trim(get_if_set("dts"));
        $cs = trim(get_if_set("cs"));
        $es = trim(get_if_set("es"));
        
        $file = fopen("data.txt", "r");
        if (!$file) {
            die("There was a problem opening the data.txt file");
        }
                
        $events = array();
        $events = file("data.txt");
        
        foreach ($events as $event) {
            //Make a copy of each search field variable
            $tscopy = $ts;
            $dscopy = $ds;
            $dtscopy = $dts;
            $cscopy = $cs;
            $escopy = $es;
            
            //Make an array of the event fields
            $info = explode($delimiter, $event); 
            
            if ($ts == "") {
                $tscopy = $info[0];
            }
            if ($ds == "") {
                $dscopy = $info[1];
            }
            if ($dts == "") {
                $dtscopy = $info[2];
            }
            if ($cs == "") {
                $cscopy = $info[3];
            }
            
            if ($es == "") {
                $escopy = $info[4];
            }
            
            if (($info[0] == $tscopy) && ($info[1] == $dscopy) && ($info[2] == $dtscopy)
                && ($info[3] == $cscopy) && ($info[4] == $escopy)) {
                    
                echo 
                ("<ul>
                      <li>Title: $info[0]</li>
                      <li>Description: $info[1]</li>
                      <li>Date: $info[2]</li>
                      <li>Contact: $info[3]</li>
                      <li>E-mail: $info[4]</li>
                  </ul>");
            }
        }
      }

?>

  <h1>Community Center Programs Events</h1>
  
  <?php echo("<h3>$error_free</h3>" . "<br>"); ?>
  
  <form action="" method="post">
    Title: <input type="text" name="title" maxlength="25" value = "<?php echo($title); ?>" onchange="validTitle(title);">
    
    <?php echo("<span class=\"error\">$title_error</span>"); ?> <br>
    
    Event Description: <textarea rows = "5" name="description" maxlength = "140"><?php echo($description); ?></textarea> <br>
    <?php echo("<span class=\"error\">$description_error</span>"); ?> <br>

    Date: <input type="date" name="date" value = "<?php echo($date); ?>"> <br>
    <?php echo("<span class=\"error\">$date_error</span>"); ?> <br>
    
    Contact Name: <input type="text" name="contact" maxlength="25" value = "<?php echo($contact); ?>"> <br>
    <?php echo("<span class=\"error\">$contact_error</span>"); ?> <br>
    
    Contact E-mail: <input type="text" name="email" maxlength = "35" value = "<?php echo($email); ?>"> <br>
    <?php echo("<span class=\"error\">$email_error</span>"); ?> <br>
    
    <input type="submit" name="submit" value="submit" />

  </form>
  
  <?php
    //Get the contents of the text file as an array
    $events = array();
    if (file_exists ("data.txt")) {
      $events = file("data.txt");
    }
    
    //Use for each to loop through each guest
    //file as save each element in the array temporarily as $guest 
    foreach($events as $event){
      
      $info = explode($delimiter, $event);

      echo 
            ("<ul>
                  <li>Title: $info[0]</li>
                  <li>Description: $info[1]</li>
                  <li>Date: $info[2]</li>
                  <li>Contact: $info[3]</li>
                  <li>E-mail: $info[4]</li>
              </ul>");
        }
      
  ?>

</body>

</html>

