<?php
  session_start();
  $count = 0;
  // connecto database
  require_once "./functions/database_functions.php";
  $conn = db_connect();
  if(isset($_POST['title'])){
    if(isset($_POST['asc'])){
      $query = "SELECT * FROM books order by book_title asc";

    }
    else if(isset($_POST['desc'])){
      $query = "SELECT * FROM books order by book_title desc";
    }else{
      $query = "SELECT * FROM books";
    }
  }else if(isset($_POST['price'])){
    if(isset($_POST['asc'])){
      $query = "SELECT * FROM books order by book_price asc";

    }
    else if(isset($_POST['desc'])){
      $query = "SELECT * FROM books order by book_price desc";
    }else{
      $query = "SELECT * FROM books";
    }
  }else if(isset($_POST['author'])){
    if(isset($_POST['asc'])){
      $query = "SELECT * FROM books order by book_author asc";

    }
    else if(isset($_POST['desc'])){
      $query = "SELECT * FROM books order by book_author desc";
    }else{
      $query = "SELECT * FROM books";
    }
  }else{
    $query = "SELECT * FROM books";
  }

  $result = mysqli_query($conn, $query);
  $title = "Full Catalogs of Books";
    require_once "./template/header.php";
?>

  <p class="lead text-center text-muted">Full Catalogs of Books</p>

<br><br>

    <?php for($i = 0; $i < mysqli_num_rows($result); $i++){ ?>
      <div class="row">
        <?php while($query_row = mysqli_fetch_assoc($result)){ ?>
          <div class="col-md-3">
            <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>">
              <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>">
              </a>
              <table>
                <tr>
                  <td><strong>  <?php echo $query_row['book_title']; ?></strong></td>
                </tr>
                <tr>
                <td> <?php echo $query_row['book_author']; ?> <br>
                <?php echo $query_row['type']; ?>
              </td>
                </tr>
                <tr>
                <td><strong><?php echo $query_row['book_price'];?></strong>  </td>
                </tr>
              </table>
            </div>
        <?php
          $count++;
          if($count >= 4){
              $count = 0;
              break;
            }
          } ?> 
      </div>
      <br><br>
<?php
      }
  if(isset($conn)) { mysqli_close($conn); }
  require_once "./template/footer.php";
?>

 <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
