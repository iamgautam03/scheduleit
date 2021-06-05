<?php
    class Activity_model extends CI_Model
    {
        function addActivity($activityData,$userData)
        {
            $this->db->trans_start();
                $actNumRes = $this->db->query("SELECT max(actNum) as actNum FROM users_activities")->row();
                $actNum = $actNumRes->actNum == NULL ? 1 : intval($actNumRes->actNum)+1;
                $this->db->query("INSERT INTO users_activities VALUES($actNum,$userData->userid)");
                $this->db->query("INSERT INTO activity VALUES($actNum,'$activityData[actName]',$activityData[isUrgentAct],$activityData[isImpAct],$activityData[actTimeHr],$activityData[actTimeMin])");
            $this->db->trans_complete();
            return $this->db->trans_status(); 
        }
        function editActivity($activityData,$userData,$actNum)
        {
            return $this->db->query("UPDATE activity SET actName='$activityData[actName]',isUrgent=$activityData[isUrgent],isImp=$activityData[isImp],Hours=$activityData[Hours],Minutes=$activityData[Minutes] WHERE actNum=$actNum");
        }
        function deleteActivity($activitiesNum,$userData)
        {
            $this->db->trans_start();
            foreach($activitiesNum as $activityNum)
            {
                $actNum = intval($activityNum);
                $res = $this->db->query("DELETE FROM users_activities WHERE actNum=$actNum");   
            }
            $this->db->trans_complete();
            return $this->db->trans_status();   
        }
        function getUsersActivityList($userData)
        {
            $activityList = $this->db->query("SELECT a.actNum,actName,isUrgent,isImp,Hours,Minutes FROM activity a,users_activities us WHERE a.actNum = us.actNum AND us.userid=$userData->userid;");
            return $activityList->result();
        }
    }
?>