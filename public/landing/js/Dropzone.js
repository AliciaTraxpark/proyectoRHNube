$("div#Dropzone").dropzone({
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    url: "/empleado/file",
    headers: {
        'X-CSRF-Token': $('input[name="authenticity_token"]').val()
    },
    sending: function(file, xhr, formData) {
        // Pass token. You can use the same method to pass any other values as well such as a id to associate the image with for example.
        formData.append("_token", $('[name=_token').val()); // Laravel expect the token post value to be named _token by default
    },
});
Dropzone.options.Dropzone = false;