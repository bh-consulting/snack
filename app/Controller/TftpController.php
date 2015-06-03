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
		$files = $this->list_files();
		$this->set('files', $files);
	}

	public function get_file($id) {
		$files = $this->list_files();
		$this->response->file($this->tftppath.'/'.$files[$id]);
        return $this->response;
	}

	public function delete_file($id) {
		$files = $this->list_files();
		$file = new File($this->tftppath.'/'.$files[$id], false, 0644);
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

	public function list_files() {
		$dir = new Folder($this->tftppath);
		$files = $dir->find('.*');
        sort($files);
        return $files;
	}
}