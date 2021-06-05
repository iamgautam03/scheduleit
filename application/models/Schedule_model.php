<?php
    class Schedule_model extends CI_Model
    {
        function createSchedule($userid,$scheduleName,$scheduleData)
        {
            $this->db->trans_start();
                $sidRes = $this->db->query("SELECT MAX(s_id) as id FROM users_schedules")->row();
                $s_id = $sidRes->id == NULL ? 1 : intval($sidRes->id)+1;
                $cnt = 1;
                $temp = $this->db->query("INSERT INTO users_schedules VALUES($s_id,'$scheduleName',$userid)");
                foreach($scheduleData as $sd)
                {
                   $actNum = intval($sd['actNum']);
                   $actStartTime = $sd['actStartTime'];
                   $actEndTime = $sd['actEndTime'];
                   $temp = $this->db->query("INSERT INTO schedule VALUES($s_id,$cnt,$actNum,'$actStartTime','$actEndTime')");
                   $cnt = $cnt + 1;
                }
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        function deleteSchedules($s_ids,$userid)
        {
            $this->db->trans_start();
                foreach($s_ids as $s_id)
                {
                    $sid = intval($s_id);
                    $temp = $this->db->query("DELETE FROM users_schedules WHERE s_id = $sid");
                }
            $this->db->trans_complete();
            return $this->db->trans_status(); 
        }
        function editSchedule($s_id,$userid,$scheduleName,$scheduleData)
        {
            $this->db->trans_start();
                $cnt = 1;
                $temp = $this->db->query("DELETE FROM schedule WHERE s_id = $s_id");
                foreach($scheduleData as $sd)
                {
                    $actNum = intval($sd['actNum']);
                    $actStartTime = $sd['actStartTime'];
                    $actEndTime = $sd['actEndTime'];
                    $temp = $this->db->query("INSERT INTO schedule VALUES($s_id,$cnt,$actNum,'$actStartTime','$actEndTime')");
                    if(isset($oldData))
                    {
                        $temp = $this->db->query("UPDATE todaysschedule SET isActCompleted = '$oldData->isActCompleted' WHERE s_id = $s_id AND seqNum = $cnt");
                    }
                    $cnt += 1;
                }
                $temp = $this->db->query("UPDATE users_schedules SET scheduleName = '$scheduleName' WHERE s_id = $s_id");
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        function assignScheduleDates($scheduleData,$userid)
        {
            $this->db->trans_start();
                foreach($scheduleData as $data)
                {
                    $sid = intval($data['sid']);
                    $sDate = date_create_from_format('jS M Y',$data['sdate']);
                    $sDate = date_format($sDate,"Y-m-d");
                    $temp = $this->db->query("CALL AssignedScheduleIns($sid,'$sDate',$userid)");
                }
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        function updateTodaysSchedule($s_id,$updateData)
        {
            if($updateData)
            {
                $this->db->trans_start();
                    foreach($updateData as $ud)
                    {
                        $seqNum = intval($ud['seqNum']);
                        $temp = $this->db->query("UPDATE todays_schedule SET isActCompleted = '$ud[isActCompleted]' WHERE s_id = $s_id AND seqNum = $seqNum");
                    }
                $this->db->trans_complete();
                return $this->db->trans_status();
            }
            return true;
        }
        function deleteTodaysSchedule($s_id,$deleteData)
        {
            if($deleteData)
            {
                $this->db->trans_start();
                foreach($deleteData as $dd)
                {
                    $temp = $this->db->query("DELETE FROM todays_schedule WHERE s_id = $s_id AND seqNum = $dd");
                }
                $this->db->trans_complete();
                return $this->db->trans_status();
            }
            return 1;
        }
        function unassignScheduleDates($scheduleData)
        {
            $this->db->trans_start();
                foreach($scheduleData as $data)
                {
                    $sid = intval($data['sid']);
                    $sDate = date_create_from_format('jS M Y',$data['sdate']);
                    $sDate = date_format($sDate,"Y-m-d");
                    $temp = $this->db->query("CALL AssignedScheduleDel($sid,'$sDate')");
                }
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        function editAssignedSchedule($s_id,$oldScheduleDate,$newScheduleDate,$userid)
        {
            $this->db->trans_start();
                $this->db->query("CALL AssignedScheduleUpd($s_id,'$oldScheduleDate','$newScheduleDate',$userid)");
            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        function getTodaysSchedule($userid)
        {
            $todaysSchedule = $this->db->query("SELECT ts.s_id,ts.seqNum,a.actName,a.isUrgent,a.isImp,DATE_FORMAT(s.actStartTime,'%h:%i %p') as actStartTime,DATE_FORMAT(s.actEndTime,'%h:%i %p') as actEndTime,ts.isActCompleted FROM todays_schedule ts,users_schedules us,schedule s,activity a WHERE ts.s_id = s.s_id AND us.s_id = s.s_id AND us.userid = $userid AND ts.seqNum = s.seqNum AND s.actNum = a.actNum ORDER BY s.actStartTime");
            return $todaysSchedule->result();
        }
        function getScheduleNames($userid)
        {
            $scheduleList = $this->db->query("SELECT s_id,scheduleName FROM users_schedules WHERE userid = $userid");
            return $scheduleList->result();
        }
        function getAssignedScheduleNames($userid)
        {
            $scheduleList = $this->db->query("SELECT a.s_id,a.scheduleDate,us.scheduleName FROM users_schedules us,assigned_schedules a WHERE us.s_id = a.s_id AND us.userid = $userid ORDER BY scheduleDate");
            return $scheduleList->result();
        }
        function getScheduleData($userid,$s_id) 
        {
            $s_id = intval($s_id);
            $scheduleData = $this->db->query("SELECT DISTINCT s.s_id,seqNum,s.actNum,a.actName,actStartTime,actEndTime FROM schedule s,activity a,users_schedules us WHERE s.actNum = a.actNum AND s.s_id=$s_id AND us.userid = $userid ORDER BY actStartTime");
            return $scheduleData->result();
        }
    }
?>