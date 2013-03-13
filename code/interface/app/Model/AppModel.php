<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');
App::uses('Lib', 'Validation');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    public $findMethods = array('customList' =>  true);

    public function _findCustomList($state, $query, $results = array()) {
        if ($state == 'before') {
            return $query;
        } else {
            $newResults = array();

            foreach ($results as $result) {
                $records = array_shift($result);
                foreach ($records as $key=>$value) {
                    if ($value != null && $value != '' && $value != false) {
                        $newResults[] = $value;
                    }
                }
            }

            $newResults = array_unique($newResults);

            return $newResults;
        }
    }
}
