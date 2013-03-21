<?php
class UserGroupException extends CakeException {
    public function __construct($msg, $params=array()) {
        parent::__construct(vsprintf($msg, $params));
    }
}

class DeleteException extends UserGroupException {
    public function __construct($className, $id, $name) {
        parent::__construct(
            __('Unable to delete %s %s.'),
                array(substr($className, 3), $name)
            ); 
    }
}

class ExportException extends UserGroupException {
    public function __construct($className, $id, $name) {
        parent::__construct(
            __('Unable to export %s %s.'),
                array(substr($className,3), $name)
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
            __('Unable to edit %s %s.'),
                array(substr($className,3), $name)
            ); 
    }
}

class RSAKeyException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s: RSA key generation failed.'),
                array($name)
            ); 
    }
}

class CertificateException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s: certificate generation failed.'),
                array($name)
            ); 
    }
}

class CertificateSignException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s: certificate authentification failed.'),
                array($name)
            ); 
    }
}

class CRLException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s: revocation list update failed.'),
                array($name)
            ); 
    }
}

class RevokeException extends UserGroupException {
    public function __construct($id, $name) {
        parent::__construct(
            __('User %s: certificate revocation failed.'),
                array($name)
            ); 
    }
}

class CertificateRemoveException extends UserGroupException {
    public function __construct($id, $name) {
        $cert = Utils::getUserCertsPath($name);

        parent::__construct(
            __("User %s: cannot remove %s."),
                array($name, $cert)
            ); 
    }
}

class CertificateNotFoundException extends UserGroupException {
    public function __construct($id, $name) {
        $cert = Utils::getUserCertsPath($name);

        parent::__construct(
            __("User %s: cannot find %s."),
                array($name, $cert)
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
            _('%s %s: unable to add check "%s".'),
            array(ucfirst(substr($className,3)), $name, $check)
        ); 
    }
}

class CheckRemoveException extends UserGroupException {
    public function __construct($className, $id, $name, $checkID) {
        parent::__construct(
            _('%s %s: unable to delete check.'),
            array(ucfirst(substr($className,3)), $name)
        ); 
    }
}

class ReplyAddException extends UserGroupException {
    public function __construct($className, $id, $name, $reply) {
        parent::__construct(
            _('%s %s: unable to add reply "%s".'),
            array(ucfirst(substr($className,3)), $name, $reply)
        ); 
    }
}

class ReplyRemoveException extends UserGroupException {
    public function __construct($className, $id, $name, $replyID) {
        parent::__construct(
            _('%s %s: unable to delete reply.'),
            array(ucfirst(substr($className,3)), $name)
        ); 
    }
}

class BadBackupOrNasID extends CakeException {
    public function __construct($msg, $params=array()) {
        parent::__construct(vsprintf($msg, $params));
    }
}

?>
