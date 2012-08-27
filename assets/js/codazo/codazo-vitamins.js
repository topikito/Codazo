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
				code = code.replace(/\t/gi, _getSpaces());
			}
			_prettyCode.html(code);
			prettyPrint();
			_prettyContainer.show();
		};

		var _captureTab = function(event) {
			var keyCode = event.keyCode || event.which,
				charToInsert = (_convertTabs.is(':checked')) ? _getSpaces() : '\t';
			if (keyCode == 9)
			{
				event.preventDefault();
				if (document.selection)
				{
					// IE suport
					this.focus();
					var sel = document.selection.createRange();
					sel.text = charToInsert;
					this.focus();
				}
				else if (this.selectionStart || this.selectionStart == '0')
				{
					// FF/chrome support
					var startPos = this.selectionStart
						endPos = this.selectionEnd;
						scrollTop = this.scrollTop;
					this.value = this.value.substring(0, startPos) + charToInsert + this.value.substring(endPos, this.value.length);
					this.focus();
					this.selectionStart = startPos + charToInsert.length;
					this.selectionEnd = startPos + charToInsert.length;
					this.scrollTop = scrollTop;
				}
				else
				{
					this.value += charToInsert;
					this.focus();
				}
			}
		};

		var _getSpaces = function() {
			var tab2SpacesValue = _tab2Spaces.val();
				spacesString = '';
			if (tab2SpacesValue > 10)
			{
				tab2SpacesValue = 10;
				_tab2Spaces.val(10);
			}
			for (var i = 0; i < tab2SpacesValue; i++)
			{
				spacesString += ' ';
			}
			return spacesString;
		};

		var init = function(config) {
			_language.typeahead({
				source: config.availableLanguages
			});
			_convertTabs.change(_refreshPreview);
			_tab2Spaces.keyup(_refreshPreview);
			_codeTextArea.keyup(_refreshPreview).keydown(_captureTab);
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