<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 4/21/2017
 * Time: 10:15 PM
 */

namespace classes\models;

use Classes\DataBase as connect;

class Booking
{
    protected $id;
    private $connect;

    protected static $tables1 = 'booking';
    protected static $tables2 = 'product_booking';

    protected static $fields1 = array('booking.id_booking','booking.date','booking.id_customer','booking.id_delivery');
    protected static $fields2 = array('product_booking.id_booking','product_booking.id_product',
                                      'product_booking.quantity','product_booking.id_user');
    public function __construct()
    {
        $this->connect = new connect();
    }

    public function create($data)
    {
        $result = $this->connect->insert($this::$tables1,$data);
        return $result;
    }

    public function create_relation_data($data){
        $this->connect->insert($this::$tables2,$data);
    }


}