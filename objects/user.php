<?php
// 'user' object
/*
User Model:

{
  id,
  username: string(128),
  lastname: string(128),
  firstname: string(128),
  email: string(128),
  password: string(2048),
  role: string(256),
  phone: string(64),
  create: datetime,
}

*/

class User
{

  // database connection and table name
  private $conn;
  private $table_name = "users";

  // object properties
  public $id;
  public $username;
  public $firstname;
  public $lastname;
  public $email;
  public $phone;
  public $password;
  public $role = "USER";

  public $collection;
  public $search;
  public $page = 0;
  public $pagination = 10;
  public $orderBy = 'created';
  public $orderTo = 'DESC';

  // constructor
  public function __construct($db)
  {
    $this->conn = $db;
  }

  // create new user record
  public function create()
  {

    // insert query
    $query = "INSERT INTO " . $this->table_name . "
            SET
                username = :username,
                firstname = :firstname,
                lastname = :lastname,
                role = :role,
                email = :email,
                phone = :phone,
                password = :password";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->firstname = htmlspecialchars(strip_tags($this->firstname));
    $this->lastname = htmlspecialchars(strip_tags($this->lastname));
    $this->role = htmlspecialchars(strip_tags($this->role));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->phone = htmlspecialchars(strip_tags($this->phone));
    $this->password = htmlspecialchars(strip_tags($this->password));

    // bind the values
    $stmt->bindParam(':username', $this->username);
    $stmt->bindParam(':firstname', $this->firstname);
    $stmt->bindParam(':lastname', $this->lastname);
    $stmt->bindParam(':role', $this->role);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':phone', $this->phone);

    // hash the password before saving to database
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password_hash);

    // execute the query, also check if query was successful
    if ($stmt->execute()) {
      $last_insert_id = $this->conn->lastInsertId();
      $this->id = $last_insert_id;
      return true;
    }

    return false;
  }

  // check if given username exist in the database
  public function setByUsername()
  {

    // query to check if email exists
    $query = "SELECT id, email, firstname, lastname, phone, role, password
          FROM " . $this->table_name . "
          WHERE username = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->username = htmlspecialchars(strip_tags($this->username));

    // bind given email value
    $stmt->bindParam(1, $this->username);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if email exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {

      // get record details / values
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // assign values to object properties
      $this->id = $row['id'];
      $this->email = $row['email'];
      $this->firstname = $row['firstname'];
      $this->lastname = $row['lastname'];
      $this->phone = $row['phone'];
      $this->role = $row['role'];
      $this->password = $row['password'];

      // return true because email exists in the database
      return true;
    }

    // return false if email does not exist in the database
    return false;
  }

  // check if given email exist in the database
  public function setById()
  {

    // query to check if id exists
    $query = "SELECT username, firstname, lastname, phone, role, email
          FROM " . $this->table_name . "
          WHERE id = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind given id value
    $stmt->bindParam(1, $this->id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if id exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {
      // get record details / values
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // assign values to object properties      
      $this->username = $row['username'];
      $this->firstname = $row['firstname'];
      $this->lastname = $row['lastname'];
      $this->role = $row['role'];
      $this->email = $row['email'];
      $this->phone = $row['phone'];

      // return true because id exists in the database
      return true;
    }

    // return false if email does not exist in the database
    return false;
  }
  public function existById()
  {

    // query to check if email exists
    $query = "SELECT role
          FROM " . $this->table_name . "
          WHERE id = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind given email value
    $stmt->bindParam(1, $this->id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if email exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {
      // return true because email exists in the database
      return true;
    }

    // return false if email does not exist in the database
    return false;
  }

  // update a user record
  public function update()
  {
    // if no posted, do not update
    $query = "UPDATE " . $this->table_name . " SET";
    $query .= !empty($this->username) ? " username=:username," : "";
    $query .= !empty($this->firstname) ? " firstname=:firstname," : "";
    $query .= !empty($this->lastname) ? " lastname=:lastname," : "";
    $query .= !empty($this->role) ? " role=:role," : "";
    $query .= !empty($this->email) ? " email=:email," : "";
    $query .= !empty($this->phone) ? " phone=:phone," : "";
    $query .= !empty($this->password) ? " password=:password," : "";

    $query = substr($query, 0, -1);

    $query .= " WHERE id=:id";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    if (!empty($this->username)) {
      $this->username = htmlspecialchars(strip_tags($this->username));
      $stmt->bindParam(':username', $this->username);
    }
    if (!empty($this->firstname)) {
      $this->firstname = htmlspecialchars(strip_tags($this->firstname));
      $stmt->bindParam(':firstname', $this->firstname);
    }
    if (!empty($this->lastname)) {
      $this->lastname = htmlspecialchars(strip_tags($this->lastname));
      $stmt->bindParam(':lastname', $this->lastname);
    }
    if (!empty($this->role)) {
      $this->role = htmlspecialchars(strip_tags($this->role));
      $stmt->bindParam(':role', $this->role);
    }
    if (!empty($this->email)) {
      $this->email = htmlspecialchars(strip_tags($this->email));
      $stmt->bindParam(':email', $this->email);
    }
    if (!empty($this->phone)) {
      $this->phone = htmlspecialchars(strip_tags($this->phone));
      $stmt->bindParam(':phone', $this->phone);
    }

    // hash the password before saving to database
    if (!empty($this->password)) {
      $this->password = htmlspecialchars(strip_tags($this->password));
      $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
      $stmt->bindParam(':password', $password_hash);
    }

    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);

    // execute the query
    if ($stmt->execute()) {
      return true;
    }

    return $query;
  }

  public function delete()
  {

    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind id of record to delete
    $stmt->bindParam(1, $this->id);

    // execute query
    if ($stmt->execute()) {
      return true;
    }

    return false;
  }
  function getTotals()
  {
    $search_set = !empty($this->search) ? " WHERE p.username LIKE ? OR p.firstname LIKE ? OR p.lastname LIKE ? OR p.email LIKE ? " : "";
    $query = "SELECT p.id FROM " . $this->table_name . " p" . $search_set;
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    if (!empty($this->search)) {
      // sanitize
      $keywords = htmlspecialchars(strip_tags($this->search));
      $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
      $stmt->bindParam(2, $keywords);
      $stmt->bindParam(3, $keywords);
      $stmt->bindParam(4, $keywords);
    }
    if ($stmt->execute()) {
      // get number of rows
      $num = $stmt->rowCount();
      return $num;
    }
    return 0;
  }
  public function list()
  {

    $r_initial = $this->pagination * $this->page;
    $r_final = $this->pagination;

    $search_set = !empty($this->search) ? " WHERE p.username LIKE ? p.firstname LIKE ? OR p.lastname LIKE ? OR p.email LIKE ? " : "";

    // select all query
    $query = "SELECT p.id, p.username, p.firstname, p.lastname, p.role, p.email, p.phone, p.created FROM " . $this->table_name . " p " . $search_set . "ORDER BY p." . $this->orderBy . " " . $this->orderTo . " LIMIT " . $r_initial . "," . $r_final;

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    if (!empty($this->search)) {
      // sanitize
      $keywords = htmlspecialchars(strip_tags($this->search));
      $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
      $stmt->bindParam(2, $keywords);
      $stmt->bindParam(3, $keywords);
      $stmt->bindParam(4, $keywords);
    }

    // execute the query
    if ($stmt->execute()) {
      // get number of rows
      $num = $stmt->rowCount();

      $collection_arr = array();
      $collection_arr["elementsTotal"] = $this->getTotals();
      $collection_arr["elementsInPage"] = $num;
      if (!empty($this->search)) {
        $collection_arr["search"] = $this->search;
      }
      $collection_arr["page"] = $this->page;
      $collection_arr["pagination"] = $this->pagination;
      $collection_arr["orderBy"] = $this->orderBy;
      $collection_arr["orderTo"] = $this->orderTo;
      $collection_arr["data"] = array();

      if ($num > 0) {

        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // extract row
          // this will make $row['name'] to
          // just $name only
          extract($row);

          $item = array(
            "id" => $id,
            "username" => $username,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "role" => $role,
            "email" => $email,
            "phone" => $phone,
            "created" => $created
          );

          array_push($collection_arr["data"], $item);
        }
      }
      $this->collection = $collection_arr;

      return true;
    }
    // return false if email does not exist in the database
    return false;
  }
}