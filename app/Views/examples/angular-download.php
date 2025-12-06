<?php 
// Write this in controller
$files = $this->Projects_Model->get_files($id, $type);
$this->load->library('zip');
$this->zip->archive('./uploads/files/projects/'.$projectId.'/submittal.zip');
$destination = './uploads/files/projects/'.$projectId.'/submittal.zip';
foreach ($files as $file) {
	$this->zip->read_file('./uploads/files/projects/'.$projectId.'/'.$file['file_name']);
}
$this->zip->archive('./uploads/files/projects/'.$projectId.'/submittal.zip');
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=submittal");
header("Content-length: " . filesize($destination));
header("Pragma: no-cache");
header("Expires: 0");
readfile($destination);
unlink($destination);

?>

<script type="text/javascript">
	// Angular Code to call the file
	$http({
		url: BASE_URL+'projects/download_files/'+PROJECTID+'/'+id+'/'+type,
		method: 'POST',
		params: {},
		headers: {
			'Content-type': 'application/zip',
		},
		responseType: 'arraybuffer'
	}).then(function (Data, status, headers, config) {
		if (Data.data.success) {
			if (Data.data.success == false) {
				globals.mdToast('success', Data.data.message);
			}
		} else {
			var file = new Blob([Data.data], {
				type: 'application/zip'
			});
			var fileURL = URL.createObjectURL(file);
			var a = document.createElement('a');
			a.href = fileURL;
			a.target = '_blank';
			a.download = name+'.zip';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		}
	});
</script>