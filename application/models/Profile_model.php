<?php
    class Profile_model extends CI_Model
    {
        function editUserInfo($userData,$newUserData) 
        {
            $this->db->where('userid',$userData->userid);
            $this->db->update('user',$newUserData);
            $userData->name = $userData->name === $newUserData['name'] ? $userData->name : $newUserData['name'];
            $userData->email = $userData->email === $newUserData['email'] ? $userData->email : $newUserData['email'];
            $this->session->set_userdata('user',$userData);
        }
        function editPasswordInfo($userData,$passwordData)
        {
            if($passwordData['newpassword'] === $passwordData['confpassword'])
            {
                
            }
        }
    }
?>