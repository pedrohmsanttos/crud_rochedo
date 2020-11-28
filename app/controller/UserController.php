<?php

namespace App\Controller;

use App\Model\User;
use App\Model\Profile;
use App\Facade\Facade;


class UserController
{
    private $container;
    private $db;
    private $facade;

    function __construct($container)
    {
        $this->container = $container;
        $this->db = $container->db;
        $this->facade = new Facade($this->db);
    }

    public function login($request, $response)
    {
        $args['title'] = "CRUD Rochedo";
        if (!is_null($_SESSION['user'])) {
            return $response->withRedirect(PATH . "admin/users");
        } else {
            if ($request->isPost()) {
                $data = $request->getParsedBody();
                $user = new User($data);

                $return = [];
                if ($user->login != null && $user->password != null) {
                    $user = $this->facade->getByLoginPass($user);

                    if (!$user->id == null) {
                        if ($user->blocked == "1") {
                            $args['error'] = "Usuário bloqueado!";
                        } else {
                            $user->date_last_access = date("Y-m-d H:i:s");
                            $user->password = null;
                            if($this->facade->update($user)){
                                unset($_SESSION['user']);
                                session_start();
                                $_SESSION['user'] = $user;
                                return $response->withRedirect(PATH . "admin/users");
                            }else{
                                $args['error'] = "Problema na atualização do usuário :(";
                            }

                        }
                    } else {
                        $args['error'] = "Usuário não encontrado!";
                    }
                } else {
                    $args['error'] = "Todos os campos são obrigatórios :(";
                }
            }

            return $this->container->renderer->render($response, 'user/login.phtml', $args);
        }

    }


    public function logout($request, $response)
    {
        session_start();
        unset($_SESSION['user']);
        return $response->withRedirect(PATH);
    }

    public function index($request, $response)
    {
        $args['title'] = "Usuários";

        $args['users'] = $this->facade->getAll(new User());

        return $this->container->renderer->render($response, 'user/index.phtml', $args);
    }

    public function add($request, $response)
    {
        $args['title'] = "Usuário - Adicionar";

        $args['profiles'] = $this->facade->getAll(new Profile());


        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $user = new User($data);

            $return = [];
            if ($user->is_valid()) {
                if ($this->facade->insert($user)) {
                    $return['type'] = "success";
                    $return['message'] = "Usuário cadastrado com sucesso :)";
                } else {
                    $return['type'] = "error";
                    $return['message'] = ['created' => 'Problemas ao adicionar Usuário :('];
                }
            } else {
                $return['type'] = "error";
                $return['errors'] = $user->errors;
            }

            echo json_encode($return);
            die;
        }

        return $this->container->renderer->render($response, 'user/add.phtml', $args);
    }

    public function edit($request, $response, $args)
    {

        $args['title'] = "Usuário - Editar";

        $user = $this->facade->getByID(new User($args));
        $args['user'] = $user;

        $args['profiles'] = $this->facade->getAll(new Profile());

        if ($request->isPost()) {
            $data = $request->getParsedBody();

            if (isset($data['id']) && !empty($data['id'])) {

                $user = new User($data);

                $return = [];
                if ($user->is_valid()) {
                    if ($this->facade->update($user)) {
                        $return['type'] = "success";
                        $return['message'] = "Usuário atualizado com sucesso :)";
                    } else {
                        $return['type'] = "error";
                        $return['errors'] = ['modified' => 'Problemas ao atualizar Usuário :('];
                    }
                } else {
                    $return['type'] = "error";
                    $return['errors'] = $user->errors;
                }

            } else {
                $return['type'] = "error";
                $return['errors'] = ['modified' => 'ID do perfil não pode ser vazio :('];
            }

            echo json_encode($return);
            die;

        }


        return $this->container->renderer->render($response, 'user/edit.phtml', $args);
    }

    public function delete($request, $response, $args)
    {
        $return = [];

        $user = new User($args);

        $user = $this->facade->getByID($user);

        if (!$user == null) {
            if ($this->facade->delete($user)) {
                $return['type'] = "success";
                $return['message'] = "Usuário deletado com sucesso :)";
            } else {
                $return['type'] = "success";
                $return['message'] = ['deleted' => 'Problemas ao deletar Usuário :('];
            }
        } else {
            $return['type'] = "error";
            $return['message'] = ['item' => "Registro não encontrado!"];
        }

        echo json_encode($return);
        die;


    }
}