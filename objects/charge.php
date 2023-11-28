<?php
// 'charge' object
/*
Charge Model:

{
  id,
  client_id: INT(11),  
  client_name: text,
  turn_id: text,
  status: tinyINT(1),
  cost: DECIMAL(20,2),
  date_created: datetime,
}

*/

class Charge
{

  // database connection and table name
  private $conn;
  private $table_name = "charges";

  // object properties
  public $id;
  public $client_id;
  public $client_name;
  public $turn_id;
  public $status;
  public $cost;
  public $date_created;

  public $collection;
  public $search;
  public $page = 0;
  public $pagination = 10;
  public $orderBy = 'date_created';
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
                client_id = :client_id,
                client_name = :client_name,
                turn_id = :turn_id,
                status = :status,
                cost = :cost";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->client_id = htmlspecialchars(strip_tags($this->client_id));
    $this->client_name = htmlspecialchars(strip_tags($this->client_name));
    $this->turn_id = htmlspecialchars(strip_tags($this->turn_id));
    $this->status = htmlspecialchars(strip_tags($this->status));
    $this->cost = htmlspecialchars(strip_tags($this->cost));

    // bind the values
    $stmt->bindParam(':client_id', $this->client_id);
    $stmt->bindParam(':client_name', $this->client_name);
    $stmt->bindParam(':turn_id', $this->turn_id);
    $stmt->bindParam(':status', $this->status);
    $stmt->bindParam(':cost', $this->cost);

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
    $query = "SELECT client_id, client_name, turn_id, status, cost, date_created
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
      $this->client_id = $row['client_id'];
      $this->client_name = $row['client_name'];
      $this->turn_id = $row['turn_id'];
      $this->status = $row['status'];
      $this->cost = $row['cost'];
      $this->date_created = $row['date_created'];

      // return true because id exists in the database
      return true;
    }

    // return false if does not exist in the database
    return false;
  }
  // check if given id exist in the database
  public function existById()
  {

    // query to check if  exists
    $query = "SELECT cost
          FROM " . $this->table_name . "
          WHERE id = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind given  value
    $stmt->bindParam(1, $this->id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if  exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {
      // return true because  exists in the database
      return true;
    }

    // return false if  does not exist in the database
    return false;
  }

  // update a user record
  public function update()
  {
    // if no posted, do not update
    $query = "UPDATE " . $this->table_name . " SET";
    $query .= !empty($this->client_id) ? " client_id=:client_id," : "";
    $query .= !empty($this->client_name) ? " client_name=:client_name," : "";
    $query .= !empty($this->turn_id) ? " turn_id=:turn_id," : "";
    $query .= !empty($this->status) ? " status=:status," : "";
    $query .= !empty($this->cost) ? " cost=:cost," : "";

    $query = substr($query, 0, -1);

    $query .= " WHERE id=:id";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    if (!empty($this->client_id)) {
      $this->client_id = htmlspecialchars(strip_tags($this->client_id));
      $stmt->bindParam(':client_id', $this->client_id);
    }
    if (!empty($this->client_name)) {
      $this->client_name = htmlspecialchars(strip_tags($this->client_name));
      $stmt->bindParam(':client_name', $this->client_name);
    }
    if (!empty($this->turn_id)) {
      $this->turn_id = htmlspecialchars(strip_tags($this->turn_id));
      $stmt->bindParam(':turn_id', $this->turn_id);
    }
    if (!empty($this->status)) {
      $this->status = htmlspecialchars(strip_tags($this->status));
      $stmt->bindParam(':status', $this->status);
    }
    if (!empty($this->cost)) {
      $this->cost = htmlspecialchars(strip_tags($this->cost));
      $stmt->bindParam(':cost', $this->cost);
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
    $search_set = !empty($this->search) ? " WHERE p.client_name LIKE ? " : "";
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

    $search_set = !empty($this->search) ? " WHERE p.client_name LIKE ? " : "";

    // select all query
    $query = "SELECT p.id, p.client_id, p.client_name, p.turn_id, p.status, p.cost, p.date_created FROM " . $this->table_name . " p " . $search_set . "ORDER BY p." . $this->orderBy . " " . $this->orderTo . " LIMIT " . $r_initial . "," . $r_final;

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

          extract($row);

          $item = array(
            "id" => $id,
            "client_id" => $client_id,
            "client_name" => $client_name,
            "turn_id" => $turn_id,
            "status" => $status,
            "cost" => $cost,
            "date_created" => $date_created
          );

          array_push($collection_arr["data"], $item);
        }
      }
      $this->collection = $collection_arr;

      return true;
    }
    // return false if  does not exist in the database
    return false;
  }
}
