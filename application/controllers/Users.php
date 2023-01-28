<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class Users extends RestController {
	public function __construct()
	{
		parent::__construct();
        $this->load->model('auth_mdl');
    }
    public function index_get()
    { 
        $get_users= $this->auth_mdl->get_user_details();
        $this->response($get_users,200);
    }

    public function signup_post()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == true) {
          $result = $this->auth_mdl->signup_users();
          if($result > 0){
            $this->response(
                [
                    'status'=>true,
                    'msg' => $result
                ],RestController::HTTP_OK);
          }else{
            $this->response(
                [
                    'status'=>false,
                    'msg' =>'Failed to create user'
                ],RestController::HTTP_BAD_REQUEST);
          }
        }else{
            $this->response(
                [
                    'status'=>false,
                    'msg' =>'please fill the required field'
                ],RestController::HTTP_NOT_FOUND);
        }

    }

    public function login_post()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == true) {
            $result = $this->auth_mdl->check_login();
            if($result > 0){
                $this->response($result,200);
              }else{
                $this->response(
                    [
                        'status'=>false,
                        'msg' => 'Faild to loggedIn'
                    ],RestController::HTTP_BAD_REQUEST);
              }
        } else {
            $this->response(
                [
                    'status'=>false,
                    'msg' =>'please fill the required field'
                ],RestController::HTTP_NOT_FOUND);
        }
    }
    
    public function edit_get($id)
    {
      
       $result = $this->auth_mdl->get_user_details_by_id($id);
       if($result != null){
           $this->response($result,200);
       }else{
        $this->response(
            [
                'status'=>false,
                'msg' => 'Faild to get user'
            ],RestController::HTTP_BAD_REQUEST);
      }
    }

    public function update_post($id)
    {
        $result = $this->auth_mdl->update_user($id);
        if($result > 0 ){
            $this->response(
                [
                    'status'=>true,
                    'msg' => $result
                ],RestController::HTTP_OK);
        }else{
         $this->response(
             [
                 'status'=>false,
                 'msg' => 'Faild to update user'
             ],RestController::HTTP_BAD_REQUEST);
       }
    }

    public function deleteuser_delete($id)
    {
        $result = $this->auth_mdl->delete_entry($id);
        if($result > 0 ){
            $this->response(
                [
                    'status'=>true,
                    'msg' => 'Deleted successfully!'
                ],RestController::HTTP_OK);
        }else{
         $this->response(
             [
                 'status'=>false,
                 'msg' => 'Faild to delete user'
             ],RestController::HTTP_BAD_REQUEST);
       }
    }

}