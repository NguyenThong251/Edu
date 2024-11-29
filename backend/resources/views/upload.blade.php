<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải lên file lên S3</title>
    <style>
        #progressBar {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            margin-top: 20px;
            height: 20px;
        }
        #progress {
            width: 0;
            height: 100%;
            background-color: #4caf50;
            border-radius: 5px;
            text-align: center;
            color: white;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <h1>Tải lên file lên S3</h1>

    <form id="uploadForm" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="fileInput">Chọn file:</label>
        <input type="file" id="fileInput" name="content" accept="video/*, .pdf">
        <input type="hidden" id="durationInput" name="duration">
        <button type="submit">Tải lên</button>
    </form>

    <!-- Thanh tiến độ -->
    <div id="progressBar">
        <div id="progress">0%</div>
    </div>

    <div id="result"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Lắng nghe sự kiện khi file được chọn
        document.getElementById('fileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const durationInput = document.getElementById('durationInput');

            // Kiểm tra nếu là video, lấy duration
            if (file && file.type.startsWith('video')) {
                const videoElement = document.createElement('video');
                videoElement.preload = 'metadata';

                videoElement.onloadedmetadata = function() {
                    const duration = videoElement.duration;
                    durationInput.value = duration;  // Đặt giá trị duration vào hidden input
                };

                videoElement.onerror = function() {
                    document.getElementById('result').innerHTML = 'Không thể lấy thời gian video.';
                };

                videoElement.src = URL.createObjectURL(file);
            }
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();  // Ngừng hành động mặc định của form
            var formData = new FormData(this);

            $.ajax({
                url: '{{ route('lectures.store') }}',  // Đảm bảo route đúng
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 60000,  // Thời gian tối đa cho upload
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = (e.loaded / e.total) * 100;
                            $('#progress').css('width', percent + '%');
                            $('#progress').text(Math.round(percent) + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    $('#result').html('Tải lên thành công! <a href="' + response.url + '" target="_blank">Xem file</a>');
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi tải lên:', error);
                    $('#result').html('Có lỗi khi tải lên.');
                }
            });
        });
    </script>
</body>
</html>
