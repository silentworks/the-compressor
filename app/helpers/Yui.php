<?php defined('SYSPATH') or die('No direct script access.');
/**
 * YUI Compressor helper file
 *
 * @package default
 * @author Andrew Smith
 */
class Yui
{
	var $tmp_dir = 'tmp'; // tmp dir
	var $upload_dir = 'upload'; // Upload dir
	var $compressor_dir; // Upload dir
	var $fp = array();
	var $fileList;
	var $ext;
	
	// YUI Parameters
	var $params = array('nomunge' => 0,
						'preserve-semi' => 0,
						'disable-all-opt' => 0 );

	// Upload file(s)
	public function upload($data)
	{
		$newData = $data['upload'];
		if($newData){
			// Check how many files are being uploaded
			foreach($newData['error'] as $key => $error)
			{
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $newData['tmp_name'][$key];
					$name = $newData['name'][$key];

					move_uploaded_file($tmp_name, "$this->tmp_dir/$name");

					if(file_exists($this->tmp_dir . DS . $name))
					{
						$fileInfo = pathinfo($name);
						$this->fileList[] = $fileInfo[basename];
						$this->copy($name);
					}
				}
			}
		}
		return true;
	}

	// Copy content
	public function copy($data)
	{
		$PathArray = explode('/', $_SERVER['SCRIPT_FILENAME']);
		$fileInfo = pathinfo($data);
		$this->ext = '.' . $fileInfo[extension];

		array_pop($PathArray);
		$dir = implode(MYDS, $PathArray) . MYDS;
		
		$input = $dir . $this->tmp_dir . MYDS . $data;
		$output = $dir . $this->tmp_dir . MYDS . uniqid($fileInfo[filename]) . $this->ext;
		
		// $this->debug($this->check($this->params)); die;

		$cmd = "java -Xmx32m -jar " . $dir . $this->compressor_dir . "yuicompressor-2.4.2.jar --charset UTF-8". $this->check($this->params) ." -o " . $output . " " . $input . " 2>&1";
		exec($cmd, $out, $err); // Run Compressor
		unlink($input); // Delete Input File

		if ($err === 0) {
			$this->fp['content'] .= file_get_contents($output);
			$this->fp['content'] .= "\n\n";

			unlink($output);
		} else if ($err === SYNTAX_ERROR) {
			$this->error = "The YUI Compressor reported the following error(s):\n\n" . implode("\n", $out);
		} else {
			$this->error = "An unexpected error occurred:\n\n" . implode("\n", $out);
		}
	}

	// Write file
	public function write($data, $name_set = NULL)
	{
		// Create filename
		$name = $name_set['name'] != '' ? $name_set['name'] : 'lib_' . date('Ymd_His');
		$filename = $name . $this->ext;

		// Make New Directory
		$newUploadDir = $this->upload_dir . MYDS . date('Ymd_His') . MYDS;
		$rs = @mkdir($newUploadDir, '0755');
		if($rs)
		{
			chmod($newUploadDir, 0755); // Set dir permission
			file_put_contents($newUploadDir . $filename, $this->processVar($name_set['file-header']) . "\n\n" . $data);

			if(file_exists($newUploadDir . $filename))
			{
				// Return url for file
				$this->compressed = array('dir' => $newUploadDir, 'file' => $filename);
				return $this->compressed;
			}
		}
	}

	// Process Variable Placeholders
	public function processVar($content) {

		$fileCount = count($this->fileList);
		$i = 1;
		foreach($this->fileList as $theFile)
		{
			$theFileList .= $theFile;
			if($fileCount > 1 && $fileCount != $i) {
				$theFileList .= ', ';
			}
			$i++;
		}

		$find = array (
			'[file list]',
			'[date:time]'
		);

		$replace = array (
			$theFileList,
			date('d/m/Y H:i')
		);

		return str_replace($find, $replace, $content);
	}

	// Execute all code
	public function execute($data)
	{
		if($this->upload($data))
		{
			$this->write($this->fp['content'], $data);
		}
	}
	
	public function check($data) {
		$option = '';
		foreach($data as $key => $value){
			if($value === "1"){
				$option .= ' --' . $key;
			}
		}
		
		return $option;
	}

	// Debugger
	public static function debug($data)
	{
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
}