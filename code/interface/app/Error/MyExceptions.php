<?php
class UserGroupException extends CakeException {
    public function __construct($msg, $params=array()) {
        parent::__construct(vsprintf($msg, $params));
    }
}

class DeleteException extends UserGroupException {
    public function __construct($className, $id, $name) {
        parent::__construct(
            __('Unable to delete %s %s (#%d).'),
                array(substr($className, 3), $name, $id)
            ); 
    }
}

class ExportException extends UserGroupException {
    public function __construct($className, $id, $name) {
        parent::__construct(
            __('Unable to export %s %s (#%d).'),
                array(substr($className,3), $name, $id)
            ); 
    }
}

class AddException extends UserGroupException {
    public function __construct($className, $name) {
        parent::__construct(
            __('Unable to create %s "%s".'),
            array(substr($className,3), $name)
        ); 
    }
}

class EditException extends UserGroupException {
    public function __construct($className, $id, $name) {
        parent::__construct(
            __('Unable to edit %s %s (#%d).'),
                array(substr($className,3), $name, $id)
            ); 
    }
}

class RSAKeyException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s (#%d) : RSA key generation failed.'),
                array($name, $id)
            ); 
    }
}

class CertificateException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s (#%d) : Certificate generation failed.'),
                array($name, $id)
            ); 
    }
}

class CertificateSignException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s (#%d) : Certificate authentification failed.'),
                array($name, $id)
            ); 
    }
}

class CRLException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s (#%d) : Revocation list update failed.'),
                array($name, $id)
            ); 
    }
}

class RevokeException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s (#%d) : Certificate revocation failed.'),
                array($name, $id)
            ); 
    }
}

class CertificateRemoveException extends UserGroupException {
    public function __construct($id, $name) {
        $certs = Utils::getUserCertsPath($name);

        parent::__construct(
            __("User %s (#%d) : Cannot remove %s or %s."),
                array($name, $id, $certs['public'], $certs['key'])
            ); 
    }
}

class CertificateNotFoundException extends UserGroupException {
    public function __construct($id, $name) {
        $certs = Utils::getUserCertsPath($name);

        parent::__construct(
            __("User %s (#%d) : Cannot find %s or %s."),
                array($name, $id, $certs['public'], $certs['key'])
            ); 
    }
}

class UserGroupDeleteException extends UserGroupException {
    public function __construct($username, $groupname) {
        parent::__construct(
            __('Unable to unregister %s from group %s.'),
            array($username, $groupname)
        ); 
    }
}

class UserGroupCleanGroupException extends UserGroupException {
    public function __construct($username) {
        parent::__construct(
            __('Unable to unregister %s from all his groups.'),
            array($username)
        ); 
    }
}

class UserGroupCleanUserException extends UserGroupException {
    public function __construct($groupname) {
        parent::__construct(
            __('Unable to unregister all users from group %s.'),
            array($groupname)
        ); 
    }
}

class UserGroupAddException extends UserGroupException {
    public function __construct($username, $groupname) {
        parent::__construct(
            __('Unable to register %s in group %s.'),
            array($username, $groupname)
        ); 
    }
}

class CheckAddException extends UserGroupException {
    public function __construct($className, $id, $name, $check) {
        parent::__construct(
            _('%s %s (#%d) : Unable to add check "%s".'),
            array(ucfirst(substr($className,3)), $name, $id, $check)
        ); 
    }
}

class CheckRemoveException extends UserGroupException {
    public function __construct($className, $id, $name, $checkID) {
        parent::__construct(
            _('%s %s (#%d) : Unable to delete check #%s.'),
            array(ucfirst(substr($className,3)), $name, $id, $checkID)
        ); 
    }
}

class ReplyAddException extends UserGroupException {
    public function __construct($className, $id, $name, $reply) {
        parent::__construct(
            _('%s %s (#%d) : Unable to add reply "%s".'),
            array(ucfirst(substr($className,3)), $name, $id, $reply)
        ); 
    }
}

class ReplyRemoveException extends UserGroupException {
    public function __construct($className, $id, $name, $replyID) {
        parent::__construct(
            _('%s %s (#%d) : Unable to delete reply #%s.'),
            array(ucfirst(substr($className,3)), $name, $id, $replyID)
        ); 
    }
}

class BadBackupOrNasID extends CakeException {
    public function __construct($msg, $params=array()) {
        parent::__construct(vsprintf($msg, $params));
    }
}

?>
