$(document).ready(function(){

	// Font color change
	$('input:radio').click(function() {
		var id = $(this).attr('id');
		$("body").css("color", id);
		$("#text").css("color", id);
	});

	// Text Display
	$("input[name = 'hideAll']").click(function() {
		$('p').hide();
	});
	
	$("input[name = 'showAll']").click(function() {
		$('p').show();
	});

	// Font size change
	$("input[name = 'font']").change(function() {
		var input_size = $("input[name = 'font']").val();
		if (input_size < 8 || input_size > 80) {
			
			alert("Please enter a font between 8px and 80px.");
			("#sizeWarning").text('Please enter a font between 8px and 80px.');
			("#sizeWarning").height(100);
		}
		else {
			input_size = input_size + "px";
			$("#text").css("font-size", input_size);
		}
	});


	// Image Altering
	$("input[name = 'pokemonReturn']").click(function() {
		$("img").attr("src", "../img/pokeball.jpg");
	});
	
	$("input[name = 'pokemonGo']").click(function() {
		$('img').each(
			function(){
				var parentID = $(this).closest('div').attr('id');
				$(this).attr("src", "../img/" + parentID + ".jpg");
			});
	});
	
	
	// search functionality
	$("#search").bind('keyup', function(){

		$("#text").find("p").each(function(){
			
			var currentString = $(this).html();

			//Remove existing highlights
			currentString = replaceAll(currentString, "<span class=\"matched\">","");
			currentString = replaceAll(currentString, "</span>","");

			// add in new highlights
			currentString = replaceAll(currentString, $("#search").val(), "<span class=\"matched\">$&</span>");

			$(this).html(currentString);
		});
	});

	//replace curent text with new text
	$('#replace').on('click', function(){
		$('p').each(function(){
			
			//retrieve current html
			var currentString = $(this).html();
			
			var patt = new RegExp("<\/?[^<>]*>");
			
			var old = $("input[id = 'original']").val();
			
			//erase anything within tags
			old = old.replace(patt, '');
			$("#original").val(old);
			old = old.trim();
			
			var replace = $("input[id = 'newtext']").val();
			replace = replace.replace(patt, '');
			replace = replace.trim();
			$("#newtext").val(replace);
			
			if (old == "" || old == null || replace == "" || replace == null) {
				alert("The 'old' and 'replace' fields must not be empty.");
			}
			
			else {
			currentString = replaceAll(currentString, old, replace);
			
			$(this).html(currentString);
			}
		});
			
	});
	
	
	$("input[name = 'savebutton']").on('click', function() {
		var alltext = $("#text").text();
		var textarray = alltext.split("\n");
		textarray.splice(0,2);
		alltext = textarray.join("\n");
		alert(alltext);
		$("input[name = 'hiddentext']").text(alltext);
	
	});
		

/* Replaces all instances of "replace" with "with_this" in the string "txt" using
   regular expressions */
function replaceAll(txt, replace, with_this) {
	return txt.replace(new RegExp(replace, 'g'),with_this);
}

// taken from Yahoo UI, checks if o is a finite number
function isNumber(o) {
    return typeof o === 'number' && isFinite(o);
}