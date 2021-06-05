<?php
    class Home extends CI_Controller 
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->helper(array('url','form','security'));
            $this->load->library(array('form_validation','session','uuid'));
            $this->load->model('User_model');
            $this->load->database();
        }
        public function index()
        {
            redirect('home/login');
        }
        public function login()
        {
            $result['loginErr'] = "";
            $result['requestedPage'] = "includes/login.html";
            if($this->input->post('loginButton'))
            {
                $this->form_validation->set_rules('txtEmail','Email',array('required','valid_email'),array('valid_email' => 'Invalid Format of Email'));
                $this->form_validation->set_rules('txtPassword','Password',array('required','min_length[8]'),array('min_length[8]' => 'Password Should be atleast of 8 characters'));
                if($this->form_validation->run() === true)
                {
                    $userInfo = [
                        "email" => $this->input->post('txtEmail'),
                        "password" => $this->input->post('txtPassword')
                    ];
                    $userData = $this->User_model->verifyUser($userInfo);
                    if($userData)
                    {
                        $this->session->set_userdata('user',$userData);
                        redirect('dashboard/todayschedule');
                    }
                    $result['loginErr'] = "Invalid Email and Password Combination";
                }
            }
            $this->load->view("main",$result);
        }
        public function register()
        {
            $result['requestedPage'] = "includes/registration.html";
            $result['registrationErr'] = "";
            if($this->input->post('regButton'))
            {
                $this->form_validation->set_rules('txtName','Name','required');
                $this->form_validation->set_rules('txtEmail','Email',array('required','valid_email'),array('valid_email' => 'Provided Email is not valid'));
                $this->form_validation->set_rules('txtPassword','Password',array('required','min_length[8]'),array('min_length[8]' => 'Password Should be atleast of 8 characters'));
                if($this->form_validation->run() === true)
                {
                    $userInfo = [
                        "name" => $this->input->post('txtName'),
                        "email" => $this->input->post('txtEmail'),
                        "password" => $this->input->post('txtPassword')
                    ];
                    if($this->User_model->createUser($userInfo))
                    {
                        redirect('home/login');
                    }
                    $result['registrationErr'] = "The Provided Email is Already in Use";
                }
            }
            $this->load->view("main",$result);
        }
    }
?>