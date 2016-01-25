<?php

class BackupsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Backup');
    public $components = array(
        'Filters',
        'Users' => array()
    );

    private $git = '~snack/backups.git/';

    /*public function getRegexSynchronisation($args = array()) {
        if (!empty($args['input']) && !empty($args['nas'])) {
            $data = &$this->request->data['Backup'][$args['input']];
            $regex = '(1 = 1';
            $ids = array();
            $flag = false;

            foreach ((array)$data as $choice) {
                switch ($choice) {
                case 'changed':
                    $ids = array_merge(
                        $ids,
                        (array)$this->getUnwrittenBackups($args['nas'])
                    );
                    $flag = true;
                    break;
                case 'notchanged':
                    $ids = array_merge(
                        $ids,
                        (array)$this->getUnwrittenBackups($args['nas'], true)
                    );
                    $flag = true;
                    break;
                }
            }

            if (!empty($ids)) {
                $regex .= ' AND id IN ('
                    . implode($ids, ',')
                    . '))';
            } else if ($flag) {
                $regex = '(1=0)'; 
            } else {
                $regex .= ')';
            }

            if (!empty($regex) && $regex != '(1 = 1)') {
                return $regex;
            }
        }
    }*/

    public function index($id = null) {
        $this->loadModel('Nas');
        $nas = $this->Nas->findById($id);

        $this->set('nasID', $nas['Nas']['id']);
        $this->set('nasIP', $nas['Nas']['nasname']);
        $this->set('nasShortname', $nas['Nas']['shortname']);

        $this->Filters->addDatesConstraint(array(
            'field' => 'datetime', 
            'from' => 'datefrom',
            'to' => 'dateto',
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'users', 
            'input' => 'author', 
            'ahead' => array('users'),
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'nas', 
            'input' => 'nas', 
            'value'  => $nas['Nas']['nasname'],
            'strict' => true,
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('action'),
            'items' => $this->Backup->actions,
            'input' => 'action',
            'title' => false,
        ));

        /*$this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'notchanged' => '<i class="icon-ok-sign icon-green"></i> '
                    . __('Synchronized'),
                        'changed' => ' <i class="icon-exclamation-sign icon-red"></i> '
                        . __('Not synchronized'),
                        ),
                        'input' => 'writemem',
                        'title' => false,
                    ),
                    'callback' => array(
                        'getRegexSynchronisation',
                        array(
                            'input' => 'writemem',
                            'nas' => $nas,
                        ),
                    ),
                ));*/
        
        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'active' => __(' '),
                ),
                'input' => 'lastchange',
                'title' => false,
            ),
            /*'callback' => array(
                'getBackupsLastChange',
                array(
                    'input' => 'lastchange',
                ),
            )*/
        ));
        
        $this->Filters->addGroupConstraint('commit');

        $backups = $this->Filters->paginate();

        $users = array();

        foreach($backups as &$backup) {
            $users[$backup['Backup']['id']] =
                $this->Users->extendUsers($backup['Backup']['users']);

            if (isset($backup['Backup']['datetime'])) {
                $backup['Backup']['datetime'] = Utils::formatDate(
                    $backup['Backup']['datetime'],
                    'display'
                );
            }
        }

        $this->set('users', $users);
        //$this->set('unwrittenids', $this->getUnwrittenBackups($nas));
        $this->set('backups', $backups);
    }
    
    public function listbackups($id = null) {
        $this->loadModel('Nas');
        $nas = $this->Nas->findById($id);

        $this->set('nasID', $nas['Nas']['id']);
        $this->set('nasIP', $nas['Nas']['nasname']);
        $this->set('nasShortname', $nas['Nas']['shortname']);

        $this->Filters->addDatesConstraint(array(
            'field' => 'datetime', 
            'from' => 'datefrom',
            'to' => 'dateto',
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'users', 
            'input' => 'author', 
            'ahead' => array('users'),
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'nas', 
            'input' => 'nas', 
            'value'  => $nas['Nas']['nasname'],
            'strict' => true,
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('action'),
            'items' => $this->Backup->actions,
            'input' => 'action',
            'title' => false,
        ));

        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'notchanged' => '<i class="icon-ok-sign icon-green"></i> '
                    . __('Synchronized'),
                        'changed' => ' <i class="icon-exclamation-sign icon-red"></i> '
                        . __('Not synchronized'),
                        ),
                        'input' => 'writemem',
                        'title' => false,
                    ),
                    /*'callback' => array(
                        'getRegexSynchronisation',
                        array(
                            'input' => 'writemem',
                            'nas' => $nas,
                        ),
                    )*/
                ));

        //$this->Filters->addGroupConstraint('commit');

        $backups = $this->Filters->paginate();

        $users = array();

        foreach($backups as &$backup) {
            $users[$backup['Backup']['id']] =
                $this->Users->extendUsers($backup['Backup']['users']);

            if (isset($backup['Backup']['datetime'])) {
                $backup['Backup']['datetime'] = Utils::formatDate(
                    $backup['Backup']['datetime'],
                    'display'
                );
            }
        }

        $this->set('users', $users);
        //$this->set('unwrittenids', $this->getUnwrittenBackups($nas));
        $this->set('backups', $backups);
    }

    /**
     * Return differences between two nas backups.
     * If the second id is not specified,
     * differences are compute with the last commit.
     */
    private function gitDiffNas($nasId, $firstId, $secondId = null) {
        $first = $this->Backup->findById($firstId);

        if (!$first) {
            throw new BadBackupOrNasID(
                'Please select an existant version A for this NAS.'
            );
        }

        $first['Backup']['users'] = $this->Users->extendUsers($first['Backup']['users']);

        $this->set('first', $first);

        if ($secondId != null) {
            $second = $this->Backup->findById($secondId);

            if (!$second) {
                throw new BadBackupOrNasID(
                    'Please select an existant version B for this NAS.'
                );
            }

            $second['Backup']['users'] = $this->Users->extendUsers($second['Backup']['users']);

            $this->set('second', $second);
        }

        exec("cd $this->git; git diff $commitB $commitA", $output);
        $this->set('diff', implode("\n", $output));

        return $backupA;
    }

    public function diff() {
        try {
            if(!isset($this->params['url']['nas'])
                || !isset($this->params['url']['a'])
                || !isset($this->params['url']['b'])
            ) {

                throw new BadBackupOrNasID(
                    'Please select specific versions for a specific NAS.'
                );
            }

            $nas = new Nas($this->params['url']['nas']);
            $nas = $nas->read();

            if (!$nas) {
                throw new BadBackupOrNasID(
                    __('Please select an existant NAS.')
                );
            }

            $data = $this->getDifferences(
                $this->params['url']['a'],
                $this->params['url']['b'],
                $nas['Nas']['nasname']
            );

            $this->set('nas', $nas);
            $this->set('left', $data['left']['info']);
            $this->set('right', $data['right']['info']);
            $this->set('rawDiff', implode("\n", $data['diff']['raw']));
            $this->set('graphicalDiff', $data['diff']['graphical']);
        } catch(BadBackupOrNasID $e) {
            $this->Session->setFlash(
                $e->getMessage(),
                'flash_error'
            );
        }
    }

    public function renderDiff($left, $right, $diff) {
        if (is_array($left) && is_array($right) && is_array($diff)) {
            $begin = false;
            $action = false;
            $currentL = $currentR = 0;
            $offsetR = $offsetL = 0;
            foreach ($diff as $line) {
                switch (substr($line,0,1)) {
                case '@':
                    $begin = true;
                    $action = false;

                    if (preg_match('#@\s-(?<lineS>[0-9]+)\s\+(?<lineD>[0-9]+)\s@#', $line, $info)) {
                        $currentL = intval($info['lineS']);
                        $currentR = intval($info['lineD']);
                        $action = 'update';
                    } else if (preg_match('#@\s-(?<lineS>[0-9]+)(,[0-9]+)?\s\+(?<lineD>[0-9]+,0)\s@#', $line, $info)) {
                        $currentL = intval($info['lineS']);
                        $currentR = intval($info['lineD']);
                        $action = 'delete';
                    } else if (preg_match('#@\s-(?<lineS>[0-9]+,0)\s\+(?<lineD>[0-9]+)(,[0-9]+)?\s@#', $line, $info)) {
                        $currentL = intval($info['lineS']);
                        $currentR = intval($info['lineD']);
                        $action = 'add';
                    } else if (preg_match('#-(?<lineS>[0-9]+)(,(?<lenS>[0-9]+))?\s\+(?<lineD>[0-9]+)(,(?<lenD>[0-9]+))?#', $line, $info)) {
                        $currentL = intval($info['lineS']);
                        $currentR = intval($info['lineD']);

                        if (isset($info['lenS'])
                            && isset($info['lenD'])
                            && $info['lenS'] == $info['lenD']
                        ) {
                            $action = 'update';
                        } else {
                            $action = 'mix';
                        }
                    } else {
                        $begin = false;
                    }
                    break;
                case '-':
                    if ($begin && $action) {
                        $line = substr($line, 1);

                        // Update left
                        switch ($action) {
                        case 'update':
                            $left[$currentL + $offsetL - 1] = array('UP' => $line);
                            break;
                        case 'delete':
                        case 'mix':
                            $left[$currentL + $offsetL - 1] = array('DEL' => $line);
                            break;
                        }
                        ++$currentL;

                        // Update right
                        switch ($action) {
                        case 'delete':
                            $right = array_merge(
                                array_slice($right, 0, $currentR + $offsetR),
                                array(array('DEL' => '')),
                                array_slice($right, $currentR + $offsetR)
                            );
                            ++$offsetR;
                            break;
                        case 'mix':
                            $right = array_merge(
                                array_slice($right, 0, $currentR + $offsetR - 1),
                                array(array('DEL' => '')),
                                array_slice($right, $currentR + $offsetR - 1)
                            );
                            ++$offsetR;
                            break;
                        }
                    }
                    break;
                case '+':
                    if ($begin) {
                        $line = substr($line, 1);

                        // Update right
                        switch ($action) {
                        case 'update':
                            $right[$currentR + $offsetR - 1] = array(
                                'UP' => $line);
                            break;
                        case 'add':
                        case 'mix':
                            $right[$currentR + $offsetR - 1] = array('ADD' => $line);
                            break;
                        }
                        ++$currentR;

                        // Update left
                        switch ($action) {
                        case 'add':
                            $left = array_merge(
                                array_slice($left, 0, $currentL + $offsetL),
                                array(array('ADD' => '')),
                                array_slice($left, $currentL + $offsetL)
                            );
                            ++$offsetL;
                            break;
                        case 'mix':
                            $left = array_merge(
                                array_slice($left, 0, $currentL + $offsetL - 1),
                                array(array('ADD' => '')),
                                array_slice($left, $currentL + $offsetL - 1)
                            );
                            ++$offsetL;
                            break;
                        }
                    }
                    break;
                }
            }
        } else {
            $left = array($left);
            $right = array($right);
        }

        return array('left' => $left, 'right' => $right);
    }

    private function getBackup($idBackup, $nasname) {
        // Backup.
        $backup = $this->Backup->read(null, $idBackup);

        if (!$backup) {
            throw new BadBackupOrNasID(
                __('Please select an existant backup.')
            );
        }

        $backup['Backup']['users'] = 
            $this->Users->extendUsers($backup['Backup']['users']);
        $backup['Backup']['datetime'] = Utils::formatDate(
            $backup['Backup']['datetime'],
            'display'
        );
        if (isset($this->Backup->actions[$backup['Backup']['action']])) {
            $backup['Backup']['action'] = 
                $this->Backup->actions[$backup['Backup']['action']];
        }

        $content = Utils::shell(
            "cd $this->git; "
            . "git show {$backup['Backup']['commit']}:{$nasname}"
        );

        if ($content['code']) {
            $content = __(
                'Error while retrieving file content: %s.',
                "{$backup['Backup']['commit']}:{$nasname}"
            );
        } else {
            $content = $content['msg'];
        }

        return array(
            'info' => $backup,
            'nasname' => $nasname,
            'file' => $content,
        );
    }

    private function getDifferences($idLeft, $idRight, $nasname) {
        // Backup left.
        $left = $this->getBackup($idLeft, $nasname);

        // Backup right.
        $right = $this->getBackup($idRight, $nasname);

        // Differences.
        $diff = Utils::shell(
            "cd $this->git;"
            . "git diff -U0 {$left['info']['Backup']['commit']} "
            . "{$right['info']['Backup']['commit']} $nasname"
        );

        if ($diff['code']) {
            $diff = __(
                'Error while comparing commit %s and %s.',
                $left['info']['Backup']['commit'],
                $right['info']['Backup']['commit']
            );
        } else {
            $diff = $diff['msg'];
        }

        $diffExtend = $this->renderDiff($left['file'], $right['file'], $diff);

        return array(
            'left' => $left,
            'right' => $right,
            'diff' => array('raw' => $diff, 'graphical' => $diffExtend),
        );
    }

    /**
     * Show details about a backup:
     * - Information like nas, when, who, why
     * - Current configuration differences
     * - File content
     * - Other backups which look like
     */
    public function view($backupId = null, $nasId = null) {
        try {
            if($backupId != null && $nasId != null) {
                // Nas informations.
                $nas = new Nas($nasId);
                $nas = $nas->read();

                if (!$nas) {
                    throw new BadBackupOrNasID(
                        __('Please select an existant NAS.')
                    );
                }

                $this->set('nas', $nas);

                // Backup information.
                $backup = $this->Backup->read(null, $backupId);

                if (!$backup) {
                    throw new BadBackupOrNasID(
                        __('Please select an existant backup.')
                    );
                }

                $backup['Backup']['users'] = 
                    $this->Users->extendUsers($backup['Backup']['users']);
                $backup['Backup']['datetime'] = Utils::formatDate(
                    $backup['Backup']['datetime'],
                    'display'
                );
                if (isset($this->Backup->actions[$backup['Backup']['action']])) {
                    $backup['Backup']['action'] = 
                        $this->Backup->actions[$backup['Backup']['action']];
                }

                $this->set('current', $backup);

                // Current configuration differences + Backup information.
                $diff = Utils::shell(
                    "cd $this->git; git diff -U0 {$backup['Backup']['commit']}"
                );

                if ($diff['code']) {
                    $diff = __(
                        'Error while compare backup %s with last backup.',
                        "{$backup['Backup']['commit']}"
                    );
                } else {
                    $diff = $diff['msg'];
                }

                $this->set('diff', implode("\n", (array)$diff));

                // Backup content.
                $content = Utils::shell(
                    "cd $this->git; "
                    . "git show {$backup['Backup']['commit']}:{$nas['Nas']['nasname']}"
                );

                if ($content['code']) {
                    $content = __(
                        'Error while retrieving file content: %s.',
                        "{$backup['Backup']['commit']}:{$nas['Nas']['nasname']}"
                    );
                } else {
                    $content = $content['msg'];
                }

                $this->set('content', implode("\n", (array)$content));

                // Graphical differences
                $last = $this->Backup->find(
                    'first',
                    array(
                        'fields' => 'commit',
                        'conditions' => array('nas' => $nas['Nas']['nasname']),
                        'order' => 'datetime DESC',
                    )
                );

                $lastContent = Utils::shell(
                    "cd $this->git; "
                    . "git show {$last['Backup']['commit']}:{$nas['Nas']['nasname']}"
                );

                if ($lastContent['code']) {
                    $lastContent = __(
                        'Error while retrieving file content: %s.',
                        "{$last['Backup']['commit']}:{$nas['Nas']['nasname']}"
                    );
                } else {
                    $lastContent = $lastContent['msg'];
                }

                $diffExtend = $this->renderDiff($content, $lastContent, $diff);

                $this->set('diffExtend', $diffExtend);

                // Similar backups.
                // Users information.

                $this->Filters->addStringConstraint(array(
                    'fields' => 'commit', 
                    'input' => 'commit', 
                    'value'  => $backup['Backup']['commit'],
                    'strict'  => true,
                ));

                $this->Filters->addStringConstraint(array(
                    'fields' => 'nas', 
                    'input' => 'nas', 
                    'value'  => $nas['Nas']['nasname'],
                    'strict'  => true,
                ));

                $similar = $this->Filters->paginate('similar');

                $users = array();

                foreach($similar as &$backup) {
                    $users[$backup['Backup']['id']] =
                        $this->Users->extendUsers($backup['Backup']['users']);
                    $backup['Backup']['datetime'] = Utils::formatDate(
                        $backup['Backup']['datetime'],
                        'display'
                    );
                    if (isset($this->Backup->actions[$backup['Backup']['action']])) {
                        $backup['Backup']['action'] = 
                            $this->Backup->actions[$backup['Backup']['action']];
                    }
                }

                $this->set('users', $users);
                $this->set('similar', $similar);
            } else {
                throw new BadBackupOrNasID(
                    'Please select a NAS and a configuration version.'
                );
            }

        } catch(BadBackupOrNasID $e) {
            $this->Session->setFlash(
                $e->getMessage(),
                'flash_error'
            );
        }
    }

    /*public function restore($nasId, $backupId) {
        $nas = new Nas($nasId);
        $nasname = $nas->field('nasname');

        $backup = $this->Backup->read(null, $backupId);

        $restore = Utils::shell("~snack/scripts/restore {$backup['Backup']['commit']} $nasname");

        if ($restore['code'] == 0) {
            $this->Session->setFlash(__(
                    "Commit %s restored on NAS %s.%sDon't forget to reload the NAS.",
                    $backup['Backup']['commit'],
                    $nasname,
                    '</br>'
                ),
                'flash_warning'
            );

            $this->redirect(
                array(
                    'controller' => 'backups',
                    'action' => 'index',
                    $nasId,
                )
            );
        } else {
            $this->Session->setFlash(__(
                    'Unable to restore commit %s on NAS %s.',
                    $backup['Backup']['commit'],
                    $nasname
                ),
                'flash_error'
            );

            $this->redirect(
                array(
                    'controller' => 'backups',
                    'action' => 'view',
                    $backupId,
                    $nasId,
                )
            );
        }
    }
    
    public function getBackupsLastChange($args = array()) {
        if (!empty($args['input'])) {
	    $data = &$this->request->data['Backup'][$args['input']];
            if (isset($data[0]) && $data[0] == 'active') {
                return "(id IN (select id from backups group by(commit)))";
            } else {
                return '(1=1)';
            }
        }
    }*/
}

?>
