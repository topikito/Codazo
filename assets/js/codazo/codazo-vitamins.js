(function($) {

	var Application = (function(config)
	{
		//Form elements
		var _codeTextArea		= $('#form_code');
		var _language			= $('#form_lang');
		var _firstLine			= $('#form_line');

		//Other DOM elements
		var _prettyCode			= $('#pretty-code');
		var _prettyContainer	= $('#preview-code');
		var _viewPrettyContainer	= $('#view-pretty-code');

		var _alertUrl = $(".alert");
		var _alertUrlLink = $('a#copy-link');

		var _htmlEntities = function(str) {
			return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}

		var _refreshPreview = function() {

			if( _prettyfy( _codeTextArea.val(), _prettyCode ) )
			{
				_prettyContainer.show()
			}
			else
			{
				_prettyContainer.hide()
			}
		};

		var _prettyfy = function( code, target ) {
			var	formatted = []
			,	language = _language && _language.length && _language.val() || null
			,	line = Math.max( 1, parseInt( _firstLine && _firstLine.val() || 1, 10 ) )
			,	$target = $(target)

			$target.empty()

			code = _htmlEntities( code ).replace(/\r\n?/g, "\n" )
			if (!code) return false

			$target.html( code )
			$target.prop( 'className', $target.prop('className').replace( /\blinenums(\:[0-9]*)?\b|\blang-[a-z]*\b/gi, '' ).replace(/\s+/g, ' ') )
			if (language) $target.addClass( 'lang-'+language.toLowerCase() )
			if (line > 1) $target.addClass( 'linenums:'+line )
			else $target.addClass('linenums')

			// TODO: specify target to prettyPrint
			prettyPrint();

			return true
		}

		var _captureTab = function(event) {
			var keyCode = event.keyCode || event.which,
				charToInsert = '\t';
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
					,	endPos = this.selectionEnd
					,	scrollTop = this.scrollTop

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

		var init = function(config) {
			_language.typeahead({
				source: config.availableLanguages
			});


			_codeTextArea.on({
				'keyup'		: _refreshPreview,
				'change'	: _refreshPreview,
				'blur'		: _refreshPreview,
				'keydown'	: _captureTab
			})

			_firstLine.on({
				'blur'		: _refreshPreview
			})

			_language.on({
				'blur'		: _refreshPreview
			})

			if (_viewPrettyContainer && _viewPrettyContainer.length)
			{
				_prettyfy( _viewPrettyContainer.html(), _viewPrettyContainer )
			}

			_alertUrl.alert();
			_alertUrlLink.zclip({
				path:'./assets/js/zClip/ZeroClipboard.swf',
				copy:window.location.href,
				afterCopy:function(){
					_alert.fadeIn();
					_alertLink.addClass("disabled").html('Yet Copied!');
				}
			});
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

})( window.jQuery )