var Application = (function(config)
	{
		//Form elements
		var _codeTextArea		= $('#form_code');
		var _language			= $('#form_lang');
		var _convertTabs		= $('#form_convert_tabs')
		var _tab2Spaces			= $('#form_tab2spaces');
		//Other DOM elements
		var _prettyCode			= $('#pretty-code');
		var _prettyContainer	= $('#preview-code');
		var _viewPrettyContainer	= $('#view-preview-code');

		var _htmlEntities = function(str) {
			return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}

		var _refreshPreview = function() {
			var code = _htmlEntities(_codeTextArea.val());
			if (_convertTabs.is(':checked'))
			{
				var tab2SpacesValue = _tab2Spaces.val();
				if (tab2SpacesValue > 10)
				{
					tab2SpacesValue = 10;
					_tab2Spaces.val(10);
				}
				var spaces = '';
				for (var i = 0; i < tab2SpacesValue; i++)
				{
					spaces += ' ';
				}
				code = code.replace(/\t/gi, spaces);
			}
			_prettyCode.html(code);
			prettyPrint();
			_prettyContainer.show();
		};

		var init = function(config) {
			_language.typeahead({
				source: config.availableLanguages
			});
			_convertTabs.change(_refreshPreview);
			_tab2Spaces.keyup(_refreshPreview);
			_codeTextArea.keyup(_refreshPreview);
			if (_viewPrettyContainer)
			{
				prettyPrint();
			}
		};

		return {
			init: init
		};

	})(config);

var config = {
	availableLanguages :
	[
	'ActionScript','AppleScript','Asp','BASIC','C','C++','Clojure','COBOL',
	'ColdFusion','Erlang','Fortran','Groovy','Haskell','Java','JavaScript',
	'Lisp','Perl','PHP','Python','Ruby','Scala','Scheme'
	]
};

$(document).ready(function() {
	Application.init(config);
});