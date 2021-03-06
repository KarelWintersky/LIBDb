<?php
/**
 * User: Karel Wintersky
 * Date: 03.08.2018, time: 4:18
 */
define('__ACCESS_MODE__', 'admin:actions');

$SID = session_id();
if(empty($SID)) session_start();

require_once '__required.php'; // $mysqli_link

$action = $_GET['try:action'] ?? 'form';

unset($_GET);

// isLogged();

switch ($action) {
    case 'try:login': {
        $result = DBLoginCheck($_POST['login'], $_POST['md5password']);

        if ($result['error']==0)
        {
            $_SESSION[ Config::get('auth:session/user_id') ] = $result['id'];
            $_SESSION[ Config::get('auth:session/user_permissions')] = $result['permissions'];

            setcookie( Config::get('auth:cookies/user_is_logged'), $result['id'], 0, '/');
            setcookie( Config::get('auth:cookies/user_permissions'),  $result['permissions'], 0, '/');

            Redirect('/core/admin.php');
        } else {
            die(<<<ERRORMESSAGE
Error: {$result['message']} <br>
<a href="/core/admin.actions.php">Return to login form</a>
ERRORMESSAGE
            );
        }

        break;
    }

    case 'try:logout': {
        $key_cookie_is_logged = Config::get('auth:cookies/user_is_logged');
        $key_session_is_logged = Config::get('auth:session/user_is_logged');

        $key_cookie_u_permissions = Config::get('auth:cookies/user_permissions');
        $key_session_u_permissions = Config::get('auth:session/user_permissions');

        $key_cookie_u_id = Config::get('auth:cookies/user_id');
        $key_session_u_id = Config::get('auth:session/user_id');

        kwLogger::logEvent('login', 'userlist', $_SESSION[ $key_session_is_logged ] ?? 0, 'User logged out');

        setcookie( $key_cookie_is_logged, FALSE, -1, '/');
        unset($_COOKIE[ $key_cookie_is_logged ]);
        unset($_SESSION[ $key_session_is_logged ]); // instead of = -1;

        setcookie( $key_cookie_u_permissions, FALSE, -1, '/');
        unset($_COOKIE[ $key_cookie_u_permissions ]);
        unset($_SESSION[ $key_session_u_permissions ]); // instead of = -1;

        setcookie( $key_cookie_u_id, FALSE, -1, '/');
        unset($_COOKIE[ $key_cookie_u_id ]);
        unset($_SESSION[ $key_session_u_id ]);  // instead of = -1;

        Redirect('/core/');

        break;
    }
    case 'form':
    default: {
        $template_dir = '$/core/_templates';
        $template_file = "admin.form.login.html";
        $template_data = [
            'action'    =>  $action,
        ];
        echo websun_parse_template_path($template_data, $template_file, $template_dir);

        break;
    }
}



