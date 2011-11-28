<?php

class news {

    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllNews() {
        $sql = "SELECT * FROM news ORDER BY newsdate DESC";
        
        return $this->db->get_rows($sql);
    }
    
    public function getNewsItem($newsItemId) {
        $sql = sprintf("SELECT * FROM news WHERE news_id = %d", $newsItemId);

        return $this->db->get_row($sql);
    }
    
    public function saveNewsItem($save) {
        if(isset($save['news_id'])) {
            $news_id = (int) $save['news_id'];
            unset($save['news_id']);
        }
        
        if($news_id) {
            $sqlCmd = 'UPDATE';
            $where  = sprintf('WHERE news_id = %d', $news_id);
        } else {
            $sqlCmd = 'INSERT';
            $where  = '';
        }
        
        $sql = sprintf("%s news SET title = '%s', news_image = '%s', preview = '%s', content = '%s', newsdate = '%s', published = '%s' %s", $sqlCmd, $this->clean($save['news_title']), $this->clean($save['news_image']), $this->clean($save['preview_text']), $this->clean($save['news_text']), $save['news_date'], $save['published'], $where);

        if($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function clean($val) {
        return mysql_real_escape_string($val);
    }
    
    public function deleteNewsItem($newsItemId) {
        $sql = "DELETE FROM news WHERE news_id = " . $newsItemId;
        
        if($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
}

?>