$(document).ready(function() {
    setInterval(function() { 
        hideColorSquares();
    });
});

function hideColorSquares()
{
	$('.ajax_block_product div.color-list-container, .ajax_block_product ul.color_to_pick_list').addClass('hide');
}