<?php

namespace App\Dao;

use App\Model\Profile;

class ProfileDAO
{

    private $db;


    function __construct($db)
    {
        $this->db = $db;

        define('TB_NAME_PROFILE', 'perfil');

        define('COL_ID_PRO', 'id_perfil');
        define('COL_DESC_PRO', 'descricao');
        define('COL_DT_CRE_PRO', 'data_cadastro');
        define('COL_USER_CRE_PRO', 'usuario_cadastro');
        define('COL_DT_DEL_PRO', 'data_exclusao');
        define('COL_USER_DEL_PRO', 'usuario_exclusao');
        define('COL_DELETED_PRO', 'excluido');
    }

    public function insert($profile)
    {

        $data = [];
        $sql = "INSERT INTO " . TB_NAME_PROFILE . "(" . COL_DESC_PRO . "," . COL_DT_CRE_PRO . "," . COL_USER_CRE_PRO . ")";
        $sql .= " VALUES(:field1,:field2,:field3)";

        $data[':field1'] = utf8_decode($profile->description);
        $data[':field2'] = date("Y-m-d H:i:s");
        $data[':field3'] = $_SESSION['user']->id;

        try {

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);

        } catch (\PDOException $ex) {

            $return['type'] = "error";
            $return['message'] = 'Erro SQL: ' . $ex->getMessage();
            echo json_encode($return);
            die;
        }

    }

    private function update($fields, $conditions = false)
    {

        $data = [];
        $sql = "UPDATE " . TB_NAME_PROFILE . " SET ";

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

        try {

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);

        } catch (\PDOException $ex) {

            $return['type'] = "error";
            $return['message'] = 'Erro SQL: ' . $ex->getMessage();
            echo json_encode($return);
            die;
        }
    }

    public function updateProfile($profile)
    {

        $fields[COL_DESC_PRO] = utf8_decode($profile->description);
        $conditions = [COL_ID_PRO => $profile->id];

        return $this->update($fields, $conditions);
    }

    public function delete($profile)
    {
        $fields = [COL_DELETED_PRO => "1", COL_DT_DEL_PRO => date("Y-m-d H:i:s"), COL_USER_DEL_PRO => $_SESSION['user']->id];
        $conditions = [COL_ID_PRO => $profile->id];

        return $this->update($fields, $conditions);
    }

    private function getProfile($conditions = false, $getOne = false)
    {

        $sql = "SELECT * FROM " . TB_NAME_PROFILE;

        $conditions[COL_DELETED_PRO] = "0";

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


            if ($getOne) {

                $profile = new Profile();

                $profile->id = $rows['0'][COL_ID_PRO];
                $profile->description = utf8_encode($rows['0'][COL_DESC_PRO]);
                $profile->date_created = $rows['0'][COL_DT_CRE_PRO];
                $profile->id_user_created = $rows['0'][COL_USER_CRE_PRO];
                $profile->date_deleted = $rows['0'][COL_DT_DEL_PRO];
                $profile->id_user_delete = $rows['0'][COL_USER_DEL_PRO];
                $profile->deleted = $rows['0'][COL_DELETED_PRO];


                return $profile;
            } else {
                return $rows;
            }

        } catch (\PDOException $ex) {
            die($ex->getMessage());
        }

    }

    public function getByID($profile)
    {
        $conditions = [COL_ID_PRO => $profile->id];
        return $this->getProfile($conditions, true);

    }

    public function getAll()
    {
        return $this->getProfile();
    }

}