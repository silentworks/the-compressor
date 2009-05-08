<?php defined('SYSPATH') or die('No direct script access.');

class HomeController extends MasterController
{	
	public function __construct()
	{
		parent::__construct();
		use_helper('Yui', 'Zip');
		
		$this->vars = array();
	}
	
    public function index()
    {
    	// Define directory separator
    	define(MYDS, '/');
    	echo '/assets' . DS . 'compressor' . DS;
    	if($_POST['submit']){
			$yui = new Yui;
			$yui->compressor_dir = '/assets' . DS . 'compressor' . DS;
			$yui->execute(array_merge($_FILES, $_POST));
			
			$this->vars['error'] = $yui->error;
			
			$file_loc = $yui->compressed['dir'] . $yui->compressed['file'];
			$filename = $yui->compressed['file'];
			$filename_zip = current(explode('.', $yui->compressed['file'])) . '.zip';
			$zip_file_with_path = $yui->compressed['dir'] . $filename_zip;
			
			if($_POST['zipped'] === "1") {
				$zip = new Zip;
				$zip->addFile($file_loc, $filename);
				$zip->save($zip_file_with_path);
				$this->vars['zipped_file'] = '<a href="' . $zip_file_with_path .'" target="_blank">'. $filename_zip .'</a>';
			}
			
			$this->vars['compressed_file'] = '<a href="' . $file_loc .'" target="_blank">'. $filename .'</a>';
		}
		
    	$this->layout_vars['page_title'] = 'Compressor Form';
        $this->display('home/index', $this->vars);
    }
}
/* End of file HomeController.php */
/* Location: ./app/controllers/HomeController.php */