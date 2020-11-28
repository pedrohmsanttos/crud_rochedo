<?php

namespace App\Model;

class User{

    private $id;
    private $name;
    private $login;
    private $password;
    private $cpf;
    private $email;
    private $date_created;
    private $date_deleted;
    private $id_user_delete;
    private $id_user_create;
    private $deleted;
    private $date_last_access;
    private $blocked;
    private $date_blocked;
    private $id_user_blocked;

    private $profile;

    private $errors;

    function __construct($data=false){
        if($data){
            foreach ($data as $key => $value){
                if($key == "password" && trim($value) != ""){
                    $value = hash("sha256", $value);

                }elseif ($key == "cpf" && trim($value) != ""){
                    $value = str_replace(".","", $value);
                    $value = str_replace("-","", $value);
                }
                $this->$key = $value;
            }
        }
    }

    function __get($item){
        return $this->$item;
    }

    function __set($item, $value){
        if($item == "password" && trim($value) != ""){
            $value = hash('sha256', $value);
        }elseif ($item == "cpf" && trim($value) != ""){
            $value = str_replace(".","", $value);
            $value = str_replace("-","", $value);
        }
       $this->$item = $value;
    }


    // Validando os dados do modelo
    public function is_valid()
    {
        $this->validate_name();
        $this->validate_login();
        $this->validate_email();
        if(empty($this->id)){
            $this->validate_password();
        }
        $this->validate_cpf();
        $this->validate_profile();

        if(empty($this->errors)){
            return true;
        }else{
            return false;
        }
    }

    private function validate_name(){
        if (empty( trim( $this->name ) )){
            $this->errors['name'] = 'Preenche o nome ai por favor!';
        }
    }

    private function validate_login(){
        if (empty( trim( $this->login ) )){
            $this->errors['login'] = 'Preenche o login ai por favor!';
        }
    }

    private function validate_email(){
        if (empty( trim( $this->email )  )){
            $this->errors['email'] = 'Coloca teu email ai, tricolor!';
        }else{
            if( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ){
                $this->errors['email'] = 'Email inválido!';
            }
        }
    }

    private function validate_password(){
        if (empty( trim( $this->password ) )){
            $this->errors['password'] = 'A senha é obrigatória po!';
        }
    }

    private function validate_cpf(){
        if (empty( trim( $this->cpf) )){
            $this->errors['cpf'] = 'Tem que preencher o CPF!';
        }
    }
    private function validate_profile(){
        if (empty( $this->profile )){
            $this->errors['profile'] = 'Você tem que selecionar pelos menos um perfil!';
        }
    }

}