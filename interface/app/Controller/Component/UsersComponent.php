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
                ));
            } else {
                array_push($users, array(
                    'id' => $user['Raduser']['id'],
                    'username' => $strUser,
                ));
            }
        }

        return $users;
    }
}

?>
