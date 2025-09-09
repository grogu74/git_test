<?php
$servername = "mysql";
$username = "grogu";
$password = "grogu74";
$dbname = "grogu_db";

$output = null;
$edit_item = null;

//Wenn ein todo geadded wird
if(isset($_POST["add_todo"])){

  $titel = $_POST['titel'];
  $beschreibung = $_POST['beschreibung'];

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "INSERT INTO todo (titel, beschreibung) VALUES ('$titel', '$beschreibung')";
      $conn->exec($query);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    
  }
}elseif(isset($_POST["edit"])){  // Auslesen des zu editierenden Todos
  $id = $_POST['id'];
 ;

  try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $edit_item = $conn->query("SELECT * FROM todo WHERE id = '$id'")->fetch(PDO::FETCH_ASSOC);

  
  // Datenbankverbindung schliessen
  $conn = null;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

}elseif(isset($_POST["save"])){ // Speichern des editierten Todos
  
  $id = $_POST['id'];
  $titel = $_POST['titel'];
  $beschreibung = $_POST['beschreibung'];

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "UPDATE todo SET titel = '$titel', beschreibung = '$beschreibung' WHERE id = '$id'";
      $conn->exec($query);
      // Datenbankverbindung schliessen
      $conn = null;

  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    
  }
}elseif(isset($_POST["done"])){ // Todo als erledigt markieren
  $id = $_POST['id'];

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "UPDATE todo SET status = 1 WHERE id = '$id'";
      $conn->exec($query);
      var_dump($query);
      // Datenbankverbindung schliessen
      $conn = null;

  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    
  }
}elseif(isset($_POST["undone"])){ // Todo als erledigt markieren
  $id = $_POST['id'];

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "UPDATE todo SET status = 0 WHERE id = '$id'";
      $conn->exec($query);
      // Datenbankverbindung schliessen
      $conn = null;

  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    
  }
}elseif(isset($_POST["delete"])){ // Todo löschen
  $id = $_POST['id'];

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "DELETE FROM todo WHERE id = '$id'";
      $conn->exec($query);
      // Datenbankverbindung schliessen
      $conn = null;

  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    
  }
}

// Datenbank Verbindungsaufbau und auslesen aller Todos
try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $todos = $conn->query("SELECT * FROM todo")->fetchAll(PDO::FETCH_ASSOC);

  foreach ($todos as $todo) {
    $id = $todo["id"];
    $titel = $todo['titel'];
    $beschreibung = $todo['beschreibung'];
    $status = $todo['status'];


    $done_Button = "";
    if ($status == 0) {
      $done_Button = '<button class="card-button" id="done" value="done" name="done">Done</button>';
    } elseif ($status == 1) {
      $done_Button = '<button class="card-button" id="undone" value="undone" name="undone">Completed</button>';
    }
    $output [] = <<<CARD
    <form method="POST">
      <div class="card">
        <div class="card-details">
          <input type="hidden" name="id" value="$id">
          <p class="text-title">$titel</p>
          <p class="text-body">$beschreibung</p>
        </div>
        <div class="buttons-grp">
        $done_Button
        <button class="delete-button" id="delete" value="delete" name="delete">X</button>
        <button class="card-button" id="edit" value="edit" name="edit">Edit</button>
        test
        
        </div>
        </div>
      </form>
    CARD;
  
   
  }
  // variabeln wieder zurücksetzen
  $id = null;
  $titel = null;
  $beschreibung = null; 
  $todo = null;

  // Datenbankverbindung schliessen
  $conn = null;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Todo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="title-container">
    <h1>Todo List</h1>
  </div>
  <div class="input-container">
    <?php 
    
    
    ?>
    <form method="POST">
      <input type="text" name="titel" placeholder="Titel" value="<?php echo $edit_item['titel'] ?? ''?>" required>
      <input type="text" name="beschreibung" placeholder="Beschreibung" value="<?php echo $edit_item['beschreibung'] ?? ''?>"  required>
      <input type="hidden" name="id" value="<?php echo $edit_item['id'] ?? ''?>">
    <?php
      if(isset($_POST["edit"])){
        echo "<button type='submit' name='save' value='Save'>Save</button> <a href='index.php'><small>Cancel</small></a>";

      }else {
        echo "<button type='submit' name='add_todo' value='add_todo'>Add Todo</button>";
      }
      ?>
        
    </form>
  </div>
  <div class="cards-container">
    <?php
    if(isset($output) && is_array($output)) {
      foreach ($output as $card) {
        echo $card;
      }
    } else {
      echo "<p>No todos found.</p>";
    }

    ?>
  </div>
</body>
</html>