<?php
    class Schedule extends CI_Controller
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
            $this->load->model('Schedule_model');
        }
        public function index()
        {
            redirect('schedule/show');
        }
        public function show()
        {
            $this->load->model('Activity_model');
            $result['userData'] = $this->session->userdata('user');
            $activities = $this->Activity_model->getUsersActivityList($result['userData']);
            if ($activities)
            {
                $actListMaps = array();
                foreach ($activities as $activity)
                {
                    $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                    $uuid = $this->uuid->conv_byte2string($uuidres);
                    $actListMaps[$uuid] = $activity->actNum;
                    $activity->actNum = $uuid;
                }
                $this->session->set_userdata('actList',$actListMaps);
            }
            $result['activityList'] = $activities;
            $schedules = $this->Schedule_model->getScheduleNames($result['userData']->userid);
            if($schedules)
            {
                $scheduleListMaps = array();
                foreach($schedules as $schedule)
                {
                    $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                    $uuid = $this->uuid->conv_byte2string($uuidres);
                    $scheduleListMaps[$uuid] = $schedule->s_id;
                    $schedule->s_id = $uuid;
                }
                $this->session->set_userdata('scheduleList',$scheduleListMaps);
            }
            $result['schedules'] = $schedules;
            $result['requestedPage'] = "includes/schedule.html";
            $this->load->view("dashboard",$result);
        }
        public function get()
        {
            $result['userData'] = $this->session->userdata('user');
            $this->form_validation->set_rules('scheduleID','Schedule','required',array('required' => 'Invalid Schedule'));
            if($this->form_validation->run() === true)
            {
                $s_uuid = $this->input->post('scheduleID');
                $s_id = $this->session->userdata('scheduleList')[$s_uuid];
                $res = $this->Schedule_model->getScheduleData($result['userData']->userid,$s_id);
                foreach($res as $r)
                {
                    $r->actNum = array_search($r->actNum,$this->session->userdata('actList'));
                }
                $dataToSend = json_encode($res);
            }
            else
            {
                $dataToSend = validation_errors();
            }
            echo $dataToSend;
        }
        public function create()
        {
            $this->load->model("Schedule_model");
            $result['userData'] = $this->session->userdata('user');
            $scheduleData = [];
            $this->form_validation->set_rules('scheduleName','Schedule Name','required',array('required'=>'Schedule Name cannot be Empty'));
            $this->form_validation->set_rules('scheduleData','Schedule Data','required|callback_checkScheduleData',array('required' => 'You Provided an Empty Schedule','checkScheduleData' => 'Provided Schedule is Not Valid'));
            if($this->form_validation->run() === true)
            {
                $scheduleData = $GLOBALS['scheduleData'];
                $actUUIDLists = $this->session->userdata('actList');
                $scheduleName = $this->input->post('scheduleName');
                foreach($scheduleData as &$schedule)
                {
                    $schedule['actNum'] = $actUUIDLists[$schedule['actNum']];
                }
                $res = $this->Schedule_model->createSchedule($result['userData']->userid,$scheduleName,$scheduleData);
            }
            else 
            {
                $res = validation_errors();
            } 
            echo $res;
        }
        public function update()
        {
            $this->load->model("Schedule_model");
            $result['userData'] = $this->session->userdata('user');
            $scheduleData = [];
            $this->form_validation->set_rules('scheduleID','Schedule ID','required',array('required' => 'Please Select Schedule and then Edit'));
            $this->form_validation->set_rules('scheduleName','Schedule Name','required',array('required'=>'Schedule Name cannot be Empty'));
            $this->form_validation->set_rules('scheduleData','Schedule Data','required|callback_checkScheduleData',array('required' => 'You Provided an Empty Schedule','checkScheduleData' => 'Provided Schedule is Not Valid'));
            if($this->form_validation->run() === true)
            {
                $scheduleName = $this->input->post('scheduleName');
                $scheduleID = $this->input->post('scheduleID');
                $scheduleUUIDs = $this->session->userdata('scheduleList');
                $scheduleID = intval($scheduleUUIDs[$scheduleID]);
                $scheduleData = $GLOBALS['scheduleData'];
                foreach($scheduleData as &$schedule)
                {
                    $schedule['actNum'] = $this->session->userdata('actList')[$schedule['actNum']];
                }
                $res = $this->Schedule_model->editSchedule($scheduleID,$result['userData']->userid,$scheduleName,$scheduleData);
            }
            else
            {
                $res = validation_errors();
            }  
            echo $res;
        }
        public function delete()
        {
            $result['userData'] = $this->session->userdata('user');
            $this->form_validation->set_rules('scheduleIdsToDelete','Schedule ID','required|callback_checkScheduleID',array('required' => 'Please Select Schedules First And Then Click on Delete','checkScheduleID' => 'Please Refresh the Page and Try Again'));
            if($this->form_validation->run() === true)
            {
                $scheduleIDs = $GLOBALS['scheduleIDs'];
                $sIds = array();
                foreach($scheduleIDs as $scheduleUUID) {
                    $scheduleID = $this->session->userdata('scheduleList')[$scheduleUUID];
                    array_push($sIds, $scheduleID);
                }
                $res = $this->Schedule_model->deleteSchedules($sIds, $result['userData']->userid);
            }
            else 
            {
                $res = validation_errors();
            }
            echo $res;
        }
        public function today()
        {
            $result['userData'] = $this->session->userdata('user');
            $todaysSchedule = $this->Schedule_model->getTodaysSchedule($result['userData']->userid);
            if(!empty($todaysSchedule))
            {
                $tdySchedule = $todaysSchedule[0];
                $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                $s_uuid = $this->uuid->conv_byte2string($uuidres);
                $tdyScheduleMaps['s_id'][$s_uuid] = $tdySchedule->s_id;
                $tdySchedule->s_id = $s_uuid;
                foreach($todaysSchedule as $tdySchedule)
                {
                    $tdySchedule->s_id = $s_uuid;
                    $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                    $uuid = $this->uuid->conv_byte2string($uuidres);
                    $tdyScheduleMaps['seqNum'][$uuid] = $tdySchedule->seqNum;
                    $tdySchedule->seqNum = $uuid;
                }
            $this->session->set_userdata('tdySchedule',$tdyScheduleMaps);
            }
            $result['todaysSchedule'] = $todaysSchedule;
            $result['requestedPage'] = "includes/todayschedule.html";
            $this->load->view("dashboard",$result);
        }
        public function markTodays()
        {
            $this->form_validation->set_rules('txtSid','Schedule ID','required',array('required' => 'Please Refresh the Page'));
           
            if($this->form_validation->run() === true)
            {
                $tdyScheduleMaps = $this->session->userdata('tdySchedule');
                $s_id = intval($tdyScheduleMaps['s_id'][$this->input->post('txtSid')]);
                $updData = explode('*',$this->input->post('txtUpd'));
                $delData = explode('*',$this->input->post('txtDel'));
                $updateData = [];
                $deleteData = [];
                $res1 = true;
                $res2 = true;
                if($updData[0]!= "")
                {
                    foreach($updData as $ud)
                    {
                        array_push($updateData,json_decode($ud,true));
                    }
                }
                if($delData[0]!="")
                {
                    foreach($delData as $dd)
                    {
                        $seqNum = intval($tdyScheduleMaps['seqNum'][$dd]);
                        array_push($deleteData,$seqNum);
                    }
                }
                foreach($updateData as &$data)
                {
                    $data['seqNum'] = intval($tdyScheduleMaps['seqNum'][$data['seqNum']]);
                }
                if(!empty($updateData))
                {
                    $res1 = $this->Schedule_model->updateTodaysSchedule($s_id,$updateData);
                }
                if(!empty($deleteData))
                {
                    $res2 = $this->Schedule_model->deleteTodaysSchedule($s_id,$deleteData);
                }
                $res = ($res1 && $res2);
            }
            else
            {
                $res = validation_errors();
            }
            echo $res;
        }
        public function manage()
        {
            $result['userData'] = $this->session->userdata('user');
            $schedules = $this->Schedule_model->getScheduleNames($result['userData']->userid);
            if($schedules)
            {
                $scheduleListMaps = array();
                foreach($schedules as $schedule)
                {
                    $uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
                    $uuid = $this->uuid->conv_byte2string($uuidres);
                    $scheduleListMaps[$uuid] = $schedule->s_id;
                    $schedule->s_id = $uuid;
                }
                $this->session->set_userdata('scheduleList',$scheduleListMaps);
            }
            $result['schedules'] = $schedules;
            $assignedSchedules = $this->Schedule_model->getAssignedScheduleNames($result['userData']->userid);

            foreach($assignedSchedules as $schedule)
            {
                $schedule->s_id = array_search($schedule->s_id,$scheduleListMaps);
            }
            $result['assignedSchedules'] = $assignedSchedules;
            $result['requestedPage'] = "includes/setschedules.html";
            $this->load->view("dashboard",$result);
        }
        public function set()
        {
            $data = $this->input->post('assignedSchedules');
            $userData = $this->session->userdata('user');
            $assignSchedules = array();
            $this->form_validation->set_rules('assignedSchedules','Schedule','required',array('required' => 'Please Specify Date'));
            if($this->form_validation->run() === true)
            {
                $temp = explode('*',$data);
                foreach($temp as $t)
                {
                    array_push($assignSchedules,json_decode($t,true));
                }
                foreach($assignSchedules as &$assignSchedule)
                {
                    $assignSchedule['sid'] = $this->session->userdata('scheduleList')[$assignSchedule['sid']];
                }
                $res = $this->Schedule_model->assignScheduleDates($assignSchedules,$userData->userid);
            }
            else
            {
                $res = validation_errors();
            }
            echo $res;
        }
        public function change()
        {
            $this->load->model("Schedule_model");
            $this->form_validation->set_rules('txtSID','Schedule ID','required',array('required'=>'Please Select Schedule'));
            $this->form_validation->set_rules('txtOldScheduleDate','Old Date','required',array('required' => 'Please Select Date'));
            $this->form_validation->set_rules('txtNewScheduleDate','New Date','required',array('required' => 'Pleaes Select New Date'));
            $userData = $this->session->userdata('user');
            if($this->form_validation->run() === true)
            {
                $s_id = intval($this->session->userdata('scheduleList')[$this->input->post('txtSID')]);
                $oldScheduleDate = $this->input->post('txtOldScheduleDate');
                $newScheduleDate = $this->input->post('txtNewScheduleDate');
                $res = $this->Schedule_model->editAssignedSchedule($s_id,$oldScheduleDate,$newScheduleDate,$userData->userid);
            }
            else
            {
                $res = validation_errors();
            }
            echo $res;
        }
        public function unset()
        {
            $this->load->model("Schedule_model");
            $data = $this->input->post('scheduleToUnassign');
            $userData = $this->session->userdata('user');
            $this->form_validation->set_rules('scheduleToUnassign','Schedule','required',array('required' => 'Please Refresh and Try Again'));
            if($this->form_validation->run() === true)
            {
                $assignSchedules = array();
                $temp = explode('*',$data);
                foreach($temp as $t)
                {
                    array_push($assignSchedules,json_decode($t,true));
                }
                foreach($assignSchedules as &$schedule)
                {
                    $schedule['sid'] = intval($this->session->userdata('scheduleList')[$schedule['sid']]);
                }
                $res = $this->Schedule_model->unassignScheduleDates($assignSchedules,$userData->userid);
            }
            else
            {
                $res = validation_errors();
            }
            echo $res;
        }
        public function checkScheduleData($scheduleData)
        {
            $preparedScheduleData = [];
            $data = explode("*",$scheduleData);
            foreach($data as $d)
            {
                $temp = json_decode($d,true);
                if(json_last_error() !== 0)
                {
                    return false;
                }
                else
                {
                    if(empty($temp['actNum']) || empty($temp['actStartTime']) || empty($temp['actEndTime']))
                    {
                        return false;
                    }
                    else if(!preg_match(UUID_REGEX,$temp['actNum']))
                    {
                        return false;
                    }
                    else
                    {
                        array_push($preparedScheduleData, $temp);
                    }
                }
            }
            $GLOBALS['scheduleData'] = $preparedScheduleData;
            return true;
        }
        public function checkScheduleID($s_ids)
        {
            $data = explode("*",$s_ids);
            foreach($data as $s_id)
            {
                if(!preg_match(UUID_REGEX,$s_id))
                {
                    return false;
                }
            }
            $GLOBALS['scheduleIDs'] = $data;
            return true;
        }
    }
?>