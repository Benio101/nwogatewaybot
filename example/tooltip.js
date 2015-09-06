$(document).on('mouseenter', '[data-tt-item]', function(event) {
	tooltipShowForElement(this, event);
});

$(document).on('mouseleave', '[data-tt-item]', function(event) {
	tooltipHideForElement(this, event);
});

var s_curTooltip = { parent: null, tip: null };
var s_tooltipID = 0;
function tooltipShowForElement(elem, event)
{
	if(s_curTooltip.tip)
	{
		$(s_curTooltip.tip).remove();
	}

	s_curTooltip.parent = elem;
	var id = 'tooltip-'+s_tooltipID;
	s_tooltipID++;
	var text = $(elem).attr('data-tt-text');
	if(text)
	{
		$('#tooltip-content').html($('<div>').append(text));
	}
	else
	{
		s_curTooltip.tip = $('<div id="'+id+'" class="tooltip"><div class="loading-image"></div></div>');

		var itemNameOrID = $(elem).attr('data-tt-item');
		if(itemNameOrID)
		{
			$.get('tooltip/' + itemNameOrID, function(data){
				$('#tooltip, .tooltip').show();
				$('#tooltip-content').html($('<div>').append(cleanUpTooltip(data)));
				
				var right = null;
				var rightOffset = $(elem).offset().left + $(elem).outerWidth() + 10;
				if(rightOffset + $('#tooltip-content').outerWidth() + 10 <= $(window).width()){
					right = rightOffset;
				} else {
					right = $(elem).offset().left - 40 - $('#tooltip-content').outerWidth();
					if(right < 0){
						right = 10;
					}
				}
				
				var top = null;
				var topOffset = $(elem).offset().top;
				if(topOffset + $('#tooltip-content').outerHeight() + 10 <= $(window).scrollTop() + $(window).height()){
					top = topOffset;
				} else {
					top = topOffset + $(elem).outerHeight() - $('#tooltip-content').outerHeight();
					if(top < $(window).scrollTop()){
						top = $(window).scrollTop() + 10;
					}
				}
				
				$('#tooltip').css({
					'top': top,
					'left': right,
				});
			});
		}
	}
}

function tooltipHideForElement(elem, event)
{
	$('#tooltip, .tooltip').hide();
}

function cleanUpTooltip(tip)
{
	// <img src=foobar
	//    to
	// <img src="/tex/foobar"
	var reImg = /(<img[^>]+)src=([^ >]+)/gi;
	tip = tip.replace(reImg, '$1src="http://gateway.playneverwinter.com/tex/$2"');

	// <font style=foobar>
	//    to
	// <span class="foobar">
	var reFontStyle = /<font style=([^ >]+)/gi;
	tip = tip.replace(reFontStyle, '<span class="$1"');
	// <font.*>
	//    to
	// <span.*>
	var reFont = /<font/gi;
	tip = tip.replace(reFont, '<span');
	// </font>
	//    to
	// </span>
	var reFontClose = /<\/font/gi;
	tip = tip.replace(reFontClose, '</span');

	// align=right
	//    to
	// class="align-right"
	var reAlign = /align=(right|left)/gi;
	tip = tip.replace(reAlign, 'class="align-$1"');

	// <span color=blarg>
	//    to
	// <span class=blarg>
	var reColor = /(<span[^>]+)color=/gi;
	tip = tip.replace(reColor, '$1class=');

	// The first table
	var reTable1 = /<table>/i;
	tip = tip.replace(reTable1, '<table class="tooltipTable">');

	// The second table is for gem slots.
	tip = tip.replace('<table>', '<table class="gemslots">');

	// border=4
	//    to
	// class="tableSets"
	tip = tip.replace(/border=4/gi, 'class="tableSets"');

	// Remove extraneous width=100% attribs
	tip = tip.replace(/width=100%/gi, '');

	return tip;
}
