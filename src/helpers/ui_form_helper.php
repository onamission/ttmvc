<?php
/**
 * buildDropdownOptions
 * The purpose of this method is to create the dropdown options for an HTML
 *   SELECT element based on a data model
 *
 * @param string $model: The name of the model
 * @param string $valueField: The name of the field in each row to be used
 *                  as the return value for each option (usually the ID)
 * @param string $nameField: The name of the field in each row that will be
 *                  the display value
 * @return string
 */
function buildDropdownOptions($model, $valueField, $nameField) {
    $modelPath = APPROOT . "/models/$model.php";
    if (!file_exists($modelPath)) {
        return '';
    }
    require_once $modelPath;
    $this->m = new $model();

    // validate that the model contains both fields
    if (strpos($this->m->getAttribute('fields'), $valueField)===false || 
            strpos($this->m->getAttribute('fields'), $nameField) == false) {
        return '';
    }
    // build the options
    $options = '';
    $rows = $this->m->fetchAll();
    foreach ($rows as $row){
        $options .= "\n<option value={$row->$valueField}>{$row->$nameField}</option>";
    }
    return $options;
}