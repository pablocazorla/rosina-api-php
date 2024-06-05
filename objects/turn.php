<?php
// 'turn' object
/*
Turn Model:

{
  id,
  client_id: ID
  client_name: VARCHAR(64)
  description: text
  formula: text [array] 
  cost: decimal(20,2)
  items_id: text [array] - "id1,id2,id3"
  day: date
  startTime: time
  duration: VARCHAR(8) - max: 9999 minutos
  location: VARCHAR(16) - [ peluqueria , gabinete, ambos ]
  status: VARCHAR(16) - [ pending , confirmed , cancelled ]

  //list:
  from_day:date,
  to_day:date,
  search:string(256),
}

*/

class Turn
{

  // database connection and table name
  private $conn;
  private $table_name = "turns";

  // object properties
  public $id;
  public $client_id;
  public $client_name;
  public $description;
  public $formula;
  public $cost;
  public $item_ids;
  public $day;
  public $startTime;
  public $duration;
  public $location;
  public $status;

  public $collection;
  public $search;
  public $from_day;
  public $to_day;

  // constructor
  public function __construct($db)
  {
    $this->conn = $db;
  }

  // create new
  public function create()
  {

    // insert query
    $query = "INSERT INTO " . $this->table_name . "
            SET
            client_id = :client_id,
            client_name = :client_name,
            formula = :formula,
            cost = :cost,
            item_ids = :item_ids,
            description = :description,
            day = :day,
            startTime = :startTime,
            duration = :duration,
            location = :location,
            status = :status";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->client_id = htmlspecialchars(strip_tags($this->client_id));
    $this->client_name = htmlspecialchars(strip_tags($this->client_name));
    $this->formula = htmlspecialchars(strip_tags($this->formula));
    $this->cost = htmlspecialchars(strip_tags($this->cost));
    $this->item_ids = htmlspecialchars(strip_tags($this->item_ids));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->day = htmlspecialchars(strip_tags($this->day));
    $this->startTime = htmlspecialchars(strip_tags($this->startTime));
    $this->duration = htmlspecialchars(strip_tags($this->duration));
    $this->location = htmlspecialchars(strip_tags($this->location));
    $this->status = htmlspecialchars(strip_tags($this->status));

    // bind the values
    $stmt->bindParam(':client_id', $this->client_id);
    $stmt->bindParam(':client_name', $this->client_name);
    $stmt->bindParam(':formula', $this->formula);
    $stmt->bindParam(':cost', $this->cost);
    $stmt->bindParam(':item_ids', $this->item_ids);
    $stmt->bindParam(':description', $this->description);
    $stmt->bindParam(':day', $this->day);
    $stmt->bindParam(':startTime', $this->startTime);
    $stmt->bindParam(':duration', $this->duration);
    $stmt->bindParam(':location', $this->location);
    $stmt->bindParam(':status', $this->status);

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

    // query to check if start exists
    $query = "SELECT client_id, client_name, description, formula, cost, item_ids, day, startTime, duration, location, status FROM " . $this->table_name . "
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
      $this->formula = $row['formula'];
      $this->cost = $row['cost'];
      $this->item_ids = $row['item_ids'];
      $this->description = $row['description'];
      $this->day = $row['day'];
      $this->startTime = $row['startTime'];
      $this->duration = $row['duration'];
      $this->location = $row['location'];
      $this->status = $row['status'];

      // return true because id exists in the database
      return true;
    }

    // return false if start does not exist in the database
    return false;
  }
  public function existById()
  {

    // query to check if start exists
    $query = "SELECT day
          FROM " . $this->table_name . "
          WHERE id = ?
          LIMIT 0,1";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id = htmlspecialchars(strip_tags($this->id));

    // bind given start value
    $stmt->bindParam(1, $this->id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if start exists, assign values to object properties for easy access and use for php sessions
    if ($num > 0) {
      // return true because start exists in the database
      return true;
    }

    // return false if start does not exist in the database
    return false;
  }

  // update a user record
  public function update()
  {
    // if no posted, do not update
    $query = "UPDATE " . $this->table_name . " SET";
    $query .= !empty($this->client_id) ? " client_id=:client_id," : "";
    $query .= !empty($this->client_name) ? " client_name=:client_name," : "";
    $query .= !empty($this->formula) ? " formula=:formula," : "";
    $query .= !empty($this->cost) ? " cost=:cost," : "";
    $query .= !empty($this->item_ids) ? " item_ids=:item_ids," : "";
    $query .= !empty($this->description) ? " description=:description," : "";
    $query .= !empty($this->day) ? " day=:day," : "";
    $query .= !empty($this->startTime) ? " startTime=:startTime," : "";
    $query .= !empty($this->duration) ? " duration=:duration," : "";
    $query .= !empty($this->location) ? " location=:location," : "";
    $query .= !empty($this->status) ? " status=:status," : "";

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
    if (!empty($this->formula)) {
      $this->formula = htmlspecialchars(strip_tags($this->formula));
      $stmt->bindParam(':formula', $this->formula);
    }
    if (!empty($this->cost)) {
      $this->cost = htmlspecialchars(strip_tags($this->cost));
      $stmt->bindParam(':cost', $this->cost);
    }
    if (!empty($this->item_ids)) {
      $this->item_ids = htmlspecialchars(strip_tags($this->item_ids));
      $stmt->bindParam(':item_ids', $this->item_ids);
    }
    if (!empty($this->description)) {
      $this->description = htmlspecialchars(strip_tags($this->description));
      $stmt->bindParam(':description', $this->description);
    }
    if (!empty($this->day)) {
      $this->day = htmlspecialchars(strip_tags($this->day));
      $stmt->bindParam(':day', $this->day);
    }
    if (!empty($this->startTime)) {
      $this->startTime = htmlspecialchars(strip_tags($this->startTime));
      $stmt->bindParam(':startTime', $this->startTime);
    }
    if (!empty($this->duration)) {
      $this->duration = htmlspecialchars(strip_tags($this->duration));
      $stmt->bindParam(':duration', $this->duration);
    }
    if (!empty($this->location)) {
      $this->location = htmlspecialchars(strip_tags($this->location));
      $stmt->bindParam(':location', $this->location);
    }
    if (!empty($this->status)) {
      $this->status = htmlspecialchars(strip_tags($this->status));
      $stmt->bindParam(':status', $this->status);
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
  public function list()
  {

    $search_set = "";

    if (!empty($this->search)) {
      $search_set = " WHERE p.client_id = ? ";
    }
    if (!empty($this->from_day)) {
      if (empty($this->search)) {
        $search_set = " WHERE p.day BETWEEN ? AND ? ";
      } else {
        $search_set .= "AND (p.day BETWEEN ? AND ?) ";
      }
    }

    // select all query
    $query = "SELECT p.id, p.client_id, p.client_name, p.formula, p.cost, p.item_ids, p.description, p.day, p.startTime, p.duration, p.location, p.status FROM " . $this->table_name . " p " . $search_set . " ORDER BY p.day DESC";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    if (!empty($this->search)) {
      // sanitize
      $keywords = $this->search;//htmlspecialchars(strip_tags($this->search));
    //  $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
    }

    if (!empty($this->from_day)) {
      // sanitize
      $this->from_day = htmlspecialchars(strip_tags($this->from_day));

      if (!empty($this->to_day)) {
        $this->to_day = htmlspecialchars(strip_tags($this->to_day));
      } else {
        $this->to_day = $this->from_day;
      }

      // bind
      if (empty($this->search)) {
        $stmt->bindParam(1, $this->from_day);
        $stmt->bindParam(2, $this->to_day);
      } else {
        $stmt->bindParam(2, $this->from_day);
        $stmt->bindParam(3, $this->to_day);
      }
    }


    // execute the query
    if ($stmt->execute()) {
      // get number of rows
      $num = $stmt->rowCount();

      $collection_arr = array();
      $collection_arr["elementsTotal"] = $num;
      if (!empty($this->search)) {
        $collection_arr["search"] = $this->search;
      }
      if (!empty($this->from_day)) {
        $collection_arr["from"] = $this->from_day;
        $collection_arr["to"] = $this->to_day;
      }

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
            "client_id" => $client_id,
            "client_name" => $client_name,
            "formula" => $formula,
            "cost" => $cost,
            "item_ids" => $item_ids,
            "description" => $description,
            "day" => $day,
            "startTime" => $startTime,
            "duration" => $duration,
            "location" => $location,
            "status" => $status
          );

          array_push($collection_arr["data"], $item);
        }
      }
      $this->collection = $collection_arr;

      return true;
    }
    // return false if start does not exist in the database
    return false;
  }
}
