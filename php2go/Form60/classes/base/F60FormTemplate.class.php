<?php
import('php2go.form.FormTemplate');

class F60FormTemplate extends FormTemplate
{
    function F60FormTemplate($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array()) {
            Form::Form($xmlFile, $formName, $Document);
            $this->formAction = HttpRequest::basePath();
            $this->formMethod = "POST";
            $this->Template = new Template($templateFile);
            if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {			
                    foreach ($tplIncludes as $blockName => $blockValue)
                            $this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
            }	
            
            if (PHP2Go::getConfigVal('TEMPLATE_CACHE', FALSE))
                $this->Template->setCacheProperties(PHP2Go::getConfigVal('FORM60_CACHE_PATH'));                
            $this->Template->parse();
    }
    
    function switchTemplate($fromTemplateName)
    {
        $this->templateName = TEMPLATE_PATH . $fromTemplateName;
        $this->Template = new Template($this->templateName);
        if (PHP2Go::getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties(PHP2Go::getConfigVal('FORM60_CACHE_PATH'));                
        $this->Template->parse();
    }
}
?>