<?php
namespace App\Controllers;

use App\Controllers\Controller;


use Classes\models\Product;
use Classes\models\Customer;
use Classes\models\Delivery;
use Classes\models\Booking;

class busketController extends Controller
{
    protected $view;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {

        $connect = $this->bd();
        $fields = array('*');
        $data = $connect->select('goods',$fields);
        $this->view->render('busket',compact('data'));
    }

    public function addAction($product){
        if(isset( $_SESSION['tovar'])){
            array_push($_SESSION['tovar'],$product);
        }
        else $_SESSION['tovar'] = array($product);
       $this->redirect('http://new/busket');

    }

    /**
     * получаем информацию о продуктах добавленных в карзину
     * возвращаем форму для оформления заказа
     * @return mixed
     */

    public function storeAction(){
        $data = [];
        foreach($_SESSION['tovar'] as $item){
        $product = new Product();
        $value = $product->get_product($item);
            array_push($data,$value);
        }
        return $this->view->render('partials/new_Booking',compact('data'));
    }

    public function setAction()
    {

        if($_POST['name'] && $_POST['s_name'] && $_POST['notes'] &&
           $_POST['phone_1'] && $_POST['phone_2'] && $_POST['address'] && $_POST['date']
            != '' )
        {
            /* ищем существующего или создаем нового Customer*/
            $item = new Customer();
            $where = array('name'=>$_POST['name'],'phone'=>$_POST['phone_1']);
            $customer = $item->find_person($where);

            if(is_null($customer))
            {
                $customer = $item->new_customer(['name'=>$_POST['name'],
                    's_name'=>$_POST['s_name'],
                    'phone'=>$_POST['phone_1']]);
            }

            // создаем новый заказ на доставку
            $d_data = array( "name"     =>$_POST['name'],
                             "phone"    =>$_POST['phone_2'],
                             "address"  =>$_POST['address'],
                             "notes"    =>$_POST['notes']);
            $d = new Delivery();
            $delivery = $d->new_order($d_data);

            $b_data = array('id_customer'=>$customer,'id_delivery'=>$delivery);
            $boo = new Booking();
            $id_booking = $boo->create($b_data);
                foreach($_SESSION['tovar'] as $val)
                {
                    $boo->create_relation_data(['id_booking'=>$id_booking,'id_product'=>$val]);
                    return $this->view->render('partials/new_Booking');
                }

        }else //  если есть пустые поля возвращаем форму и введенные данные
        {
            echo "Заполните пожалуйста все поля";
            $input = $_POST;
            return $this->view->render('partials/new_Booking',compact('input'));
        }
    }

    public function bd(){
        return parent::bd();
    }
}