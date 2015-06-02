<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('AppController', 'Controller');

/**
 * Controller to handle tftp.
 */
class TftpController extends AppController {

	public $tftppath="/home/snack/tftp";

	public function index() {
		$dir = new Folder('/home/snack/tftp');
		$files = $dir->find('.*');
        sort($files);
        $files=array_reverse($files);
		$this->set('files', $files);
	}

	public function get_file($filename) {
		$this->response->file($this->tftppath.'/'.$filename);
        return $this->response;
	}

	public function delete_file($filename) {
		$file = new File($this->tftppath.'/'.$filename, false, 0644);
		$file->delete();
		return $this->redirect(array('action' => 'index'));
	}

	public function upload_file() {
		if ($this->request->isPost()) {
			//debug($_FILES);
            if (move_uploaded_file($_FILES['data']['tmp_name']['importFile']['file'], $this->tftppath.'/'.$_FILES['data']['name']['importFile']['file'])) {
			}
		}
		return $this->redirect(array('action' => 'index'));
	}
}