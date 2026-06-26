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

    public function getEstimatedRevenue(){

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

        $sql .= " ORDER BY reservations.created_at DESC LIMIT " . (int)$limit;

        $stmt = $this->conn->prepare($sql);

        if($status){

            $stmt->bindParam(":status", $status);

        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getReservationsFiltered($status = "", $search = "", $dateFrom = "", $dateTo = ""){

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
            $params[':search'] = "%".$search."%";
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

        $sql .= " ORDER BY reservations.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        foreach($params as $key => $value){
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getStatusSummary(){
        return [
            "solicitado" => $this->countByStatus("solicitado"),
            "confirmado" => $this->countByStatus("confirmado"),
            "cancelado" => $this->countByStatus("cancelado"),
            "cancelamento solicitado" => $this->countByStatus("cancelamento solicitado")
        ];
    }

    public function findByID($id){

        $stmt = $this->conn->prepare("SELECT status FROM reservations WHERE id = :id");

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function updateStatus($id, $status){

        $stmt = $this->conn->prepare("UPDATE reservations SET status = :status WHERE id = :id");

        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();

    }

    public function getUnavailableDates(){

        $stmt = $this->conn->prepare("SELECT checkin_date,checkout_date FROM reservations WHERE status IN ('confirmado', 'cancelamento solicitado')");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function checkAvailability($checkin, $checkout){

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE status IN ('confirmado', 'cancelamento solicitado') AND checkin_date < :checkout AND checkout_date > :checkin");

        $stmt->bindParam(":checkin", $checkin);
        $stmt->bindParam(":checkout", $checkout);

        $stmt->execute();

        return $stmt->fetchColumn() == 0;
    }

    public function createReservation($user_id, $checkin, $checkout, $total_price, $status){

        $stmt = $this->conn->prepare("INSERT INTO reservations
                                    (
                                        user_id,
                                        checkin_date,
                                        checkout_date,
                                        total_price,
                                        status
                                    )

                                    VALUES
                                    (
                                        :user_id,
                                        :checkin,
                                        :checkout,
                                        :total_price,
                                        :status
                                    )
        ");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":checkin", $checkin);
        $stmt->bindParam(":checkout", $checkout);
        $stmt->bindParam(":total_price", $total_price);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();

    }

    public function getReservationsByUser($user_id){

        $stmt = $this->conn->prepare("SELECT id, checkin_date, checkout_date, total_price, status FROM reservations WHERE user_id = :user_id ORDER BY checkin_date DESC");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getLatestByUser($user_id, $limit = 5){

        $stmt = $this->conn->prepare("SELECT id, checkin_date, checkout_date, total_price, status FROM reservations WHERE user_id = :user_id ORDER BY checkin_date DESC LIMIT $limit");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function countByUser($user_id){

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchColumn();

    }

    public function conutConfirmedByUser($user_id){

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id AND status = 'confirmado'");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchColumn();

    }

    public function sumValueByUser($user_id){

        $stmt = $this->conn->prepare("SELECT SUM(total_price) FROM reservations WHERE user_id = :user_id AND status != 'cancelado'");

        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchColumn() ?? 0;

    }

    public function findByIdAndUser($reservation_id, $user_id){

        $stmt = $this->conn->prepare("SELECT id, status FROM reservations WHERE id = :id AND user_id = :user_id");

        $stmt->bindParam(":id", $reservation_id);
        $stmt->bindParam(":user_id", $user_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
}