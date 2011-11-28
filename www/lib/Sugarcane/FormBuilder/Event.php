<?php

class Sugarcane_FormBuilder_Event extends Sugarcane_FormBuilder_Form
{
    protected $event_id;
    
    public function __construct($fields, $data, $event_id)
    {
        $this->fields   = $fields;
        $this->data     = $data;
        $this->event_id = $event_id;
    }
    
    /**
     * Save the form
     */
    public function save($dbMapper)
    {
        // Align the form to the post data and work out which fields are required
        $required_fields = array();
        
        foreach($this->fields as $field) {
            $formData[$field['fieldname']]['value']          = $this->data[$field['fieldname']];
            $formData[$field['fieldname']]['event_field_id'] = $field['event_field_id'];
            if($field['required'] == 'Y') {
                $required_fields[$field['fieldname']] = "The field <strong>'" . $field['label'] . "'</strong> is required";
            }
        }
        
        // Check the required fields have been filled in
        $errors = array();
        
        foreach($required_fields as $required_field=>$error) {
            if($formData[$required_field]['value'] == '') {
                $errors[] = $error;
            }
        }
        
        if(!count($errors)) {
            foreach($formData as $data) {
                $save['event_data_id']  = $dbMapper->getAdditionalEventDetail($this->event_id, $data['event_field_id']);
                $save['event_id']       = $this->event_id;
                $save['event_field_id'] = $data['event_field_id'];
                $save['fieldvalue']     = $data['value'];
                
                $dbMapper->saveRecord($save, 'events_data', 'event_data_id');
            }
            
            return true;
        } else {
            $_SESSION['formdata']['data']   = $this->data;
            $_SESSION['formdata']['errors'] = $errors;
            return false;
        }
    }
}