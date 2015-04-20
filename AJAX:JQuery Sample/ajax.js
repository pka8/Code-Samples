//keep track of an ajax request as a global variable so it can be aborted if necessary
var requestNameCheck;
$(document).ready(function() {

	//Checks databse to see if name & school being typed into text input already exists and
	//disable the submit button if typed name & school already exist.
	$(document).on("keyup", "#goblet-first-name, #goblet-last-name, #goblet-school", function() {
		//Make sure the "Enter" button is disabled
		$("#goblet-submit").attr("disabled", "disabled");
		$("#goblet-msg").text("");

		//Cancel any existing ajax requests
		if (typeof requestNameCheck !== 'undefined') {
			requestNameCheck.abort();
		}
		$("#goblet-submit-msg").text("");
		var firstName = $("#goblet-first-name").val();
		var lastName = $("#goblet-last-name").val();
		var school = $("#goblet-school").val();

		if (firstName && lastName && school) {
			var wizardInfo = {requestType: 'checkName', firstName: firstName, lastName: lastName, school: school};

			requestNameCheck = $.ajax({
				url: 'ajax/ajax.php',
				method: 'POST',
				data: wizardInfo,
				dataType: 'text',
				error: function(error) {
					console.log(error);
				}
			});
			
			requestNameCheck.success( function(data) { //get data from ajax.php

				if (data === 'NoDuplicates')
					$("#goblet-submit").removeAttr("disabled");
				else
					$("#goblet-submit").attr("disabled", "disabled");
			});

		}
		

	});

	//Posts name, last name, and school to the database
	$(document).on("click", "#goblet-submit", function() {
		var firstName = $("#goblet-first-name").val();
		var lastName = $("#goblet-last-name").val();
		var school = $("#goblet-school").val();
		
		//Clear the submission form and prevent clicking "Enter" again
		$("#goblet-first-name").val("");
		$("#goblet-last-name").val("");
		$("#goblet-school").val("");
		$("#goblet-submit").attr("disabled", "disabled");
		
		var wizardInfo = {requestType: 'submitName', firstName: firstName, lastName: lastName, school: school};
		
		
		requestSubmitName = $.ajax({
				url: 'ajax/ajax.php',
				method: 'POST',
				data: wizardInfo,
				dataType: 'text',
				error: function(error) {
					console.log(error);
				}
			});
		
		requestSubmitName.success( function(data) {
			if (data == 'success') {
				$("#goblet-msg").text("Successful addition.");
			}
			else {
				$("#goblet-msg").text(data);
			}
		});
	});

	
	//Gets and updates a name from the database
	$(document).on("click", "#goblet-choose", function() {
		var wizardInfo = {requestType:'chooseName'};
	
		requestChooseName = $.ajax({
				url: 'ajax/ajax.php',
				method: 'GET',
				data: wizardInfo,
				dataType: 'text',
				error: function(error) {
					console.log(error);
				}
			});
	
		requestChooseName.success( function(data) {
			var string = data;
			$("#goblet-msg").text(string);
		});
		
	});



	//Uses Spotify API to play music (See https://developer.spotify.com/web-api/search-item/ for more details)
	$(document).on("click", "#get-music", function() {

		var artistName = "chiddy+bang";
		var url = "https://api.spotify.com/v1/search?query=" + artistName + "&limit=1&type=artist";
		
				
		$.ajax({
			
			url: url,
			method: 'GET',
			dataType: 'JSON', 
			success: function(data) {

				
				var artist = data.artists.items[0];
				var artistID = artist.id;
				var preview_url = artist.href;
				var top_tracks_url = preview_url + "/top-tracks?country=US";

				console.log(data);
				console.log(artistID);
				console.log(preview_url);

				$.ajax({
					
					//Get artists top tracks
   					url: top_tracks_url,
   					method: "GET",
   					dataType: "JSON",
   					success: function(data) {
						console.log(data);
						href = data.tracks[0].preview_url;
						$("#music-frame").attr('src', href);
   					}
   				});

			},
			error: function(error) {
				console.log(error);
			}

		});
		
	});

});





