<?php 

class Reservation {

    private $id;
    private $user_id;
    private $checkin_date;
    private $checkout_date;
    private $total_price;
    private $status;

    public function __construct($user_id, $checkin_date, $checkout_date, $total_price, $status){
        $this->user_id = $user_id;
        $this->checkin_date = $checkin_date;
        $this->checkout_date = $checkout_date;
        $this->total_price = $total_price;
        $this->status = $status;
    }

    public function getUserId(){
        return $this->user_id;
    }
    
    public function getCheckinDate(){
        return $this->checkin_date;
    }

    public function getCheckoutDate(){
        return $this->checkout_date;
    }

    public function getTotalPrice(){
        return $this->total_price;
    }

    public function getStatus(){
        return $this->status;
    }
}