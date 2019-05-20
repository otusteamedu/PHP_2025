<?php

namespace app\models;

class Products extends Model
{

    public $products = [/*Product*/];

    public function __construct()
    {

        parent::__construct();

        $this->sync2db();

    }
    private function sync2db(){

        $products = $this->getAll();
        $this->products = [];

        foreach($products as $product){

            $current_product = new Product([

                id_product          => $product['id_product'],
                name_product        => $product['name_product'],
                description         => $product['description'],
                price               => $product['price'],
                name_unit           => $product['name_unit'],
                img                 => $product['img'],
                description         => $product['description'],
                type                => $product['type'],
                category            => $product['category']
            ]);

            $this->products[] = $current_product;
        }

    }
    public  function getAll()
    {

        $sql = "SELECT id_product, name_product, price, img, description, category, type, name_unit FROM products as p, units as u, product_category as c, product_types as t WHERE p.id_unit=u.id_unit AND p.id_product_category = c.id_product_category AND p.id_product_type = t.id_product_type";

        return $this->db->queryAll($sql);
    }

    public  function getOne($product)
    {

        $sql = "SELECT id_product, name_product, price, img, description, category, type, name_unit FROM products as p, units as u, product_category as c, product_types as t WHERE u.id_unit=p.id_unit AND c.id_product_category=p.id_product_category AND t.id_product_type=t.id_product_type";

        $productsql = $this->db->queryOne($sql, ['id'=>$product->id_product]);

        return new Product([

            id_product          => $product->id_product,
            name_product        => $product->name_product,
            description         => $product->description,
            price               => $product->price,
            name_unit           => $product->name_unit,
            img                 => $product->img,
            type                => $product->type,
            category            => $product->category
        ]);
    }
    public function delete($product) {

        $tableName = $this->getTableName();
        $sql = "delete FROM {$tableName} WHERE id_product = :id";
//        var_dump($sql);
        $this->db->execute($sql, ['id'=>(int)$product->id_product]);
        $this->sync2db();
    }
    public function update($newproduct){

        $oldproduct = $this->findProductById($newproduct->id_product);
        var_dump($oldproduct);
        $sql_set = '';
        $params = ['id'=>(int)$newproduct->id_product];
        if($oldproduct){

            foreach ($this->products as $product){

                if($product->id_product == $newproduct->id_product){

                    if ($newproduct->name_product !== $product->name_product){

                        $sql_set .= "`name_product`= :name,";
                        $params ['name'] = "$newproduct->name_product";

                    }
                    if($newproduct->price !== $product->price){

                        $sql_set .= "`price`= :price,";
                        $params ['price'] = "$newproduct->price";
                    }
                    if($newproduct->description !== $product->description){

                        $sql_set .= "`description`= :description,";
                        $params ['description'] = "$newproduct->description";
                    }
                    if($newproduct->name_unit !== $product->name_unit){

                        $sql_set .= "`name_unit`= :name_unit,";
                        $params ['name_unit'] = "$newproduct->name_unit";
                    }
                    if($newproduct->img !== $product->img){

                        $sql_set .= "`img`= :img,";
                        $params ['img'] = "$newproduct->img";
                    }
                    if($newproduct->type !== $product->type){

                        $sql_set .= "`img`= :img,";
                        $params ['img'] = "$newproduct->img";
                    }
                    if($newproduct->category !== $product->category){

                        $sql_set .= "`category`= :category,";
                        $params ['category'] = "$newproduct->category";
                    }
                }
            }
            $sql_set = substr($sql_set, 0,-1);
        }
//        var_dump('sql_set', $sql_set);
        $sql = "UPDATE `products` SET " . $sql_set . " WHERE id_product=:id";
        var_dump($sql);
        var_dump($params);
//        die();
        $this->db->execute($sql, $params);
        $this->sync2db();
    }
    private function findProductById($id){

        $find=null;
        foreach ($this->products as $product){

//            var_dump("new",$product);
//            var_dump("ID",$id);
//            var_dump("ID_prod",$product->id_product);
            if($product->id_product == $id){

                $find = $product;
//                var_dump('FIND++++++++++++', $find);
            }
        }
        return $find;
    }
    public function insert($newproduct)
    {
        $id_unit     = $this->getUnitIdByName($newproduct->name_unit);
        $id_type     = $this->getTypeIdByName($newproduct->type);
        $id_category = $this->getCategoryIdByName($newproduct->category);

        $sql = "INSERT INTO `products`(`name_product`, `price`, `img`, `id_unit`, `id_product_type`, `id_product_category`, `description`) VALUES (:name_product,:price,:img,:id_unit,:id_product_type,:id_product_category,:description)";
//        var_dump($sql);
        $this->db->execute($sql, ['name_product'=>"$newproduct->name_product",'price'=>$newproduct->price,'img'=>"$newproduct->img",'id_unit'=>(int)$id_unit, 'id_product_type'=>(int)$id_type, 'id_product_category'=>(int)$id_category,'description'=>"$newproduct->description"]);
        $this->sync2db();
    }
    private function getUnitIdByName($name){

        $sql = "SELECT id_unit FROM `units` WHERE name_unit=:name";

        return $this->db->queryOne($sql, ['name'=> $name]);
    }
    private function getTypeIdByName($type){

        $sql = "SELECT id_product_type FROM `product_types` as t WHERE type=:type";

        return $this->db->queryOne($sql, ['type'=> $type]);
    }
    private function getCategoryIdByName($category){

        $sql = "SELECT c.id_product_category FROM `product_category` as c WHERE category = :category";

        return $this->db->queryOne($sql, ['category'=> $category]);
    }
    public function getTableName()
    {
        return 'products';
    }

}