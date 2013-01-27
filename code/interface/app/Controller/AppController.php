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
    public function beforeFilter() {
        if (!$this->Session->check('Config.language')) {
            $lang = Utils::getISOCode($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $this->Session->write('Config.language', $lang);
        }

        Configure::write(
            'Config.language',
            $this->Session->read('Config.language')
        );
    }

    public function changeLang($lang) {
        $this->autoRender = false;
        $this->Session->write('Config.language', $lang);
        Configure::write('Config.language', $lang);
        $this->redirect($this->referer());
    }
    
    /*
    public $components = array(
        'Session',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'radusers',
                'action' => 'login'
            ),
            'loginRedirect' => array('controller' => 'radusers', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'username', 'password' => 'value'),
                    'userModel' => 'Raduser'
                )
            )
        )
    );
     */
}
