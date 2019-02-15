/*
 * Magazine sample
*/

function addPage(page, book) {

	var id, pages = book.turn('pages');

	// Create a new element for this page
	var element = $j('<div />', {});

	// Add the page to the flipbook
	if (book.turn('addPage', element, page)) {

		// Add the initial HTML
		// It will contain a loader indicator and a gradient
		element.html('<div class="gradient"></div><div class="loader"></div>');

		// Load the page
		loadPage(page, element);
	}

}

function loadPage(page, pageElement) {

	// Create an image element

	var img = $j('<img />');

	img.mousedown(function(e) {
		e.preventDefault();
	});

	img.load(function() {
		
		// Set the size
		$j(this).css({width: '100%', height: '100%'});

		// Add the image to the page after loaded

		$j(this).appendTo(pageElement);

		// Remove the loader indicator
		
		pageElement.find('.loader').remove();
	});

	// Load the page

	img.attr('src', '../media/catalogue/' +  page + '.jpg');

	// loadRegions(page, pageElement);

}

// Zoom in / Zoom out

function zoomTo(event) {

		setTimeout(function() {
			if ($j('.magazine-viewport').data().regionClicked) {
				$j('.magazine-viewport').data().regionClicked = false;
			} else {
				if ($j('.magazine-viewport').zoom('value')==1) {
					$j('.magazine-viewport').zoom('zoomIn', event);
				} else {
					$j('.magazine-viewport').zoom('zoomOut');
				}
			}
		}, 1);

}



// Load regions

function loadRegions(page, element) {

	$j.getJSON('../media/catalogue/'+page+'-regions.json').
		done(function(data) {

			$j.each(data, function(key, region) {
				addRegion(region, element);
			});
		});
}

// Add region

function addRegion(region, pageElement) {
	
	var reg = $j('<div />', {'class': 'region  ' + region['class']}),
		options = $j('.magazine').turn('options'),
		pageWidth = options.width/2,
		pageHeight = options.height;

	reg.css({
		top: Math.round(region.y/pageHeight*100)+'%',
		left: Math.round(region.x/pageWidth*100)+'%',
		width: Math.round(region.width/pageWidth*100)+'%',
		height: Math.round(region.height/pageHeight*100)+'%'
	}).attr('region-data', $j.param(region.data||''));


	reg.appendTo(pageElement);
}

// Process click on a region

function regionClick(event) {

	var region = $j(event.target);

	if (region.hasClass('region')) {

		$j('.magazine-viewport').data().regionClicked = true;
		
		setTimeout(function() {
			$j('.magazine-viewport').data().regionClicked = false;
		}, 100);
		
		var regionType = $j.trim(region.attr('class').replace('region', ''));

		return processRegion(region, regionType);

	}

}

// Process the data of every region

function processRegion(region, regionType) {

	data = decodeParams(region.attr('region-data'));

	switch (regionType) {
		case 'link' :

			window.open(data.url);

		break;
		case 'zoom' :

			var regionOffset = region.offset(),
				viewportOffset = $j('.magazine-viewport').offset(),
				pos = {
					x: regionOffset.left-viewportOffset.left,
					y: regionOffset.top-viewportOffset.top
				};

			$j('.magazine-viewport').zoom('zoomIn', pos);

		break;
		case 'to-page' :

			$j('.magazine').turn('page', data.page);

		break;
	}

}

// Load large page

function loadLargePage(page, pageElement) {
	
	var img = $j('<img />');

	img.load(function() {

		var prevImg = pageElement.find('img');
		$j(this).css({width: '100%', height: '100%'});
		$j(this).appendTo(pageElement);
		prevImg.remove();
		
	});

	// Loadnew page
	
	img.attr('src', '../media/catalogue/' +  page + '-large.jpg');
}

// Load small page

function loadSmallPage(page, pageElement) {
	
	var img = pageElement.find('img');

	img.css({width: '100%', height: '100%'});

	img.unbind('load');
	// Loadnew page

	img.attr('src', '../media/catalogue/' +  page + '.jpg');
}

// http://code.google.com/p/chromium/issues/detail?id=128488

function isChrome() {

	return navigator.userAgent.indexOf('Chrome')!=-1;

}

function disableControls(page) {
		if (page==1)
			$j('.previous-button').hide();
		else
			$j('.previous-button').show();
					
		if (page==$j('.magazine').turn('pages'))
			$j('.next-button').hide();
		else
			$j('.next-button').show();
}

// Set the width and height for the viewport

function resizeViewport() {

	var canvasElement = $j('#canvas'),
		colMainElement = $j(canvasElement).parents('.col-main'),
        containerElement = $j(canvasElement).find('.container'),
		options = $j('.magazine').turn('options');

	$j('.magazine').removeClass('animated');
	$j(colMainElement).css({
        'padding-right': 0,
		'padding-left': 0
	});
    $j(containerElement).css({
        'padding-right': 0,
        'padding-left': 0
    });

    width = $j(canvasElement).width();
    height = $j('#canvas').height()||600;

	$j('.magazine-viewport').css({
		width: width,
		height: height
	}).
	zoom('resize');

	if ($j('.magazine').turn('zoom')==1) {
		var bound = calculateBound({
			width: options.width,
			height: options.height,
			boundWidth: Math.min(options.width, width),
			boundHeight: Math.min(options.height, height)
		});

		if (bound.width%2!==0)
			bound.width-=1;

			
		if (bound.width!=$j('.magazine').width() || bound.height!=$j('.magazine').height()) {

			$j('.magazine').turn('size', bound.width, bound.height);

			if ($j('.magazine').turn('page')==1)
				$j('.magazine').turn('peel', 'br');

			$j('.next-button').css({height: bound.height, backgroundPosition: '-38px '+(bound.height/2-32/2)+'px'});
			$j('.previous-button').css({height: bound.height, backgroundPosition: '-4px '+(bound.height/2-32/2)+'px'});
		}

		// $j('.magazine').css({top: -bound.height/2, left: -bound.width/2});
	}

	var magazineOffset = $j('.magazine').offset(),
		boundH = height - magazineOffset.top - $j('.magazine').height(),
		marginTop = (boundH - $j('.thumbnails > div').height()) / 2;

	if (marginTop<0) {
		$j('.thumbnails').css({height:1});
	} else {
		$j('.thumbnails').css({height: boundH});
		$j('.thumbnails > div').css({marginTop: marginTop});
	}

	if (magazineOffset.top<$j('.made').height())
		$j('.made').hide();
	else
		$j('.made').show();

	$j('.magazine').addClass('animated');
	
}


// Number of views in a flipbook

function numberOfViews(book) {
	return book.turn('pages') / 2 + 1;
}

// Current view in a flipbook

function getViewNumber(book, page) {
	return parseInt((page || book.turn('page'))/2 + 1, 10);
}

function moveBar(yes) {
	if (Modernizr && Modernizr.csstransforms) {
		$j('#slider .ui-slider-handle').css({zIndex: yes ? -1 : 10000});
	}
}

function setPreview(view) {

	var previewWidth = 112,
		previewHeight = 73,
		previewSrc = 'pages/preview.jpg',
		preview = $j(_thumbPreview.children(':first')),
		numPages = (view==1 || view==$j('#slider').slider('option', 'max')) ? 1 : 2,
		width = (numPages==1) ? previewWidth/2 : previewWidth;

	_thumbPreview.
		addClass('no-transition').
		css({width: width + 15,
			height: previewHeight + 15,
			top: -previewHeight - 30,
			left: ($j($j('#slider').children(':first')).width() - width - 15)/2
		});

	preview.css({
		width: width,
		height: previewHeight
	});

	if (preview.css('background-image')==='' ||
		preview.css('background-image')=='none') {

		preview.css({backgroundImage: 'url(' + previewSrc + ')'});

		setTimeout(function(){
			_thumbPreview.removeClass('no-transition');
		}, 0);

	}

	preview.css({backgroundPosition:
		'0px -'+((view-1)*previewHeight)+'px'
	});
}

// Width of the flipbook when zoomed in

function largeMagazineWidth() {
	
	return 2214;

}

// decode URL Parameters

function decodeParams(data) {

	var parts = data.split('&'), d, obj = {};

	for (var i =0; i<parts.length; i++) {
		d = parts[i].split('=');
		obj[decodeURIComponent(d[0])] = decodeURIComponent(d[1]);
	}

	return obj;
}

// Calculate the width and height of a square within another square

function calculateBound(d) {
	
	var bound = {width: d.width, height: d.height};

	if (bound.width>d.boundWidth || bound.height>d.boundHeight) {
		
		var rel = bound.width/bound.height;

		if (d.boundWidth/rel>d.boundHeight && d.boundHeight*rel<=d.boundWidth) {
			
			bound.width = Math.round(d.boundHeight*rel);
			bound.height = d.boundHeight;

		} else {
			
			bound.width = d.boundWidth;
			bound.height = Math.round(d.boundWidth/rel);
		
		}
	}
		
	return bound;
}