<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Product extends Model{

    public static function listAll(){
        $sql = new SQL();
        return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
        var_dump("product");
        exit;
    }

    public static function checkList($list){
        foreach ($list as &$row) {
            $p = new Product;
            $p->setData($row);
            $row = $p->getValues();            
        }
        return $list;   
    }
    
    public function save(){       
        $sql = new Sql();
        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)",
            array(  ":idproduct"  =>$this->getidproduct(),
                    ":desproduct" =>$this->getdesproduct(),
                    ":vlprice"    =>$this->getvlprice(),
                    ":vlwidth"    =>$this->getvlwidth(),
                    ":vlheight"   =>$this->getvlheight(),
                    ":vllength"   =>$this->getvllength(),
                    ":vlweight"   =>$this->getvlweight(),
                    ":desurl"     =>$this->getdesurl()
        ));        
        $this->setData($results[0]);
    }

    public function get($idproduct){
        $sql = new SQL();
        $results = $sql->select("SELECT * FROM tb_products where idproduct = :idproduct", [':idproduct'=>$idproduct]);
        $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct",
                    array(":idproduct"=>$this->getidproduct()));
    }

    public function checkPhoto(){
        // Dentro do if é verificado o arquivo e o diretório da maquina/servidor onde esta rodonado o sistema.
        //Por isso usa-se o DIRECTORY_SEPARATOR.
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
        "resource" . DIRECTORY_SEPARATOR . 
        "site" . DIRECTORY_SEPARATOR . 
        "img" . DIRECTORY_SEPARATOR . 
        "products" . DIRECTORY_SEPARATOR . $this->getidproduct(). ".jpg")){
            // Aqui é o caminho usado pelo navegador. Por isso usa-se as barras(/).
            $url = "/resource/site/img/products/" . $this->getidproduct() . ".jpg";
        }
        else{
            $url = "/resource/site/img/product.jpg";
        }
        return $this->setdesphoto($url);
    }

    public function getValues(){
        $this->checkPhoto();
        $values = parent::getValues();
        return $values;
    }

    public function setPhoto($file){
        //var_dump($file);
        //exit;
        //Aqui é feito a identificação do tipo da extensão do arquivo.        
        // O explode do arquivo tranformando-o em array, e procura por um ponto.
        $extension = explode('.', $file['name']);       
        $extension = end($extension);
        
        switch($extension){
            case "jpg":
            case "jpeg":
                    $image = imagecreatefromjpeg($file["tmp_name"]);                   
                break;

            case "gif":
                    $image = imagecreatefromgif($file["tmp_name"]);
                break;

            case "png":
                    $image = imagecreatefrompng($file["tmp_name"]);
                break;
                
            case "webp":            
                    $image = imagecreatefromwebp($file["tmp_name"]);
                break;
        }

        $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
            "resource" . DIRECTORY_SEPARATOR . 
            "site" . DIRECTORY_SEPARATOR . 
            "img" . DIRECTORY_SEPARATOR . 
            "products" . DIRECTORY_SEPARATOR . $this->getidproduct(). ".jpg";

        imagejpeg($image, $dist);
        imagedestroy($image);
        $this->checkPhoto();
    }
}
?>