$(document).ready(function() {

	//Form elements
	var codeTextArea	= $('#form_code');
	var language		= $('#form_lang');
	var convertTabs		= $('#form_convert_tabs')
	var tab2Spaces		= $('#form_tab2spaces');
	//Other DOM elements
	var prettyCode		= $('#pretty-code');
	var prettyContainer = $('#preview-code');

	$(function() {
		var availableTags = [
		'ActionScript','AppleScript','Asp','BASIC','C','C++','Clojure','COBOL',
		'ColdFusion','Erlang','Fortran','Groovy','Haskell','Java','JavaScript',
		'Lisp','Perl','PHP','Python','Ruby','Scala','Scheme'
		];
		language.typeahead({
			source: availableTags
		});
	});

	var refreshPreview = function() {
		var code = codeTextArea.val();
		if (convertTabs.is(':checked'))
		{
			var tab2SpacesValue = tab2Spaces.val();
			var spaces = '';
			for (var i = 0; i < tab2SpacesValue; i++)
			{
				spaces += ' ';
			}
			code = code.replace(/\t/gi, spaces);
		}
		console.log(code);
		prettyCode.html(code);
		prettyPrint();
		prettyContainer.show();
	};

	$(function() {
		convertTabs.change(refreshPreview);
		tab2Spaces.keyup(refreshPreview);
		codeTextArea.keyup(refreshPreview);
	});

});