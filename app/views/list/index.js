$(function(){
	
	var images = $('#images').GITheWall({
		arrows: true,
		closebutton: true,
		keyboardNavigation: true,
		animationSpeed:0,
		nextButtonClass: 'glyphicon glyphicon-arrow-right',
		prevButtonClass: 'glyphicon glyphicon-arrow-left',
		closeButtonClass: 'glyphicon glyphicon-remove'
	});
	
	$(document).on('click', '.image-delete', function(){
		var access_key = $(this).attr('data-target');
		
		$.ajax({
			type: "POST",
			url : "/image/delete",
			data : {
				client_id : client_id,
				access_key : access_key
			}
		}).done(function(result){
			if(result)
			{
				images.hideExpander();
				$('li[data-href="#' + access_key + '"]').remove();
				$('#' + access_key).remove();
				images.refresh();
			}
		});
		
		return false;
	});
});
