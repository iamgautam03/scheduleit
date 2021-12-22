<?php
    class Activity extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->helper(array('url','form','security'));
            $this->load->library(array('form_validation','session','uuid'));
            if (!$this->session->userdata('user'))
            {
                redirect('home/login');
            }
            $this->load->database();
            $this->load->model('Activity_model');
        }
        public function index()
        {
            redirect('activity/show');
        }
        public function show($activityErr = "")
        {
            $result['activityErr'] = $activityErr;
            $result['userData'] = $this->session->userdata('user');
            $result['requestedPage'] = "includes/activity.html";
            $activities = $this->Activity_model->getUsersActivityList($result['userData']);
            if($activities)
            {
                $actListMaps = array();
                foreach($activities as $activity)
                {
                    $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                    $uuid = $this->uuid->conv_byte2string($uuidres);
                    $actListMaps[$uuid] = $activity->actNum;
                    $activity->actNum = $uuid;
                }
                $this->session->set_userdata('actList',$actListMaps);
            }
            $result['activityList'] = $activities;
            $this->load->view("dashboard", $result);
        }
        public function add()
        {
            $result['userData'] = $this->session->userdata('user');
            if($this->input->post('addActivity'))
            {
                $this->form_validation->set_rules('txtActName','Activity Name','required');
                $this->form_validation->set_rules('txtActTimeHrs','Hours','numeric|required|greater_than[-1]|less_than[24]');
                $this->form_validation->set_rules('txtActTimeMins','Minutes','numeric|required|greater_than[-1]|less_than[60]');
                if($this->form_validation->run() === true)
                {
                    $isUrgent = $this->input->post('txtActIsUrgent');
                    $isImp = $this->input->post('txtActIsImp');
                    $userActivityData = [
                        "actName" => $this->input->post('txtActName'),
                        "actTimeHr" => $this->input->post('txtActTimeHrs') == NULL ? 0 : $this->input->post('txtActTimeHrs'),
                        "actTimeMin" => $this->input->post('txtActTimeMins') == NULL ? 0 : $this->input->post('txtActTimeMins'),
                        "isUrgentAct" => isset($isUrgent) ? 1 : 0,
                        "isImpAct" => isset($isImp) ? 1 : 0
                    ];
                    $result['activityErr'] = $this->Activity_model->addActivity($userActivityData,$result['userData']);
                }
            }
            redirect('activity/show', $result['activityErr']);
        }
        public function update($activityId)
        {
            $result['userData'] = $this->session->userdata('user');
            if($this->input->post('editActivity'))
            {
                $this->form_validation->set_rules('actID','ID','required|callback_checkActNum',array('checkActNum' => 'Please Refresh and Try Again'));
                $this->form_validation->set_rules('actName','Activity Name','required');
                $this->form_validation->set_rules('actTimeHrs','Hours','numeric|required|greater_than[-1]|less_than[24]');
                $this->form_validation->set_rules('actTimeMins','Minutes','numeric|required|greater_than[-1]|less_than[60]');
                if($this->form_validation->run() === true)
                {
                    $isUrgent = $this->input->post('actIsUrgent');
                    $isImp = $this->input->post('actIsImp');
                    $actNum = $this->input->post('actID');
                    $actUUID = $this->input->post('actID');
                    $actNum = $this->session->userdata('actList')[$actUUID];
                    $userActivityData = [
                        "actName" => $this->input->post('actName'),
                        "Hours" => $this->input->post('actTimeHrs') == NULL ? 0 : $this->input->post('actTimeHrs'),
                        "Minutes" => $this->input->post('actTimeMins') == NULL ? 0 : $this->input->post('actTimeMins'),
                        "isUrgent" => isset($isUrgent) ? 1 : 0,
                        "isImp" => isset($isImp) ? 1 : 0
                    ];
                    $result['activityErr'] = $this->Activity_model->editActivity($userActivityData,$result['userData'],$actNum);
                    $result['activityList'] = $this->Activity_model->getUsersActivityList($result['userData']);
                }
            }
            redirect('activity/show', $result['activityErr']);
        }
        public function delete()
        {
            $this->load->model('Activity_model');
            $result['userData'] = $this->session->userdata('user');
            $actsNumToDelete = explode("*",$this->input->post("actToDelete"));
            foreach($actsNumToDelete as &$actNum)
            {
                $actNum = $this->session->userdata('actList')[$actNum];
            }
            $res = $this->Activity_model->deleteActivity($actsNumToDelete,$result['userData']);
            echo $res;
        }
        public function checkActNum($actNum)
        {
            if(!preg_match(UUID_REGEX,$actNum))
            {
                return false;
            }
            return true;
        }
    }
?>