$("#login-form").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this);
    let url = form.attr('action');

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            alert(data); // show response from the php script.
        }
    });


});