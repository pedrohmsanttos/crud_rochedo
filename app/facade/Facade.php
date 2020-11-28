<?php

namespace App\Facade;

use App\Dao\ProfileDAO;
use App\Dao\UserDAO;
use App\Model\User;
use App\Model\Profile;

class Facade{
    private $db;
    private $userDAO;
    private $profileDAO;

    function __construct($db){
        $this->db = $db;
        $this->userDAO = new UserDAO($this->db);
        $this->profileDAO = new ProfileDAO($this->db);
    }

    public function insert($obj){
        if($obj instanceof User){
            return $this->userDAO->insert($obj);
        }elseif ($obj instanceof Profile){
            return $this->profileDAO->insert($obj);
        }
    }

    public function update($obj){
        if($obj instanceof User){
            return $this->userDAO->updateUser($obj);
        }elseif ($obj instanceof Profile){
            return $this->profileDAO->updateProfile($obj);
        }
    }

    public function delete($obj){
        if($obj instanceof User){
            return $this->userDAO->delete($obj);
        }elseif ($obj instanceof Profile){
            return $this->profileDAO->delete($obj);
        }
    }


    public function getByID($obj){
        if($obj instanceof User){
            return $this->userDAO->getByID($obj);
        }elseif($obj instanceof Profile){
            return $this->profileDAO->getByID($obj);
        }
    }

    public function getAll($obj){
        if($obj instanceof User){
            return $this->userDAO->getAll($obj);
        }elseif($obj instanceof Profile){
            return $this->profileDAO->getAll($obj);
        }
    }

    public function getByLoginPass($user){
        return $this->userDAO->getByLoginPass($user);
    }
}