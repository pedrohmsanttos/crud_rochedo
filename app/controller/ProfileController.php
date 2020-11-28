<?php

namespace App\Controller;

use App\Facade\Facade;
use App\Model\Profile;

class ProfileController{

    private $container;
    private $db;
    private $facade;

    function __construct($container){
        $this->container = $container;
        $this->db = $container->db;
        $this->facade = new Facade($this->db);
    }

    public function index($request, $response){
        $args['title'] = "Perfis";
        $args['profiles'] = $this->facade->getAll(new Profile());

        return $this->container->renderer->render($response, 'profile/index.phtml', $args);
    }

    public function add($request, $response){

        $args['title'] = "Perfil - Adicionar";


        if($request->isPost()){
            $data = $request->getParsedBody();

            $profile = new Profile($data);

            $return = [];
            if($profile->is_valid()){
                if($this->facade->insert($profile)){
                    $return['type'] = "success";
                    $return['message'] = "Perfil cadastrado com sucesso :)";
                }else{
                    $return['type'] = "error";
                    $return['message'] = ['created' => 'Problemas ao adicionar Perfil :('];
                }
            }else{
                $return['type'] = "error";
                $return['errors'] = $profile->errors;
            }

            echo json_encode($return);die;
        }

        return $this->container->renderer->render($response, 'profile/add.phtml', $args);
    }

    public function edit($request, $response, $args){
        $args['title'] = "Perfil - Editar";

        $profile = new Profile($args);
        $profile = $this->facade->getByID($profile);
        $args['profile'] = $profile;

        if($request->isPost()){
            $data = $request->getParsedBody();

            if(isset($data['id']) && !empty($data['id'])){

                $profile = new Profile($data);

                $return = [];
                if($profile->is_valid()){
                    if($this->facade->update($profile)){
                        $return['type'] = "success";
                        $return['message'] = "Perfil atualizado com sucesso :)";
                    }else{
                        $return['type'] = "error";
                        $return['errors'] = ['modified' => 'Problemas ao atualizar Perfil :('];
                    }
                }else{
                    $return['type'] = "error";
                    $return['errors'] = $profile->errors;
                }

            }else{
                $return['type'] = "error";
                $return['errors'] = ['modified' => 'ID do perfil não pode ser vazio :('];
            }

            echo json_encode($return);die;

        }



        return $this->container->renderer->render($response, 'profile/edit.phtml', $args);
    }

    public function delete($request, $response, $args){

        $return = [];

        $profile = new Profile($args);

        $profile = $this->facade->getByID($profile);

        if(!$profile == null){
            if($this->facade->delete($profile)){
                $return['type'] = "success";
                $return['message'] = "Perfil deletado com sucesso :)";
            }else{
                $return['type'] = "success";
                $return['message'] = ['deleted' => 'Problemas ao deletar Perfil :('];
            }
        }else{
            $return['type'] = "error";
            $return['message'] = ['item' => "Registro não encontrado!"];
        }

        echo json_encode($return);die;

    }
}