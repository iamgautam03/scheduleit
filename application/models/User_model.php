<?php
    class User_model extends CI_Model
    {
        function createUser($userInfo)
        {
            $userInfo['password'] = password_hash($userInfo['password'],PASSWORD_BCRYPT);
            $oldState = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            $res = $this->db->query("INSERT INTO user VALUES(NULL,'$userInfo[name]','$userInfo[email]','$userInfo[password]')");
            $this->db->db_debug = $oldState;
            return $res;
        }
        function verifyUser($userInfo)
        {
            $query = $this->db->query("SELECT * FROM user WHERE email='$userInfo[email]';");
            $userData = $query->row();
            if(isset($userData) && password_verify($userInfo['password'],$userData->password))
            {
                return $userData;
            }
            return false;
        }
        function editUserInfo($userData,$newUserData) 
        {
            $this->db->db_debug = FALSE;
            $this->db->trans_start();
                $this->db->query("UPDATE user SET name='$newUserData[name]',email='$newUserData[email]'  WHERE userid=$userData->userid");
            $this->db->trans_complete();    
            $res = $this->db->trans_status();
            if($res)
            {
                $userData->name = $userData->name === $newUserData['name'] ? $userData->name : $newUserData['name'];
                $userData->email = $userData->email === $newUserData['email'] ? $userData->email : $newUserData['email'];
                $this->session->set_userdata('user',$userData);
            }
            return $res;
        }
        function editPasswordInfo($userData,$passwordData)
        {
            $newPassword = password_hash($passwordData['newPassword'],PASSWORD_BCRYPT);
            $userInfo = (array) $userData;
            $userInfo['password'] = $passwordData['oldPassword'];
            $res = $this->verifyUser($userInfo);
            if($res !== false) {
                return $this->db->query("UPDATE user SET password='$newPassword' WHERE userid=$userInfo[userid]");
            }
            return false;
        }
    }
?>