<?php

class Sugarcane_FormBuilder_Form
{
    protected $fields;
    protected $data;
    protected $html = '';
    
    public function __construct($fields, $data)
    {
        $this->fields = $fields;
        $this->data   = $data;
    }
    
    /**
     * Render the form HTML and pass it back to the calling class
     */
    public function renderForm()
    {
        // Check we have an array of fields
        if(!is_array($this->fields)) {
            throw new Exception('Form fields are not an array');
        }
        
        // Loop the fields of the form calling the object of each to get the HTML
        foreach($this->fields as $field) {
            // var_dump($field); echo '<br><br>';
            // var_dump($this->data); echo '<br><br>';
    
            // Do we have any data to put in this field?
            $value = (isset($this->data[$field['fieldname']])) ? $this->data[$field['fieldname']] : '';
            
            //var_dump($value);
            
            //var_dump($this->data[$field['fieldname']]);
                    
            switch($field['field_type']) {
                case 'textbox':
                    $textbox = new Sugarcane_FormBuilder_Textbox($field, $value);
                    $this->html .= $textbox->render();
                    break;
                case 'dropdown':
                    $dropdown = new Sugarcane_FormBuilder_Dropdown($field, $value);
                    $this->html .= $dropdown->render();
                    break;
            }
        }
        
        // Now we have all the HTML, lets push it back to the controller
        return $this->html;
    }
    
    /**
     * Save the form
     */
    public function save($db, $dbTable = 'form_data')
    {
        
    }
}