<?php 

class ReservationDAO {

    private $conn;
    
    public function __construct($conn){

        $this->conn = $conn;

    }

    public function countByStatus($status){

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = :status");
        
        $stmt->bindParam(":status", $status);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getEstimativedRevenue(){

        $stmt = $this->conn->prepare("SELECT SUM(total_price) FROM reservations WHERE status = 'confirmado'");

        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    public function getLatestReservations($status = null, $limit = 5){

        $sql = "SELECT 
                    reservations.id,
                    users.name,
                    reservations.checkin_date,
                    reservations.checkout_date,
                    reservations.total_price,
                    reservations.status
                FROM reservations
                INNER JOIN users ON reservations.user_id = users.id
                ";

        if($status){

            $sql .= " WHERE reservations.status = :status";

        }

        $sql .= " ORDER BY reservations.created_at DESC LIMIT $limit";

        $stmt = $this->conn->prepare($sql);

        if($status){

            $stmt->bindParam(":status", $status);

        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getReservations($status = "", $search = "", $dateFrom = "", $dateTo = ""){

        $sql = "SELECT 
                    reservations.id,
                    users.name,
                    reservations.checkin_date,
                    reservations.checkout_date,
                    reservations.total_price,
                    reservations.status
                FROM reservations
                INNER JOIN users ON reservations.user_id = users.id
                ";

        $conditions = [];
        $params = [];

        if(!empty($status)){
            $conditions[] = "reservations.status = :status";
            $params[':status'] = $status;
        }

        if(!empty($search)){
            $conditions[] = "users.name LIKE :search";
            $params[':search'] = "%"."search"."%";
        }

        if(!empty($dateFrom)){
            $conditions[] = "reservations.checkin_date >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if(!empty($dateTo)){
            $conditions[] = "reservations.checkout_date <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        if(!empty($conditions)){
            $sql .= " WHERE ".implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY reservations.create_at DESC";

        $stmt = $this->conn->prepare($sql);

        foreach($params as $key => $value){
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}