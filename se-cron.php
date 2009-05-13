<?php
// Include the zip function
include('app/helpers/CreateZipFile.inc.php');

// Define all variables
$usr_path = "upload/";
$dir = get_files($usr_path);

$i = 0;
while($i < count($dir)) :
	foreach ($dir as $d) :
		$dir_wpath = $usr_path.$d;
			if(is_empty($dir_wpath)) :
				rmdir($dir_wpath);
			; else :
				foreach(get_files($dir_wpath) as $f) :
					if(file_exists($backup_wpath)) :
						unlink($dir_wpath.'/'.$f);
					; else :
						$outputDir = "backup/"; //Replace "/" with the name of the desired output directory. 
						$zipName = "backup_" . date('Ymd_His') . ".zip";
						
						$zip = new CreateZipFile;
						//Code toZip a directory and all its files/subdirectories 
						$zip->zipDirectory("upload"); 
						
						$rand = md5(microtime().rand(0,999999)); 
						$backup_wpath = $outputDir . $zipName; 
						$fd = fopen($backup_wpath, "wb"); 
						$out = fwrite($fd,$zip->getZippedfile()); 
						fclose($fd);
						
						#$zip->addFile($dir_wpath, $f);
						#$zip->save($backup_wpath);
					endif;
				endforeach;
			endif;
	endforeach;
$i++;
endwhile;

/**
 * Get all files in directory
 *
 * @param string $path 
 * @return array
 * @author Andrew Smith
 */
function get_files($path){	
	$content = array();
	if(is_dir($path)) :
		if($dir = opendir($path)) :
			while(false !== ($file = readdir($dir))) :
				if($file != '.' && $file != '..' && $file != 'index.html') :
					$content[] = $file;
				endif;
			endwhile;
			closedir($dir);
		endif;
	endif;
	return $content;
}

/**
 * Check if directory is empty
 *
 * @param string $dir 
 * @return void
 * @author Andrew Smith
 */
function is_empty($dir){ 
     return (($files = @scandir($dir)) && count($files) <= 2); 
}
// End of file