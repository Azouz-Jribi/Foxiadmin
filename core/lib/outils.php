<?php
/**
 * @brief generate xml fil config
 * @author jribi.abdelaziz
 * @mail jribi.azouz@gmail.com
 * @date 26/12/2016
 */

/**
 * @brief Redirect to error message
 * @param $code
 */
function redirectMessage($code){
    echo "<script language='JavaScript'>window.location.href = 'dash.php?t=".$code."&a=error';</script>";
}

function redirectToUrl($url){
    echo "<script language='JavaScript'>window.location.href = '".$url."';</script>";
}

/**
 * @brief Create file
 * @param $path
 * @param $data
 */
function creatAllFile($path, $data){
    $fp = fopen($path, 'w');
    fwrite($fp, $data);
    fclose($fp);
}

/**
 * @brief Determine placeholder inputs
 * @return array
 */
function getInputFormat(){
    return array(
        'default' => 'Enter text',
        'number' => 'Enter numeric', /** valid only for all types of int */
        'url' => 'http://www.example.com',
        'email' => 'example@domain.com',
        'date' => '01/01/2017',
        'time' => '00:00',
        'datetime' => '01/01/2017 00:00:00', /** is not applicated */
        'password' => 'Enter password',
    );
}

/**
 * @brief  Find the position of the first occurrence of a substring in a string
 * @param $key
 * @param $string
 * @return bool|int
 */
function strIsExist($key, $string){
    return strpos(trim('¤'.$string), $key);
}

/**
 * @brief Check field requirement
 * @param $required
 * @return string
 */
function requiedFieldForm($required){
    if(!empty($required))
        return "*";
    return "";
}

/**
 * @brief get all types of message
 * @param $code
 * @return string
 */
function getMessage($code){
    $errorStyle = array(
        "warning" => "<div class='alert alert-warning' id='ermsg' ><strong>Warning!</strong> ",
        "error" => "<div class='alert alert-danger' id='ermsg' ><strong>Oh snap!</strong> ",
        "info" => "<div class='alert alert-info' id='ermsg' ><strong>Heads up!</strong> ",
        "success" => "<div class='alert alert-success' id='ermsg' ><strong>Well done!</strong> "
    );
    $doc = simplexml_load_file(HOME_DIR."/config/error.xml");
    $error = $doc->xpath("//error[@id='".$code."']")[0];
    return $errorStyle[$error['type']->__toString()].$error."</div><script>setTimeout(function(){ $('#ermsg').fadeOut('fast'); }, 2000);</script>";
}

/**
 * @brief Check permission on entity actions
 * @param $action
 * @param $table
 * @return bool
 */
function notHaveAccess($action, $table){
    $doc = simplexml_load_file(HOME_DIR."/config/menu.xml");
    $item = $doc->xpath("//item[@name='".$table."']")[0];
    $actions = explode(',', $item['actions']->__toString());
    if(in_array($action, $actions)){
        return false;
    }

    return true;
}

/**
 * @brief Get all numeric types of database
 * @return array
 */
function getAllNumericTypes(){
    return array('TINYINT','SMALLINT','MEDIUMINT','INT','BIGINT','FLOAT','DOUBLE','DECIMAL');
}

/**
 * @brief Get entity identifier info
 * @param $table
 * @return array
 */
function getIdColumn($table){
    $doc = simplexml_load_file(HOME_DIR."/config/entities.xml");
    $column = $doc->xpath("//table[@name='".$table."']/column[@id='yes']")[0];
    return array(
        'name' => $column['name']->__toString(),
        'type' => $column->type->__toString()
    );
}

/**
 * @brief Determine the value according to their type
 * @param $type
 * @param $value
 * @return string
 */
function getValueByType($type, $value){
    if(in_array(strtoupper($type),getAllNumericTypes())){
        return $value;
    }
    return "'".$value."'";
}

/**
 * @brief Get request parameters
 * @param $request
 * @return array
 */
function getRequestParams($request){
    $params = array();
    $params['table'] =$request['table'];
    unset($request['table']);
    $params['action'] = $request['action'];
    unset($request['action']);
    foreach($request as $k => $v){
        $params['data'][$k] = $v;
    }

    return $params;
}