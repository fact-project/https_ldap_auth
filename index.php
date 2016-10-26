<?php
require_once 'config.php';
$ldap = @ldap_connect($ldaphost);

if (!$ldap) {
    header('HTTP/1.1 500 Internal Server Error');
}

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == '' || $password == '' ) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'Empty user/password';
    exit;
}

$attributes = array('cn', 'mail');
$dn         = 'ou=People,'.$baseDN;
$filter     = '(uid='.$username.')';

$search_result = @ldap_search($ldap, $dn, $filter, $attributes);
if (!$search_result) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$ldap_entries = @ldap_get_entries($ldap, $search_result);
if ($ldap_entries["count"] == 0){
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$userDN = $ldap_entries[0]['dn'];

$login_successful = @ldap_bind($ldap, $userDN, $password);

if ($login_successful){
    header('HTTP/1.1 200');
    echo 'Login successful';
    exit;
} else {
    header('HTTP/1.1 401 Unauthorized');
    echo 'Wrong user/password';
    exit;
}

?>
