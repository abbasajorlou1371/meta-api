<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumable.js File Upload</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div id="upload-form" class="text-center">
            <form id="file-upload-form" class="text-center">
                <div class="input-group mb-3">
                    <input type="file" name="file" id="file-input" class="form-control" required />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="upload-progress" class="text-center w-50" style="display: none;">
            <p>Uploading: <span id="progress-percentage">0</span>%</p>
            <div class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>
        <div id="file-info" class="text-left" style="display: none;">
            <p>File Name: <span id="file-name"></span></p>
            <p>File Path: <span id="file-path"></span></p>
            <p>Mime Type: <span id="file-mime-type"></span></p>
        </div>
    </div>

    <script>
        document.getElementById('file-upload-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const fileInput = document.getElementById('file-input');
            const resumable = new Resumable({
                target: 'https://api.rgb.irpsc.com/upload',
                chunkSize: 100 * 1024 * 1024, // 1MB
                headers: {
                    'Accept': 'application/json',
                    // 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                testChunks: false,
                maxFiles: 1,
                throttleProgressCallbacks: 1,
            });

            resumable.on('fileAdded', function() {
                document.getElementById('upload-form').style.display = 'none';
                document.getElementById('upload-progress').style.display = 'block';
                resumable.upload();
            });

            resumable.on('fileProgress', function(file) {
                const progress = Math.floor(file.progress() * 100);
                document.getElementById('progress-percentage').innerText = progress;
                document.getElementById('progress-bar').style.width = progress + '%';
                document.getElementById('progress-bar').innerText = progress + '%';
            });

            resumable.on('fileSuccess', function(file, response) {
                const result = JSON.parse(response);
                document.getElementById('file-name').innerText = result.name;
                document.getElementById('file-path').innerText = result.path;
                document.getElementById('file-mime-type').innerText = result.mime_type;
                document.getElementById('upload-progress').style.display = 'none';
                document.getElementById('file-info').style.display = 'block';
            });

            resumable.on('fileError', function(file, message) {
                console.error('Error uploading file:', message);
                document.getElementById('upload-progress').style.display = 'none';
                document.getElementById('upload-form').style.display = 'block';
            });

            if (fileInput.files.length > 0) {
                resumable.addFile(fileInput.files[0]);
            }
        });
    </script>
</body>

</html>
</div>
