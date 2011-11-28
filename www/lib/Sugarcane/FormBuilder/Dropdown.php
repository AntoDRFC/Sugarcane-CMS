<?php

class Sugarcane_FormBuilder_Dropdown
{
    protected $field;
    protected $value;
    
    protected $dropdownOptions = array();
    
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }
    
    public function render()
    {
        // Manipulate the form fields / data here before sending the field to be rendered
        $options = explode('::', $this->field['extra_details']);
        foreach($options as $option) {
            $this->dropdownOptions[$option] = $option;
        }
        
        return $this->renderField();
    }
    
    
    protected function renderField()
    {
        // Setup the template for the field
        $template = '<li>
                         <label for="%s" id="%1$s_label" class="desc">%s</label>
                         <select name="%1$s" id="%1$s">
                            <option value="">- select -</option>
                            %s
                         </select> %s
                     </li>';
        
        // Is the field required? If so append a *
        $required = ($this->field['required'] == 'Y') ? '*' : '';
        
        return sprintf($template, $this->field['fieldname'],
                                  $this->field['label'],
                                  Globals::makeOptions($this->dropdownOptions, $this->value),
                                  $required);
    }
    
    protected function renderHTML()
    {
        /*
        <li>
            <label for="fieldname" id="fieldname">label</label>
            value
        </li>
        */
    }
    
    protected function getHtmlTemplate()
    {
        
    }
}