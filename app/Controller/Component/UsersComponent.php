<?php
App::import('Model', 'Raduser');

class UsersComponent extends Component {

    public function __construct($collection, $params) {
    }

    public function extendUsers($str) {
        $strUsers = explode(',', $str);
        $users = array();

        foreach($strUsers AS $strUser) {
            $raduser = new Raduser();
            $user = $raduser->findByUsername($strUser);

            if(empty($user)) {
                array_push($users, array(
                    'id' => -1,
                    'username' => $strUser,
                    'comment' => '',
                    'is_cisco' => 0,
                    'is_loginpass' => 0,
                    'is_phone' => 0,
                    'is_cert' => 0,
                    'is_mac' => 0,
                ));
            } else {
                array_push($users, array(
                    'id' => $user['Raduser']['id'],
                    'username' => $strUser,
                    'comment' => $user['Raduser']['comment'],
                    'is_cisco' => $user['Raduser']['is_cisco'],
                    'is_loginpass' => $user['Raduser']['is_loginpass'],
                    'is_phone' => $user['Raduser']['is_phone'],
                    'is_cert' => $user['Raduser']['is_cert'],
                    'is_mac' => $user['Raduser']['is_mac'],
                ));
            }
        }

        return $users;
    }
}

?>
