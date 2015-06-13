<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('AppController', 'Controller');

/**
 * Controller to handle tftp.
 */
class TftpController extends AppController {
	public $components = array(
        'Session',
    );
	public $tftppath="/home/snack/tftp";

	public function index() {
		$files = $this->list_files();
		$this->set('files', $files);
	}

	public function get_file($id) {
		$files = $this->list_files();
		debug($files[$id]['filename']);
		$this->response->file(
			$this->tftppath.'/'.$files[$id]['filename'],
			array('download' => true, 'name' => $files[$id]['filename'])
		);
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
			if (count($_FILES) > 0) {
	            if (move_uploaded_file($_FILES['data']['tmp_name']['importFile']['file'], $this->tftppath.'/'.$_FILES['data']['name']['importFile']['file'])) {
					$this->Session->setFlash(
                        "Success", 'flash_success'
                	);
				}
			} else {
				$this->Session->setFlash(
                        "Error", 'flash_error'
                );
			}
		}
		$this->redirect(array('action' => 'index'), null, true);
	}

	public function list_files() {
		$files_arr = array();
		$dir = new Folder($this->tftppath);
		$files = $dir->find('.*');
        sort($files);
        foreach($files as $filename) {
        	$file_arr = array();
        	$file_arr['filename'] = $filename;
        	$file = new File($this->tftppath.'/'.$filename, false, 0644);
        	$file_arr['md5'] = $file->md5(true);
        	$file_arr['size'] = $this->bytesToSize($file->size());
        	$file_arr['sha1'] = sha1_file($this->tftppath.'/'.$filename);
        	$files_arr[] = $file_arr;
        }
        //debug($files_arr);
        return $files_arr;
	}

	/**
	 * Convert bytes to human readable format
	 *
	 * @param integer bytes Size in bytes to convert
	 * @return string
	 */
	function bytesToSize($bytes, $precision = 2)
	{  
	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;
	   
	    if (($bytes >= 0) && ($bytes < $kilobyte)) {
	        return $bytes . ' B';
	 
	    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
	        return round($bytes / $kilobyte, $precision) . ' KB';
	 
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return round($bytes / $megabyte, $precision) . ' MB';
	 
	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return round($bytes / $gigabyte, $precision) . ' GB';
	 
	    } elseif ($bytes >= $terabyte) {
	        return round($bytes / $terabyte, $precision) . ' TB';
	    } else {
	        return $bytes . ' B';
	    }
}
}