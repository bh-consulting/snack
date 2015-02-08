<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('Utils', 'Lib');
App::uses('phpGraph', 'Lib');
App::import('Model', 'Nas');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    public $components = array(
        'Session',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'radusers',
                'action' => 'login',
            ),
            'loginRedirect' => array(
                'controller' => 'radusers',
                'action' => 'index',
            ),
            'logoutRedirect' => array(
                'controller' => 'radusers',
                'action' => 'index',
            ),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array(
                        'username' => 'username',
                        'password' => 'passwd',
                    ),
                    'userModel' => 'Raduser'
                )
            ),
            'authorize' => array('Controller'),
        ),
    );

    public $disableBackupsCheck = array(
        'Radusers' => 'login',
    );

    /**
     * Executed before every action.
     */
    public function beforeFilter() {
        // Initialize the default langage if not set,
        // regarding the browser langage.
        if (!$this->Session->check('Config.language')) {
            $lang = Utils::getISOCode($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $this->Session->write('Config.language', $lang);
        }

        // Set views langage.
        Configure::write(
            'Config.language',
            $this->Session->read('Config.language')
        );

        // Set pagination count configuration from parameters file.
        if (isset($this->paginate) && isset($this->paginate['limit'])) {
            $this->paginate['limit'] = Configure::read(
                'Parameters.paginationCount'
            );
        }

        // Set common class for sort icons, used in all indexes to display
        // sorted tables of elements.
        $this->set('sortIcons', array(
            'asc' => 'glyphicon glyphicon-chevron-down',
            'desc' => 'glyphicon glyphicon-chevron-up',
        ));
    }

    /**
     * Change the current display langage.
     */
    public function changeLang($lang) {
        $this->autoRender = false;
        $this->Session->write('Config.language', $lang);
        Configure::write('Config.language', $lang);
        $this->redirect($this->referer());
    }

    /**
     * Authorize all actions for super admin and stop other users.
     */
    public function isAuthorized($user) {
        // Root can access everything.
        if (isset($user['role']) && $user['role'] === 'root') {
            return true;
        }

        // Default deny
        $this->Session->setFlash(
            __('You are not authorized to access this page!'),
            'flash_error'
        );
        return false;
    }
}
