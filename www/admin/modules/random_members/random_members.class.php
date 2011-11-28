<?php

class Random_Members {

    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllMembers() {
        $sql = "SELECT * FROM members ORDER BY client_name ASC";
        
        return $this->db->get_rows($sql);
    }
    
    public function getMember($memberId) {
        $sql = sprintf("SELECT * FROM members WHERE member_id = %d", $memberId);

        return $this->db->get_row($sql);
    }
    
    public function saveMember($save) {
        if(isset($save['member_id'])) {
            $member_id = (int) $save['member_id'];
            unset($save['member_id']);
        }
        
        if($member_id) {
            $sqlCmd = 'UPDATE';
            $where  = sprintf('WHERE member_id = %d', $member_id);
        } else {
            $sqlCmd = 'INSERT';
            $where  = '';
        }
        
        $sql = sprintf("%s members SET client_name = '%s', summary = '%s', full_text = '%s', logo = '%s', website = '%s' %s", $sqlCmd, $this->clean($save['client_name']), $this->clean($save['summary']), $this->clean($save['full_text']), $this->clean($save['logo']), $this->clean($save['website']), $where);

        if($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function clean($val) {
        return mysql_real_escape_string($val);
    }
    
    public function deleteMember($memberId) {
        $sql = "DELETE FROM members WHERE member_id = " . $memberId;
        
        if($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
}

?>