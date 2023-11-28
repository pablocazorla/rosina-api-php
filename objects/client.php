<?php
// 'user' object
/*
User Model:

{
  id,
  name: VARCHAR(64)
  birthday: datetime
  dni: VARCHAR(10)
  phone: INT(25)
  phone_contact: INT(25)
  created (DEFAULT CURRENT_TIMESTAMP)
}

*/

class Client
{

  // database connection and table name
  private $conn;
  private $table_name = "clients";

  // object properties
  public $id;
  public $name;
  public $birthday;
  public $dni;
  public $phone_contact;
  public $phone;
  public $created;

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

  // create new record
  public function create()
  {

    // insert query
    $query = "INSERT INTO " . $this->table_name . "
            SET
                name = :name,
                dni = :dni,
                birthday = :birthday,
                phone_contact = :phone_contact,
                phone = :phone";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->dni = htmlspecialchars(strip_tags($this->dni));
    $this->birthday = htmlspecialchars(strip_tags($this->birthday));
    $this->phone_contact = htmlspecialchars(strip_tags($this->phone_contact));
    $this->phone = htmlspecialchars(strip_tags($this->phone));

    // bind the values
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':dni', $this->dni);
    $stmt->bindParam(':birthday', $this->birthday);
    $stmt->bindParam(':phone_contact', $this->phone_contact);
    $stmt->bindParam(':phone', $this->phone);

    // execute the query, also check if query was successful
    if ($stmt->execute()) {
      $last_insert_id = $this->conn->lastInsertId();
      $this->id = $last_insert_id;
      return true;
    }

    return false;
  }
  public function setById()
  {

    // query to check if id exists
    $query = "SELECT name, birthday, dni, phone, phone_contact, created
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
      $this->name = $row['name'];
      $this->dni = $row['dni'];
      $this->birthday = $row['birthday'];
      $this->phone_contact = $row['phone_contact'];
      $this->phone = $row['phone'];
      $this->created = $row['created'];

      // return true because id exists in the database
      return true;
    }

    // return false if phone_contact does not exist in the database
    return false;
  }
  // check if given id exist in the database
  public function existById()
  {

    // query to check if phone_contact exists
    $query = "SELECT name
          FROM " . $this->table_name . "
          WHERE id = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind given phone_contact value
    $stmt->bindParam(1, $this->id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if phone_contact exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {
      // return true because phone_contact exists in the database
      return true;
    }

    // return false if phone_contact does not exist in the database
    return false;
  }

  // update a user record
  public function update()
  {
    // if no posted, do not update
    $query = "UPDATE " . $this->table_name . " SET";
    $query .= !empty($this->name) ? " name=:name," : "";
    $query .= !empty($this->dni) ? " dni=:dni," : "";
    $query .= !empty($this->birthday) ? " birthday=:birthday," : "";
    $query .= !empty($this->phone_contact) ? " phone_contact=:phone_contact," : "";
    $query .= !empty($this->phone) ? " phone=:phone," : "";

    $query = substr($query, 0, -1);

    $query .= " WHERE id=:id";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    if (!empty($this->name)) {
      $this->name = htmlspecialchars(strip_tags($this->name));
      $stmt->bindParam(':name', $this->name);
    }
    if (!empty($this->dni)) {
      $this->dni = htmlspecialchars(strip_tags($this->dni));
      $stmt->bindParam(':dni', $this->dni);
    }
    if (!empty($this->birthday)) {
      $this->birthday = htmlspecialchars(strip_tags($this->birthday));
      $stmt->bindParam(':birthday', $this->birthday);
    }
    if (!empty($this->phone_contact)) {
      $this->phone_contact = htmlspecialchars(strip_tags($this->phone_contact));
      $stmt->bindParam(':phone_contact', $this->phone_contact);
    }
    if (!empty($this->phone)) {
      $this->phone = htmlspecialchars(strip_tags($this->phone));
      $stmt->bindParam(':phone', $this->phone);
    }

    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);

    // execute the query
    if ($stmt->execute()) {
      if ($this->setById()) {
        return true;
      }
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
    $search_set = !empty($this->search) ? " WHERE p.name LIKE ? " : "";
    $query = "SELECT p.id FROM " . $this->table_name . " p" . $search_set;
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    if (!empty($this->search)) {
      // sanitize
      $keywords = htmlspecialchars(strip_tags($this->search));
      $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
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

    $search_set = !empty($this->search) ? " WHERE p.name LIKE ? " : "";

    // select all query
    $query = "SELECT p.id, p.name, p.dni, p.birthday, p.phone_contact, p.phone, p.created FROM " . $this->table_name . " p " . $search_set . "ORDER BY p." . $this->orderBy . " " . $this->orderTo . " LIMIT " . $r_initial . "," . $r_final;

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    if (!empty($this->search)) {
      // sanitize
      $keywords = htmlspecialchars(strip_tags($this->search));
      $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
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
            "name" => $name,
            "dni" => $dni,
            "birthday" => $birthday,
            "phone_contact" => $phone_contact,
            "phone" => $phone,
            "created" => $created
          );

          array_push($collection_arr["data"], $item);
        }
      }
      $this->collection = $collection_arr;

      return true;
    }
    // return false if phone_contact does not exist in the database
    return false;
  }
}
