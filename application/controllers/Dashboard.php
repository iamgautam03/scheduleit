<?php 
    class Dashboard extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->helper(array('url','form','security'));
            $this->load->library(array('form_validation','session','uuid'));
            if(!$this->session->userdata('user'))
            {
                redirect('home/login');
            }
            $this->load->database();
            $this->load->model('User_model');
        }
        public function index()
        {
            redirect('dashboard/show');
        }
        public function show($result = [])
        {
            $result['editProfileErr'] = ""; 
            $result['changePassErr'] = "";
            $result['userData'] = $this->session->userdata('user');
            $result['requestedPage'] = "includes/profile.html";
            $this->load->view("dashboard",$result);
        }
        public function updateMe()
        {
            if($this->input->post('profileEditBtn')) {
                $result['userData'] = $this->session->userdata('user');
                $this->form_validation->set_rules('txtName','Name','required');
                $this->form_validation->set_rules('txtEmail','Email',array('required','valid_email'));
                if($this->form_validation->run() === true)
                {
                    $newUserData = [
                        "name" => $this->input->post('txtName'),
                        "email" => $this->input->post('txtEmail')
                    ];
                    $res = $this->User_model->editUserInfo($result['userData'],$newUserData);
                    if($res === false) {
                        $result['editProfileErr'] = "Email is Already Associated With Another Account";
                    }
                } 
                else
                {
                    $result['editProfileErr'] = validation_errors();
                } 
            }
            redirect('dashboard/show', $result);
        }
        public function cred()
        {
            if($this->input->post('passChangeBtn')) {
                $result['userData'] = $this->session->userdata('user');
                $this->form_validation->set_rules('txtOldPass','Old Password','required');
                $this->form_validation->set_rules('txtNewPass','New Password','required');
                $this->form_validation->set_rules('txtNewPassConf','New Password Confirm','required|matches[txtNewPass]');
                if($this->form_validation->run() === true)
                {
                    $passwordData = [
                        "oldPassword" => $this->input->post('txtOldPass'),
                        "newPassword" => $this->input->post('txtNewPass'),
                        "confPassword" => $this->input->post('txtNewPassConf')
                    ];
                    $res = $this->User_model->editPasswordInfo($result['userData'],$passwordData);
                    if($res  === true) {
                        redirect('dashboard/logout');
                    }
                    $result['changePassErr'] = "Wrong Password";
                }
                else
                {
                    $result['changePassErr'] = validation_errors();
                }
            }
            redirect('dashboard/show', $result);
        }
        public function logout()
        {
            $this->session->unset_userdata('user');
            redirect('home/login');
        }
    }
?>