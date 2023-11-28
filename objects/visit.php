<?php
// 'turn' object
/*
Turn Model:

{
  id,
  client_id: INT(11)
  item_ids: string(text),
  cost: decimal(20,2),
  description: string(text),
  formula: string(text),
  day: date,
  startTime: time,
  duration: decimal(5,2),

  //list:
  from_day:date,
  to_day:date,
  search:string(256),
  by_client_id:INT(11)
}

*/

class Visit
{

  // database connection and table name
  private $conn;
  private $table_name = "visits";

  // object properties
  public $id;
  public $client_id;
  public $item_ids;
  public $cost;
  public $description;
  public $formula;
  public $day;
  public $startTime;
  public $duration;

  public $collection;
  public $search;
  public $from_day;
  public $to_day;
  public $by_client_id;

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
            cost = :cost,
            formula = :formula,
            item_ids = :item_ids,
            description = :description,
            day = :day,
            startTime = :startTime,
            duration = :duration";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->client_id = htmlspecialchars(strip_tags($this->client_id));
    $this->cost = htmlspecialchars(strip_tags($this->cost));
    $this->formula = htmlspecialchars(strip_tags($this->formula));
    $this->item_ids = htmlspecialchars(strip_tags($this->item_ids));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->day = htmlspecialchars(strip_tags($this->day));
    $this->startTime = htmlspecialchars(strip_tags($this->startTime));
    $this->duration = htmlspecialchars(strip_tags($this->duration));

    // bind the values
    $stmt->bindParam(':client_id', $this->client_id);
    $stmt->bindParam(':cost', $this->cost);
    $stmt->bindParam(':formula', $this->formula);
    $stmt->bindParam(':item_ids', $this->item_ids);
    $stmt->bindParam(':description', $this->description);
    $stmt->bindParam(':day', $this->day);
    $stmt->bindParam(':startTime', $this->startTime);
    $stmt->bindParam(':duration', $this->duration);

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
    $query = "SELECT client_id, cost, formula, item_ids, description, day, startTime, duration FROM " . $this->table_name . "
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
      $this->cost = $row['cost'];
      $this->formula = $row['formula'];
      $this->item_ids = $row['item_ids'];
      $this->description = $row['description'];
      $this->day = $row['day'];
      $this->startTime = $row['startTime'];
      $this->duration = $row['duration'];

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
    $query .= !empty($this->cost) ? " cost=:cost," : "";
    $query .= !empty($this->formula) ? " formula=:formula," : "";
    $query .= !empty($this->item_ids) ? " item_ids=:item_ids," : "";
    $query .= !empty($this->description) ? " description=:description," : "";
    $query .= !empty($this->day) ? " day=:day," : "";
    $query .= !empty($this->startTime) ? " startTime=:startTime," : "";
    $query .= !empty($this->duration) ? " duration=:duration," : "";

    $query = substr($query, 0, -1);

    $query .= " WHERE id=:id";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    if (!empty($this->client_id)) {
      $this->client_id = htmlspecialchars(strip_tags($this->client_id));
      $stmt->bindParam(':client_id', $this->client_id);
    }
    if (!empty($this->cost)) {
      $this->cost = htmlspecialchars(strip_tags($this->cost));
      $stmt->bindParam(':cost', $this->cost);
    }
    if (!empty($this->formula)) {
      $this->formula = htmlspecialchars(strip_tags($this->formula));
      $stmt->bindParam(':formula', $this->formula);
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
      $search_set = " WHERE (p.formula LIKE ? OR p.description LIKE ?) ";
    }
    if (!empty($this->from_day)) {
      if (empty($this->search)) {
        $search_set = " WHERE p.day BETWEEN ? AND ? ";
      } else {
        $search_set .= "AND (p.day BETWEEN ? AND ?) ";
      }
    }

    if (!empty($this->by_client_id)) {
      if (empty($this->search) && empty($this->from_day)) {
        $search_set = " WHERE p.client_id = ? ";
      } else {
        $search_set .= "AND p.client_id = ? ";
      }
    }

    // select all query
    $query = "SELECT p.id, p.client_id, p.cost, p.formula, p.item_ids, p.description, p.day, p.startTime, p.duration FROM " . $this->table_name . " p " . $search_set . " ORDER BY p.day DESC";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    if (!empty($this->search)) {
      // sanitize
      $keywords = htmlspecialchars(strip_tags($this->search));
      $keywords = "%{$keywords}%";

      // bind
      $stmt->bindParam(1, $keywords);
      $stmt->bindParam(2, $keywords);
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
        $stmt->bindParam(3, $this->from_day);
        $stmt->bindParam(4, $this->to_day);
      }
    }
    if (!empty($this->by_client_id)) {
      // sanitize
      $this->by_client_id = htmlspecialchars(strip_tags($this->by_client_id));
      if (empty($this->search)) {
        if (empty($this->from_day)) {
          $stmt->bindParam(1, $this->by_client_id);
        } else {
          $stmt->bindParam(3, $this->by_client_id);
        }
      } else {
        if (empty($this->from_day)) {
          $stmt->bindParam(3, $this->by_client_id);
        } else {
          $stmt->bindParam(5, $this->by_client_id);
        }
      }
    }

    // execute the query
    if ($stmt->execute()) {
      // get number of rows
      $num = $stmt->rowCount();

      $collection_arr = array();
      $collection_arr["elementsTotal"] = $num;

      if (!empty($this->by_client_id)) {
        $collection_arr["by_client_id"] = $this->by_client_id;
      }
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
            "cost" => $cost,
            "formula" => $formula,
            "item_ids" => $item_ids,
            "description" => $description,
            "day" => $day,
            "startTime" => $startTime,
            "duration" => $duration,
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