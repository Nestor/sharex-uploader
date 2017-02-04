<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>

<html>
<body class="white darken-3">

<?php 
function gen_uid($l=16){
    return substr(str_shuffle("123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
}

$PASSWORD='EDIT_THIS'; //Password required to upload a file
$url = "EDIT_THIS"; //url of the folder that contains dl.php (with a / at the end)
$filerand = gen_uid(16);
$SUBDIR='files/'.$filerand.'';

while (is_dir($SUBDIR)) {
	$filerand = gen_uid(16);
	$SUBDIR='files/'.$filerand.'';
}

$scriptname = basename($_SERVER["SCRIPT_NAME"]);
if (isset($_FILES['filetoupload']) && isset($_POST['password']))
{   
	sleep(0.5); // Reduce brute-force attack effectiveness.
	
	$pathinfo = pathinfo($_FILES['filetoupload']['name']);
	
	if(!array_key_exists('extension', $pathinfo)) {
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.css" />';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.1.1/sweetalert2.min.js"></script>';
		echo '<body>';
		echo '<script type="text/javascript">sweetAlert({title: "Error!", text: "Failed to upload file", type: "error", showConfirmButton: false});</script>';
		echo '</body>';
		exit;
	}
	
	if (!is_dir($SUBDIR)) 
	{
		mkdir($SUBDIR,0705); chmod($SUBDIR,0705);
		if ($_POST['password']!=$PASSWORD) { print '<script type="text/javascript">sweetAlert(({title: "Error!", text: "Wrong password!", type: "error", showConfirmButton: false}));</script>'; exit(); }
		$filename = $SUBDIR.'/'.basename($_FILES['filetoupload']['name']); 

		$fileurl=$url.'dl/'.$filerand.'/';
		header("file-url: '$fileurl");
		
		if (file_exists($filename)) { print '<script type="text/javascript">sweetAlert({title: "Error!", text: "File already exists!", type: "error", showConfirmButton: false});</script>'; exit(); }
			if(move_uploaded_file($_FILES['filetoupload']['tmp_name'], $filename))
			{
				echo '<script type="text/javascript">swal({title: "Upload complete", html: "Link: <a href=\"'.$fileurl.'\">'.$fileurl.'</a>", type: "success", closeOnConfirm: false, confirmButtonText: "Copy"}, function(){window.prompt("Copy to clipboard", "'.$fileurl.'");});</script>';
			} 
			else { echo '<script type="text/javascript">sweetAlert({title: "Error!", text: "Failed to upload file! The file may be too big", type: "error", showConfirmButton: false});</script>'; }
		exit();
	}
	else { echo '<script type="text/javascript">sweetAlert({title: "Error!", text: "Failed to upload file! UID Generation failed", type: "error", showConfirmButton: false});</script>'; }
}
print <<<EOD
<div class="row">
<div class="col s12 m4">
</div>

<div class="col s12 m4">
<div class="card-panel grey darken-1 hoverable" style="margin-top: 30%;">
<div class="card-content white-text">
<h5 class="card-title collection-header"><center>File Uploader</br> </center></h5>
<form method="post" enctype="multipart/form-data">  
	<label>File Uploader</label>
	<div class="input-field col s12">
	<div class="file-field input-field">
	<div class="btn">
	<span>File</span>
	<input type="file" name="filetoupload">
	</div>
	
	<div class="file-path-wrapper">
	<input class="file-path" type="text" placeholder="Select a file">
	</div>
	</div>
	<input id="password" type="password" name="password" placeholder="Password"><br>
	
	</div>
	<div class="card-action">
	<div class="center">
	<button class="center btn waves-effect waves-light tooltipped" onclick="Materialize.toast('Uploading file...', 400000000)" type="submit" value="Upload" data-position="right" data-delay="150" data-tooltip="Click to upload">Upload</button>
	</div>
</form>

EOD;
?>
</body>
</html>