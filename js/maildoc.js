$(document).ready(function() {
	lod = 1;
	
	$(window).scroll(function(){
		if($('tbody').html())
			addMsg($("tbody").attr('data-num'));
	});
		
	
	$.post("/index.php", { 
		load: 'menu'
    } , 

    function(data){
		$('#text').html();
		$.each( data[0], function( key, value ) {
			$('.nav-sidebar').append('<li><a href="'+key+'">'+value+'</a></li>');
		});
		$('.nav-sidebar').slideToggle(650);
		$('#dirLoad').hide()
		
		var k = 0;
		$.each( data[1], function( key, value ) {
			$('tbody').append('<tr data-id="'+value['id']+'"><td>'+value['id']+'</td><td><span class="glyphicon glyphicon-' + ((value['dmarc']) ? "ok" : "remove") + '" aria-hidden="true"></span></td><td>'+value['from']+'</td><td>'+value['subject']+'</td><td>'+value['date']+'</td><td><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></td></tr>');
			k = key
		});
		$('tbody').slideToggle(650)
		addMsg(k);
    }, 'JSON');
	
	$('tbody').on( 'click', '.glyphicon', function () {
		if(confirm('Вы уверены?')){
			$('#bg').show();
			var row = $(this).parent().parent();
			$.post("/index.php", { drop: row.attr('data-id'), dir:$('.sub-header').attr('data-curdir')} , 
				function(data){
					if(data){
						row.hide(200);
						row.detach();
						$('#bg').hide();
					}
			}, 'JSON');	
		}
		return false;
	});
	
	$('tbody').on( 'click', '#exit', function () {
		alert()
		$.post("/index.php", { exit: 1 } , 	function(data){
			$('#testr').load('/html/' + data);
		}, 'JSON');	
		return false;
	});
	
	$('.nav-sidebar').on( 'click', 'a', function () {
		$('#text').html('');
		$("tbody").attr('data-num', 0);
		$('.sub-header').attr('data-curdir', $(this).attr('href'))
		$('.sub-header').html($(this).html());
		$('title').html($(this).html())
		addMsg(0, $(this).attr('href'), 1);
		$('tbody').html('');
		$('.table-striped').show();
		return false;
	});
	
	$('tbody').on( 'click', 'tr', function () {
		$('#bg').show();
		var dir = $('.sub-header').attr('data-curdir');
		$.post("/index.php", { load: 'open', id: $(this).attr('data-id'), dir:dir} , 
			function(data){
				$('#text').html(data);
				$('#text').show();
				$('tbody').html('');
				$('.table-striped').hide();
				$('#bg').hide();

		}, 'HTML');	
		return false;
	});
	
	var scrH = window.innerHeight;

	function addMsg(id, dir, f){
		dir = typeof b !== 'undefined' ?  dir : '';
		f = typeof f !== 'undefined' ?  f : 0;
		var scro = $(this).scrollTop();
		var scrHP = $("tbody").height();
		var scrH2 = 0;
		scrH2 = scrH + scro;
		var leftH = scrHP - scrH2;
		dir = (dir) ? dir : $('.sub-header').attr('data-curdir');
		if(leftH<100 && window.lod || f){
			$('#bg').show();
			$('#mailLoad').show(250);
			window.lod = 0;
			$('#text').html();
			if($('tbody').length){
				$.post("/index.php", { load: 'msg', id: id, dir:dir} , 
				function(data){
					var k = 0;
					$.each( data[0], function( key, value ) {
						$('tbody').append('<tr data-id="'+value['id']+'"><td>'+value['id']+'</td><td><span class="glyphicon glyphicon-' + ((value['dmarc']) ? "ok" : "remove") + '" aria-hidden="true"></span></td><td>'+value['from']+'</td><td>'+value['subject']+'</td><td>'+value['date']+'</td><td><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></td></tr>');
						k = key;
					});
					if($("tbody").attr('data-num') < k)
						$("tbody").attr('data-num', k);
					window.lod = 1;
					$('#mailLoad').hide();
					$('#bg').hide();
					if(k>0){
						addMsg(k, dir);
					}
				}, 'JSON');	
			}
		}
	}

	
})