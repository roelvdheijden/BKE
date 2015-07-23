var active = false; // variable to hold state of application request

/* function to start a new game */
function newGame() {
	active = true; // set active state, closed to user input

	// call game application to initialize
	window.jsonCall( "app.php/game/new/");
}

/* function to call when a field is clicked */
function fieldClicked(field) {
	// ignore user input when still in an active (processing) state
	if (active == false) {
		active = true; // set active state, closed to user input
		$("div#"+field.id).text('..'); // temp field text while calling json
		
		// call game application and pass fieldID
		window.jsonCall( "app.php/game/play/"+field.id );
	}
}

/* JSON function to call game application and handle results */
function jsonCall(urlRequest) {
	// change cursor to waiting on fields and body
	$('body').css({'cursor':'wait'});
	$('.field').css({'cursor':'wait'});
	
	// call JSON function
	$.getJSON( urlRequest )
		.always( function() {
			// always return to inactive (processing) state when rdy, open to user input again
			active = false;
			// return cursor to default/pointer on fields and body
			$('body').css({'cursor' : 'default'});
			$('.field:not(.inactive, .text-danger, .text-primary)').css({'cursor':'pointer'});
		})
		.done( function(data) {
			// set gameInfo (players turn, win, draw) and toggle user interaction
			processGameStatus(data);
			
			// check if data contains a 'fields' array and loop through fields
			if (Array.isArray(data.fields)) {
				$.each( data.fields, function(f, field) {
					setField(field); // set field content and interaction
				});
			}
		});
}

/* helper functions to handle json callback data */
// set field content and interaction
function setField(field)
{
	// set field content
	$("div#field_"+field.id).html(field.content);
	
	// add approriorate class and disable field interactions
	if (field.content == " ")
		$("div#field_"+field.id).removeClass("inactive text-danger text-primary");
	if (field.content == "X")
		$("div#field_"+field.id).off('click').addClass("text-danger").css({'cursor':'default'});
	if (field.content == "O")
		$("div#field_"+field.id).off('click').addClass("text-primary").css({'cursor':'default'});
	// inactive fields
	if (field.inactive == true)
		$("div#field_"+field.id).off('click').addClass("inactive").css({'cursor':'default'});
	
}

// process the gameStatus
function processGameStatus(data)
{
	// set game info
	setUserInteraction(data.gameStatus);
	
	switch(data.gameStatus) {
		// running game
		case 1 : 
			setGameInfo( data.player+" Turn", (data.player == "Player 1") ? 'alert-danger' : 'alert-info', 1);
			break;
		
		// game won	
		case 2 : 
			setGameInfo( data.player + " Wins !!", (data.player == "Player 1") ? 'alert-danger' : 'alert-info', 3);
			// blink winning fields
			for(i=0;i<3;i++) {  
				$("#field_"+data.winLine[0]).fadeToggle("fast").fadeToggle("fast");
				$("#field_"+data.winLine[1]).fadeToggle("fast").fadeToggle("fast");
				$("#field_"+data.winLine[2]).fadeToggle("fast").fadeToggle("fast");
			}
			break;
		
		// game draw	
		case 3 : 
			setGameInfo( "Draw !!",  'alert-warning', 2);
			break;	
	}
}

// update #gameinfo html and class
function setGameInfo(html, css, blinkfield)
{
	$("#gameinfo").removeClass('alert-danger alert-info alert-warning').html(html).addClass(css);
	for(i=0;i<blinkfield;i++) {  
		$("#gameinfo").fadeToggle("fast").fadeToggle("fast");
	}
}

// enable/disable onclick and start button
function setUserInteraction(gameStatus) {
	if (gameStatus == 1) {
		// set onClick function to all fields
		$('.field').css({'cursor':'pointer'}).on('click', function(event) { window.fieldClicked(event.target) });
		$("#newgame").hide(); // hide start button
	} else {	
		// disable click function on all fields
		$(".field").css({'cursor':'default'}).off('click');
		$("#newgame").show(); // show start button
	}
}