;
window.addEventListener('load', function() {
	var head = document.getElementsByTagName('head')[0];
	var fonts = [
		[
			'Roboto',
			[
				300,
				400,
				500,
				700
			]
		],
		[
			'Jost',
			[
				800,
			]
		],
		[
			'Playfair Display',
			[
				900,
			]
		]
	];
	fonts.forEach(function(font) {
		var link = document.createElement('link');
		link.href = '//fonts.googleapis.com/css2?family=' + font[0].replaceAll(" ", "+") + ':wght@' + font[1].join(';') + '&display=swap';
		link.rel = 'stylesheet';
		head.append(link);
	});
}, {
	passive: true
});