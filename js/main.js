

$(document).ready(function() {
		
	$.post("/index.php", {load: 'content'}, function(data){
        $('#testr').load('/html/' + data);
    });

	$("body").on('click', '#submit', function(event) {
        $.post("/index.php", { 
                mail: $('#inputEmail').val(),
                pass: $('#inputPassword').val(),
				name: $('#inputName').val()
        } , 

        function(data){
			try{
				data = $.parseJSON(data);
				if(data[0])
					$('#testr').load('/html/' + data[1]);
			}catch(e){
				$('#error').text(data);
			}
        }, 'TEXT');
	});
})