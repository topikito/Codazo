$(document).ready(function() {

	$(function() {
		var availableTags = ['ActionScript','AppleScript','Asp','BASIC','C','C++','Clojure','COBOL','ColdFusion','Erlang','Fortran','Groovy','Haskell','Java','JavaScript','Lisp','Perl','PHP','Python','Ruby','Scala','Scheme'];
		$( '#lang' ).autocomplete({
			source: availableTags
		});
	});

	$(function() {
		$('#code').keyup(function() {
			alert('asdad');
			var code = $(this).html();
			$('#pretty-code').show().html(code);
		});
	});


});