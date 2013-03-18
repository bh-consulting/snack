function snakeFrucht() {
	frucht = Math.floor((Math.random() * 1000) % fruchts.length);
	return fruchts[frucht];
}

function snake() {
    var Zeilen, Spalten;
    var Index = 0;

    if($('#snake').length == 0) {
	$('.bhbody').after('<div id="snakescore" style="display: none">');
	$('.bhbody').after('<div id="snakelevel" style="display: none">');
	$('.bhbody').after('<div id="snakefade" style="display: none">');
	$('.bhbody').after('<table id="snake" cellspacing="0" cellpadding="0">');

	for (Zeilen = 0; Zeilen < 11; Zeilen++)
	{
	    $('#snake').append('<tr>');

	    for (Spalten = 0; Spalten < 20; Spalten++)
	    {
		$('#snake tr:last-child').append('<td id="Zelle' + Index + '">');
		Index++;
	    }
	}

	$('#snake').wrap('<div id="snakewrap" style="display: none">');
   }

   $(document).scrollTop(0);

   $(document).on('scroll', function() {
       $(document).scrollTop(0);
       $(document).scrollLeft(0);
   });

   $('#snakefade').fadeTo(700, 0.9, function() {
	$('#snakefade').fadeTo(700, 0.6);
	$('#snakewrap').slideToggle(800, function() {
	    Start();
	});
   });
}

function snakeEnd() {
	Game = false;
        $(document).off('scroll');
	$('#snakewrap').fadeOut();
	$('#snakefade').fadeOut();
	$('#snakescore').fadeOut();
	$('#snakelevel').fadeOut();
}


//
// SNAKE FROM: http://www.freejavascriptgames.info/games/snake.html
//

var i;
Richtung = '+1';
var block = 0;
var zuEnde = 0;
var Zelle;
var Countdown = 3;
var Leckerli;
var Level = 0;
var Fruechte;
var blah, fnord = false;
var Snake = new Array();
var Kopf;
var code = 0;
var Game = false;

var fruchts = [ 'icon-glass', 'icon-music', 'icon-search', 'icon-envelope', 'icon-heart', 'icon-star', 'icon-star-empty', 'icon-user', 'icon-film', 'icon-th-large', 'icon-th', 'icon-th-list', 'icon-zoom-in', 'icon-zoom-out', 'icon-off', 'icon-signal', 'icon-cog', 'icon-trash', 'icon-home', 'icon-file', 'icon-time', 'icon-road', 'icon-download-alt', 'icon-download', 'icon-upload', 'icon-inbox', 'icon-play-circle', 'icon-repeat', 'icon-refresh', 'icon-list-alt', 'icon-lock', 'icon-flag', 'icon-headphones', 'icon-volume-off', 'icon-volume-down', 'icon-volume-up', 'icon-qrcode', 'icon-barcode', 'icon-tag', 'icon-tags', 'icon-book', 'icon-bookmark', 'icon-print', 'icon-camera', 'icon-font', 'icon-text-height', 'icon-text-width', 'icon-align-left', 'icon-align-center', 'icon-align-right', 'icon-align-justify', 'icon-list', 'icon-indent-left', 'icon-indent-right', 'icon-facetime-video', 'icon-picture', 'icon-pencil', 'icon-map-marker', 'icon-adjust', 'icon-tint', 'icon-edit', 'icon-share', 'icon-check', 'icon-move', 'icon-step-backward', 'icon-fast-backward', 'icon-backward', 'icon-pause', 'icon-stop', 'icon-forward', 'icon-fast-forward', 'icon-step-forward', 'icon-eject', 'icon-remove-sign', 'icon-ok-sign', 'icon-question-sign', 'icon-info-sign', 'icon-screenshot', 'icon-remove-circle', 'icon-ok-circle', 'icon-ban-circle', 'icon-share-alt', 'icon-plus', 'icon-minus', 'icon-asterisk', 'icon-exclamation-sign', 'icon-gift', 'icon-leaf', 'icon-fire', 'icon-eye-open', 'icon-eye-close', 'icon-warning-sign', 'icon-plane', 'icon-calendar', 'icon-random', 'icon-comment', 'icon-magnet', 'icon-chevron-up', 'icon-chevron-down', 'icon-retweet', 'icon-shopping-cart', 'icon-folder-close', 'icon-folder-open', 'icon-hdd', 'icon-bullhorn', 'icon-bell', 'icon-certificate', 'icon-thumbs-up', 'icon-thumbs-down', 'icon-hand-right', 'icon-hand-left', 'icon-hand-up', 'icon-hand-down', 'icon-circle-arrow-right', 'icon-circle-arrow-left', 'icon-circle-arrow-up', 'icon-circle-arrow-down', 'icon-globe', 'icon-wrench', 'icon-tasks', 'icon-filter', 'icon-briefcase', 'icon-fullscreen' ];

function Tastendruck(Druck)
{
	if (document.all)
	    k = window.event.keyCode;
	else
	    k = Druck.which;

	if(Game) {
	    if (k == 27) { Countdown = 3; reset(); snakeEnd(); }
	    if (k == 37 && !block && Richtung != '+1')  { Richtung = '-1';  block = 1; }
	    if (k == 38 && !block && Richtung != '+20') { Richtung = '-20'; block = 1; }
	    if (k == 39 && !block && Richtung != '-1')  { Richtung = '+1';  block = 1; }
	    if (k == 40 && !block && Richtung != '-20') { Richtung = '+20'; block = 1; }
	}

	switch(k) {
	    case 69:
		code = 0;
	    break;

	    case 83:
		if(code == 0)
			code++;
	    break;

	    case 73:
		if(code == 1)
			code++;
	    break;

	    case 65:
		if(code == 2)
		    code++;
	    break;

	    case 76:
		if(code == 3)
		    snake();
	    break;
	}
}

var C3 = [48, 49, 67, 70, 90, 109, 130, 147, 150, 168, 169];
var C2 = [48, 49, 67, 70, 90, 109, 128, 147, 167, 168, 169, 170];
var C1 = [49, 68, 69, 87, 89, 109, 129, 149, 167, 168, 169, 170, 171];
var Smiley = [27, 28, 29, 30, 31, 32, 46, 53, 65, 68, 71, 74, 85, 88, 91, 94, 105, 114, 125, 127, 132, 134, 145, 148, 149, 150, 151, 154, 166, 173, 187, 188, 189, 190, 191, 192];
var Frowny = [27, 28, 29, 30, 31, 32, 46, 53, 65, 68, 71, 74, 85, 88, 91, 94, 105, 114, 125, 129, 130, 134, 145, 148, 151, 154, 166, 173, 187, 188, 189, 190, 191, 192];
var Jubel1 = [27, 28, 29, 30, 31, 32, 41, 46, 53, 58, 61, 65, 68, 71, 74, 78, 81, 85, 88, 91, 94, 98, 102, 103, 104, 105, 114, 115, 116, 117, 125, 127, 128, 129, 130, 131, 132, 134, 145, 148, 149, 150, 151, 154, 166, 173, 187, 188, 189, 190, 191, 192];
var Jubel2 = [20, 39, 41, 47, 48, 49, 50, 51, 52, 58, 62, 66, 73, 77, 83, 85, 88, 91, 94, 96, 104, 105, 108, 111, 114, 115, 125, 134, 145, 147, 148, 149, 150, 151, 152, 154, 165, 168, 169, 170, 171, 174, 186, 193, 207, 208, 209, 210, 211, 212];

// Leer:
var Wall0     = []
var noFruit0  = []
// Rechteck in der Mitte:
var Wall1     = [67, 68, 69, 70, 71, 72, 87, 92, 107, 112, 127, 132, 147, 148, 149, 150, 151, 152]
var noFruit1  = [88, 89, 90, 91, 108, 109, 110, 111, 128, 129, 130, 131]
// 2 Dreiecke:
var Wall2     = [40, 59, 60, 61, 78, 79, 81, 82, 97, 98, 102, 103, 116, 117, 121, 122, 137, 138, 140, 141, 158, 159, 160, 179]
var noFruit2  = [80, 99, 100, 101, 118, 119, 120, 139]
// Auto:
var Wall3     = [46, 47, 48, 49, 50, 51, 52, 53, 54, 65, 70, 75, 83, 84, 86, 87, 88, 89, 90, 91, 92, 93, 94, 96, 102, 110, 117, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 143, 146, 153, 156, 164, 165, 174, 175]
var noFruit3  = [66, 67, 68, 69, 71, 72, 73, 74, 85, 95, 103, 104, 105, 106, 107, 108, 109, 111, 112, 113, 114, 115, 116, 144, 145, 154, 155]
// JS:
var Wall4     = [68, 71, 72, 73, 74, 75, 76, 77, 88, 91, 108, 111, 112, 113, 114, 115, 116, 117, 128, 137, 148, 157, 162, 163, 164, 165, 166, 167, 168, 171, 172, 173, 174, 175, 176, 177]
var noFruit4  = [92, 93, 94, 95, 96, 97]
// 5 Sternchen
var Wall5     = [15, 34, 36, 44, 55, 63, 65, 84, 110, 129, 131, 141, 150, 157, 160, 162, 176, 178, 181, 197]
var noFruit5  = [35, 64, 130, 140, 161, 177, 180, 200, 201]
// 3 Karos:
var Wall6     = [23, 36, 42, 44, 55, 57, 61, 65, 74, 78, 82, 84, 95, 97, 103, 110, 116, 129, 131, 148, 152, 169, 171, 190]
var noFruit6  = [43, 56, 62, 63, 64, 75, 76, 77, 83, 96, 130, 149, 150, 151, 170]
// Kreuz
var Wall7     = [10, 30, 50, 70, 90, 100, 101, 102, 103, 104, 115, 116, 117, 118, 119, 130, 150, 170, 190, 210]
var noFruit7  = []
// Misel:
var Wall8     = [61, 62, 63, 64, 65, 67, 69, 70, 71, 73, 74, 75, 77, 81, 83, 85, 87, 89, 93, 97, 101, 103, 105, 107, 109, 110, 111, 113, 114, 115, 117, 121, 125, 127, 131, 133, 137, 141, 145, 147, 149, 150, 151, 153, 154, 155, 157, 158, 159]
var noFruit8  = [82, 84, 90, 91, 94, 95, 102, 104, 129, 130, 134, 135]
// 8 Quadrate und ein Rechteck in der Mitte:
var Wall9     = [21, 22, 23, 25, 26, 27, 32, 33, 34, 36, 37, 38, 41, 43, 45, 47, 52, 54, 56, 58, 61, 62, 63, 65, 66, 67, 72, 73, 74, 76, 77, 78, 89, 90, 109, 110, 129, 130, 141, 142, 143, 145, 146, 147, 152, 153, 154, 156, 157, 158, 161, 163, 165, 167, 172, 174, 176, 178, 181, 182, 183, 185, 186, 187, 192, 193, 194, 196, 197, 198]
var noFruit9  = [42, 46, 53, 57, 162, 166, 173, 177]
// 4 kleine und ein großes Kreuz:
var Wall10    = [24, 30, 35, 42, 43, 45, 46, 50, 53, 54, 56, 57, 64, 70, 75, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 113, 114, 115, 116, 117, 118, 144, 150, 155, 162, 163, 165, 166, 170, 173, 174, 176, 177, 184, 190, 195]
var noFruit10 = [44, 55, 164, 175]
// Inka-Gesicht:
var Wall11    = [21, 22, 23, 24, 25, 26, 27, 32, 33, 34, 35, 36, 37, 38, 47, 52, 61, 62, 63, 64, 65, 67, 72, 74, 75, 76, 77, 78, 87, 89, 90, 92, 101, 102, 103, 104, 105, 106, 107, 109, 110, 112, 113, 114, 115, 116, 117, 118, 121, 138, 141, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 158, 161, 178, 181, 182, 183, 184, 185, 186, 187, 192, 193, 194, 195, 196, 197, 198]
var noFruit11 = []
// symmetrisches Labyrinth:
var Wall12    = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 58, 61, 63, 64, 65, 66, 67, 68, 71, 72, 73, 74, 75, 76, 78, 81, 83, 96, 98, 103, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 116, 121, 123, 136, 138, 141, 143, 144, 145, 146, 147, 148, 151, 152, 153, 154, 155, 156, 158, 161, 178, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198]
var noFruit12 = []
// Patience:
var Wall13    = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 61, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 81, 99, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 119, 121, 139, 141, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 161, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198]
var noFruit13 = []
// 2 Euros:
var Wall14    = [21, 22, 23, 24, 25, 26, 27, 28, 30, 31, 32, 33, 34, 35, 36, 37, 38, 48, 50, 68, 70, 81, 82, 83, 84, 85, 86, 87, 88, 90, 91, 92, 93, 94, 95, 96, 97, 98, 108, 109, 110, 121, 122, 123, 124, 125, 126, 127, 128, 130, 131, 132, 133, 134, 135, 136, 137, 138, 148, 150, 168, 170, 181, 182, 183, 184, 185, 186, 187, 188, 190, 191, 192, 193, 194, 195, 196, 197, 198]
var noFruit14 = [29, 49, 69, 89, 101, 102, 103, 104, 105, 106, 107, 111, 112, 113, 114, 115, 116, 117, 118, 129, 149, 169, 189]

function Start()
{
	block = 1;
	Game = true;
	if (Countdown && !fnord)
	{
		reset();
		for (i = 0; i < eval('C' + Countdown).length; i++)
		{
			Zelle = 'Zelle' + eval('C' + Countdown)[i];
			document.getElementById(Zelle).style.backgroundColor = 'black';
			$('#' + Zelle + ' i:first-child').remove();
		}
		Countdown--;
		setTimeout("Start()", 1000);
	}
	else if (!fnord)
	{
		$('#snakescore').slideDown(400);
		$('#snakelevel').slideDown(400);

		reset();
		Fruechte = 10;
		$('#snakescore').text(Fruechte);
		$('#snakelevel').text(Level + 1);
		while (Snake[0]) Snake.pop();
		Snake.push(198, 199, 200, 201, 202, 203, 204, 205, 206);
		Kopf = 206;
		block = 0;
		zuEnde = 0;
		Richtung = '+1';
		Happen();
		Hindernisse();
		Verlauf();
	}
}

function Pause()
{
	fnord = !fnord;
	Countdown = 3;
	Start();
}

function Happen()
{
	Leckerli = Math.floor((Math.random() * 1000) % 220);
	for (i = 0; i < Snake.length; i++) if (Leckerli == Snake[i]) { Happen(); return };
	for (i = 0; i < eval('Wall' + Level).length; i++) if (Leckerli == eval('Wall' + Level)[i]) { Happen(); return; }
	for (i = 0; i < eval('noFruit' + Level).length; i++) if (Leckerli == eval('noFruit' + Level)[i]) { Happen(); return; }
	$('#Zelle' + Leckerli).append('<i class="' + snakeFrucht() + ' icon-green"></i>');
}

function Hindernisse()
{
	for (i = 0; i < eval('Wall' + Level).length; i++) {
		document.getElementById('Zelle' + eval('Wall' + Level)[i]).style.backgroundColor = '#444444';
		$('#Zelle' + eval('Wall' + Level)[i] + ' i:first-child').remove();
	}
}

function Verlauf()
{
	Kopf = eval(Kopf + Richtung);
	if (Kopf < 0 || Kopf > 219 || (!(Kopf%20) && Richtung == '+1') || (!((Kopf+1)%20) && Richtung == '-1')) zuEnde = 1;
	for (i = 1; i < Snake.length; i++) if (Kopf == Snake[i]) zuEnde = 1;
	for (i = 0; i < eval('Wall' + Level).length; i++) if (Kopf == eval('Wall' + Level)[i]) zuEnde = 1;

	if (!zuEnde)
	{
		if (Kopf != Leckerli)
		{
			blah = Snake.shift();
			Snake.push(Kopf);
		}
		else
		{
			Fruechte--;
			$('#snakescore').text(Fruechte);
			Snake.push(Kopf);
			Happen();
		}
		Schlange_malen();
		if (Fruechte)
		{
			block = 0;
			setTimeout("Verlauf()",300);
		}
		else
		{
			Smiley_malen();
			block = 1;
			Level++;
			if (Level == 15) setTimeout("Jubel(1)",500);
			else
			{
				Countdown = 3;
				if (!fnord) setTimeout("Start()",2000);
			}
		}
	}
	else
	{
		setTimeout("Frowny_malen()",500);
		Countdown = 3;
	}
}

function Jubel(x)
{
	reset();
	if (x) 
	{
		for (i = 0; i < Jubel1.length; i++)
	{
			Zelle = 'Zelle' + Jubel1[i];
			document.getElementById(Zelle).style.backgroundColor = 'black';
			$('#' + Zelle + ' i:first-child').remove();
		}
	} else {
		for (i = 0; i < Jubel2.length; i++)
		{
			Zelle = 'Zelle' + Jubel2[i];
			document.getElementById(Zelle).style.backgroundColor = 'black';
			$('#' + Zelle + ' i:first-child').remove();
		}
	}
	x++;
	x %= 2;
	setTimeout("Jubel("+x+")",150);
}

function Smiley_malen()
{
	reset();
	for (i = 0; i < Smiley.length; i++)
	{
		Zelle = 'Zelle' + Smiley[i];
		document.getElementById(Zelle).style.backgroundColor = '#0c5a0c';
		$('#' + Zelle + ' i:first-child').remove();
	}
	zuEnde = 0;
}

function Frowny_malen()
{
	reset();
	for (i = 0; i < Frowny.length; i++)
	{
		Zelle = 'Zelle' + Frowny[i];
		document.getElementById(Zelle).style.backgroundColor = '#aa0000';
		$('#' + Zelle + ' i:first-child').remove();
	}

	setTimeout("snakeEnd()", 700);
}

function Schlange_malen()
{
	Zelle = 'Zelle' + Snake[0];
	document.getElementById(Zelle).style.backgroundColor = '#f5f5f5';
	$('#' + Zelle + ' i:first-child').remove();
	for (i = 1; i < Snake.length; i++)
	{
		Zelle = 'Zelle' + Snake[i];
		document.getElementById(Zelle).style.backgroundColor = 'black';
		$('#' + Zelle + ' i:first-child').remove();
	}
}

function reset()
{
	for (i = 0; i < 220; i++)
	{
		Zelle = 'Zelle' + i;
		document.getElementById(Zelle).style.backgroundColor = '#f5f5f5';
		$('#' + Zelle + ' i:first-child').remove();
	}
}
