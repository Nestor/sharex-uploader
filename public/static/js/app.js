function sAlert(message, title = '', type = 'error') {
    sweetAlert(({
        title: title,
        text: message,
        type: type,
        showConfirmButton: false
    }));
}

new Clipboard('.file-url-copy');
$('.file-url-copy').click(function() {
    Materialize.toast('URL copied to clipboard!', 5000)
});

$('.upload-form').submit(function() {
    var uploadForm = this;

    $.post('/auth', {
        password: $('.txt-password').val()
    }, function(data) {
        if (data.status == 'error') {
            return Materialize.toast(data.message, 5000);
        }

        Materialize.toast('Uploading file...', 400000000, 'toast-uploading');
        $('.upload-form').fadeOut(150);

        $('.upload-container').fadeIn('slow');

        var formData = new FormData(uploadForm);
        $.ajax({
            type: 'POST',
            url: '/upload',
            data: formData,
            xhr: function() {
                var req = $.ajaxSettings.xhr();
                if (req.upload) {
                    req.upload.addEventListener('progress', uploadProgress, false);
                }
                return req;
            },
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status == 'error') {
                    return Materialize.toast(data.message, 5000);
                }
console.log(data)
                $('.upload-progress').fadeOut('slow');

                $('.file-url-container').fadeIn('slow');
                $('.file-url').val(data.message);
                $('.file-url-copy').attr('data-clipboard-text', data.message);
                $('.toast-uploading').fadeOut('slow');
                return Materialize.toast('File uploaded successfully!', 5000);

            },
            error: function(data) {
                return Materialize.toast(data.message, 5000)
            }
        });
    });
    return false;
});

function uploadProgress(e) {
    if (e.lengthComputable) {
        var max = e.total;
        var current = e.loaded;
        var progress = (current * 100) / max;

        $('.upload-progress .determinate').css('width', Math.round(progress) + '%');
    }
}
