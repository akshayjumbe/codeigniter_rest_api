<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_mdl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_user_details()
    {
        $this->db->where('status', 0);
        $query = $this->db->get('users');
        return $query->result_array();
    }

    public function get_user_details_by_id($id)
    {
        $this->db->where('status', 0);
        $this->db->where('user_id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }

    public function check_login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if (isset($email) && $email != '' && isset($password) && $password != '') {

            $query = $this->db->get_where('users', array('email' => $email, 'password' =>  md5($password)));

            $count = $query->num_rows();
            if ($count > 0) {
                $row = $query->row();
                if ($row->status == 0) {
                    $session_data = array('id' => $row->user_id, 'username' => $row->user_name, 'mobile' => $row->mobile, 'role_id' => $row->role_id, 'email' => $row->email, 'logged_in' => TRUE);
                    $this->session->set_userdata($session_data);
                    $this->session->set_flashdata('success', 'Login successfully!');
                    $auth_token = $this->token($session_data);
                    return array('data' => $session_data, 'token' => $auth_token);
                } else {
                    return array('msg' => 'Sorry, your profile is not active.');
                }
            } else {
                return array('msg' => 'Sorry, you entered the wrong email id and/or password.');
            }
        } else {
            return  array('msg' => 'Please provide proper email and password of required fields.');
        }
    }

    public function signup_users()
    {

        $email = strtolower(trim($this->input->post('email')));
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        $num = $query->num_rows();
        if ($num == 0) {
            $password = md5($this->input->post('password'));
            $user_name = $this->input->post('user_name');
            $mobile = $this->input->post('mobile');
            $role_id = $this->input->post('role_id');
            //   $date = date('Y-m-d H:i:s');
            //   $ipAddress = $_SERVER['REMOTE_ADDR'];

            $insert = array(
                'user_name' => $user_name, 'email' => $email, 'password' => $password, 'mobile' => $mobile, 'role_id' => $role_id
            );
            //   "ip_address" => $ipAddress, 'date' => $date
            $this->db->insert('users', $insert);
            return  'New User Created Succefully.';
        } else {
            return  'Opps ' . $email . ' email address already exist!';
        }
    }

    public function update_user($user_id)
    {
        $email = strtolower(trim($this->input->post('email')));
        $this->db->where('user_id != ', $user_id);
        $query = $this->db->get('users');
        $num = $query->num_rows();
        if ($num > 0) {
            $user_name = $this->input->post('user_name');
            $mobile = $this->input->post('mobile');
            $role_id = $this->input->post('role_id');
            $status  = $this->input->post('status');
            $update = array(
                'user_name' => $user_name, 'email' => $email,'mobile' => $mobile, 'role_id' => $role_id,'status'=>$status
            );
            $this->db->where('user_id', $user_id);
            $this->db->update('users', $update);
            return 'User updated succesfully';
        } else {
            return 'error|| ' . $email . '  already exist.';
        }
    }

    public function delete_entry($user_id)
    {
       return $this->db->delete('users', array("user_id" => $user_id));
        
    }

    public function token($data)
    {
        $jwt = new JWT;
        $JwtSceretKey = "Mysecretlogin";
        $token = $jwt->encode($data, $JwtSceretKey, 'HS256');
        return $token;
    }
    public function decode_token($token)
    {

        $jwt = new JWT;
        $JwtSceretKey = "Mysecretlogin";
        $decode_token = $jwt->decode($token, $JwtSceretKey, 'HS256');
        // return $decode_token;
        $json_decode = $jwt->jsonEncode($decode_token);
        return $json_decode;
    }
}
