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
}