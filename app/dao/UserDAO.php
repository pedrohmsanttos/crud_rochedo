<?php

namespace App\Dao;

use App\Model\User;


class UserDAO
{


    private $db;

    public function __construct($db)
    {
        $this->db = $db;

        define('TB_NAME', 'usuario');
        define('TB_NAME_PRO_USE', 'usuario_perfil');

        define('COL_ID', 'id_usuario');
        define('COL_NAME', 'nome');
        define('COL_LOGIN', 'login');
        define('COL_PASS', 'senha');
        define('COL_CPF', 'cpf');
        define('COL_EMAIL', 'email');
        define('COL_DT_CRE', 'data_cadastro');
        define('COL_USER_CRE', 'usuario_cadastro');
        define('COL_DT_DEL', 'data_exclusao');
        define('COL_USER_DEL', 'usuario_exclusao');
        define('COL_DELETED', 'excluido');
        define('COL_DT_LAST_AC', 'data_ultimo_acesso');
        define('COL_BLOCKED', 'bloqueado');
        define('COL_DT_BLOCKED', 'data_bloqueado');
        define('COL_USER_BLO', 'usuario_bloqueio');

        define("COL_ID_PRO", "id_perfil");
        define("COL_ID_USER", "id_usuario");

    }

    public function insert($user)
    {
        $data = [];

        $sql = "INSERT INTO " . TB_NAME . "(" . COL_NAME . "," . COL_LOGIN . "," . COL_PASS . "," . COL_CPF . "," . COL_EMAIL . "," . COL_DT_CRE_PRO . "," . COL_USER_CRE . ")";
        $sql .= " VALUES(:field1,:field2,:field3,:field4,:field5,:field6,:field7)";

        $data[':field1'] = utf8_decode($user->name);
        $data[':field2'] = $user->login;
        $data[':field3'] = $user->password;
        $data[':field4'] = $user->cpf;
        $data[':field5'] = $user->email;
        $data[':field6'] = date("Y-m-d H:i:s");
        $data[':field7'] = $_SESSION['user']->id;

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            $user_id = $this->db->lastInsertId();

            $data = [];

            $itsOk = false;

            foreach ($user->profile as $profile) {
                $sql = "INSERT INTO " . TB_NAME_PRO_USE . "( " . COL_ID_PRO . ", " . COL_ID_USER . ") VALUES (:field1,:field2)";
                $data[':field1'] = $profile;
                $data[':field2'] = $user_id;

                $stmt = $this->db->prepare($sql);
                $itsOk = $stmt->execute($data);
            }

            return $itsOk;

        } catch (\PDOException $ex) {
            $return['type'] = "error";
            $return['message'] = 'Erro SQL: ' . $ex->getMessage();
            echo json_encode($return);
            die;
        }

    }

    public function update($fields, $fields_profile = false, $conditions = false)
    {

        $data = [];
        $sql = "UPDATE " . TB_NAME . " SET ";

        $fields_update = implode(', ', array_map(
            function ($v, $k) {
                return sprintf("%s='%s'", $k, $v);
            },
            $fields,
            array_keys($fields)
        ));
        $sql .= $fields_update;

        if ($conditions) {
            $conditions_update = " WHERE " . implode(', ', array_map(
                    function ($v, $k) {
                        return sprintf("%s='%s'", $k, $v);
                    },
                    $conditions,
                    array_keys($conditions)
                ));

            $sql .= $conditions_update;
        }
//          echo $sql;die;
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            $itsOk = true;

            if (!empty($fields_profile)) {
                $sqlDelete = "DELETE FROM " . TB_NAME_PRO_USE . $conditions_update;

                $stmt = $this->db->prepare($sqlDelete);

                $itsOk = false;
                if ($stmt->execute()) {

                    foreach ($fields_profile as $profile) {
                        $sql = "INSERT INTO " . TB_NAME_PRO_USE . "( " . COL_ID_PRO . ", " . COL_ID . ") VALUES (:field1,:field2)";
                        $data[':field1'] = $profile;
                        $data[':field2'] = $conditions[COL_ID];

                        $stmt = $this->db->prepare($sql);
                        $itsOk = $stmt->execute($data);
                    }

                }

            }
            return $itsOk;

        } catch (\PDOException $ex) {
            $return['type'] = "error";
            $return['message'] = 'Erro SQL: ' . $ex->getMessage();
            echo json_encode($return);
            die;
        }
    }

    public function updateUser($user)
    {

        $fields[COL_NAME] = utf8_decode($user->name);
        $fields[COL_LOGIN] = $user->login;
        $fields[COL_CPF] = $user->cpf;
        $fields[COL_EMAIL] = $user->email;
        if($user->date_last_access != null){
            $fields[COL_DT_LAST_AC] = $user->date_last_access;
        }
        if (!empty(trim($user->password))) {
            $fields[COL_PASS] = $user->password;
        }
        if ($user->blocked != null && $user->blocked == "1") {
            $fields[COL_BLOCKED] = $user->blocked;
            $fields[COL_DT_BLOCKED] = date("Y-m-d H:i:s");
            $fields[COL_USER_BLO] = $_SESSION['user']->id;

        }else{
            $fields[COL_BLOCKED] = "0";
        }

        $fields_profile = $user->profile;

        $conditions = [COL_ID => $user->id];

        return $this->update($fields,$fields_profile,$conditions);
    }

    public function getUser($conditions = false, $getOne = false)
    {
        $conditions[COL_DELETED] = "0";
        $rows = $this->get(TB_NAME, $conditions);

        if ($getOne) {

            $user = new User();
            $user->id = $rows['0'][COL_ID];
            $user->name = $rows['0'][COL_NAME];
            $user->login = $rows['0'][COL_LOGIN];
            $user->password = $rows['0'][COL_PASS];
            $user->cpf = $rows['0'][COL_CPF];
            $user->email = $rows['0'][COL_EMAIL];
            $user->date_created = $rows['0'][COL_DT_CRE];
            $user->id_user_create = $rows['0'][COL_USER_CRE];
            $user->date_deleted = $rows['0'][COL_DT_DEL];
            $user->deleted = $rows['0'][COL_DELETED];
            $user->date_last_access = $rows['0'][COL_DT_LAST_AC];
            $user->blocked = $rows['0'][COL_BLOCKED];
            $user->date_blocked = $rows['0'][COL_DT_BLOCKED];
            $user->id_user_blocked = $rows['0'][COL_USER_BLO];

            $profiles_user = $this->get(TB_NAME_PRO_USE, [COL_ID_USER => $rows['0'][COL_ID_USER]]);
            $profile = [];
            foreach ($profiles_user as $p) {
                $profile[] = $p[COL_ID_PRO];
            }
            $user->profile = $profile;


            return $user;
        } else {
            foreach ($rows as $key => $row) {
                $profiles_user = $this->get(TB_NAME_PRO_USE, [COL_ID_USER => $row[COL_ID_USER]]);
                $profile = [];
                foreach ($profiles_user as $p) {
                    $profile[] = $p[COL_ID_PRO];
                }
                $rows[$key]['profile'] = $profile;
            }
            return $rows;
        }
    }

    private function get($table, $conditions = false)
    {
        $sql = "SELECT * FROM " . $table;


        if ($conditions) {
            $conditions_where = " WHERE ";
            $conditions_where .= implode(' AND ', array_map(
                function ($v, $k) {
                    return sprintf("%s='%s'", $k, $v);
                },
                $conditions,
                array_keys($conditions)
            ));

            $sql .= $conditions_where;
        }
        try {


            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            return $rows;


        } catch (\PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getByID($user)
    {
        $conditions = [COL_ID => $user->id];
        return $this->getUser($conditions, true);

    }
    public function getByLoginPass($user)
    {
        $conditions[COL_LOGIN] = $user->login;
        $conditions[COL_PASS] = $user->password;
        return $this->getUser($conditions, true);

    }

    public function getAll()
    {
        return $this->getUser();
    }

    public function delete($user)
    {
        $fields = [COL_DELETED => "1", COL_DT_DEL => date("Y-m-d H:i:s"), COL_USER_DEL => $_SESSION['user']->id];
        $conditions = [COL_ID => $user->id];

        return $this->update($fields, false, $conditions);
    }

}