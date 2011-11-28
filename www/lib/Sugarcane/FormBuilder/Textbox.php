<?php

class Sugarcane_FormBuilder_Textbox
{
    protected $field;
    protected $value;
    
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }
    
    public function render()
    {
        // Manipulate the form fields / data here before sending the field to be rendered
        return $this->renderField();
    }
    
    
    protected function renderField()
    {
        // Setup the template for the field
        $template = '<li>
                         <label for="%s" id="%1$s_label" class="desc">%s</label>
                         <input type="text" name="%1$s" id="%1$s" value="%s" /> %s
                     </li>';
        
        // Is the field required? If so append a *
        $required = ($this->field['required'] == 'Y') ? '*' : '';
        
        return sprintf($template, $this->field['fieldname'], $this->field['label'], $this->value, $required);
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