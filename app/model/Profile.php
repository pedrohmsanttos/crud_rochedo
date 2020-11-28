<?php

namespace App\Model;

class Profile
{

    private $id;
    private $description;
    private $date_created;
    private $id_user_created;
    private $date_deleted;
    private $id_user_delete;
    private $deleted;

    private $errors;

    function __construct($data=false)
    {
        if($data){
            foreach ($data as $key => $item) {
                $this->$key = $item;
            }
        }
    }

    function __get($item)
    {
        return $this->$item;
    }

    function __set($item, $value)
    {
        $this->$item = $value;
    }

    public function is_valid()
    {

        $this->validate_description();

        return empty($this->errors);
    }

    private function validate_description(){
        if (empty( trim( $this->description) )){
            $this->errors['description'] = 'Preencha a descrição do perfil!';
        }

    }


}