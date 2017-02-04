<?php 
function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}

function validID($id)
{
	$array = str_split($id);
	$valid = true;
	foreach ($array as $value) {
		if(!contains($value, "123456789abcdefghijklmnopqrstuvwxyz")){
			$valid = false;
		}
	}
	return $valid;
}

function isImage($pathToFile)
{
  if( false === exif_imagetype($pathToFile) )
   return FALSE;

   return TRUE;
}

function isVideo($ext)
{
  if($ext == 'mp4'){
	  return TRUE;
  } else {
	  return FALSE;
  }
}

function isAudio($ext)
{
  if($ext == 'mp3'){
	  return TRUE;
  } else {
	  return FALSE;
  }
}


if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$oid = $_GET['id'];
	
	if(strlen($id) >= 16 && strlen($id) <= 21) {
		if(contains('.', $id)){
			if(substr_count($id, '.') == 1){
				$id = explode('.', $id)[0];
			}
		}
		if(validID($id)){
			if (is_dir('files/'.$id.'')){
				ob_end_clean();
				$files = scandir('files/'.$id.'/');
				$file = 'files/'.$id.'/'.$files[2];
				$path_parts = pathinfo($file);
				
				if(!array_key_exists('extension', $path_parts)) {
					echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.css" />';
					echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.js"></script>';
					echo '<body>';
					echo '<script type="text/javascript">sweetAlert({title: "Error!", text: "Failed to download file!", type: "error", showConfirmButton: false});</script>';
					echo '</body>';
					exit;
				}
				
				$ext = urlencode($path_parts['extension']);
				
				
				//
				
				$filename = urlencode($path_parts['filename']);
				$filename = str_replace(array('"', "'", ' ', ','), '_', $filename);
				
				
				
				if(isImage($file)) {
					header('Content-type:image/png', true);
					header("Content-Disposition: inline; filename='$filename.$ext'", true);
				} elseif(isVideo($ext)) {
					header("Content-type: video/$ext", true);
					header("Content-Disposition: inline; filename='$filename.$ext'", true);
				} elseif(isAudio($ext)) {
					header("Content-type: audio/$ext", true);
					header("Content-Disposition: inline; filename='$filename.$ext'", true);
				} else {
					header("Content-type: application/$ext", true);
					header("Content-Disposition: attachment; filename='$filename.$ext'", true);
				}
				flush();
				echo file_get_contents($file);
				exit;
			}
		}
	}
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.js"></script>

<body>
<script type="text/javascript">sweetAlert({title: "Error!", text: "File not found!", type: "error", showConfirmButton: false});</script>
</body>