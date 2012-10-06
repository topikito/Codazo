(function($) {

	var config = {
		availableLanguages :
		{
		/*	extension	: [ labels ] */
			'bas'		: [ 'BASIC' ]
		,	'sh'		: [ 'Bash Script' ]
		,	'java'		: [ 'Java' ]
		,	'xml'		: [ 'HTML', 'XML', 'XHMTL', 'XSL' ]
		,	'c'			: [ 'C', 'C++' ]
		,	'cs'		: [ 'Visual C#' ]
		,	'css'		: [ 'CSS' ]
		,	'py'		: [ 'Python' ]
		,	'perl'		: [ 'Perl' ]
		,	'rb'		: [ 'Ruby' ]
		,	'asp'		: [ 'ASP' ]
		,	'coffee'	: [ 'Coffee script' ]
		,	'js'		: [ 'ActionScript', 'Javascript', 'JSONP' ]
		,	'json'		: [ 'JSON' ]
		,	'php'		: [ 'PHP' ]
		,	'clj'		: [ 'Clojure' ]
		,	'go'		: [ 'Go' ]
		,	'hs'		: [ 'Haskell' ]
		,	'lua'		: [ 'Lua' ]
		,	'ml'		: [ 'OCAML', 'SML', 'F#' ]
		,	'nemerle'	: [ 'Nemerle' ]
		,	'proto'		: [ 'Protocol Buffers' ]
		,	'scala'		: [ 'Scala' ]
		,	'sql'		: [ 'SQL', 'MySQL' ]
		,	'tex'		: [ 'TeX', 'LaTeX' ]
		,	'vhdl'		: [ 'VHDL' ]
		,	'vb'		: [ 'Visual Basic' ]
		,	'wiki.meta'	: [ 'WikiText' ]
		,	'xq'		: [ 'XQuery' ]
		,	'yml'		: [ 'YAML' ]
		}
	};

	var Application = (function( config )
	{
		//Form elements
		var _codeTextArea		= $('#form_code');
		var _language			= $('#form_lang');
		var _languageLabel		= $('#form_langLabel');
		var _firstLine			= $('#form_line');

		//Other DOM elements
		var _prettyCode			= $('#pretty-code');
		var _prettyContainer	= $('#preview-code');
		var _viewPrettyContainer	= $('#view-pretty-code');

		var _alertUrlLink = $('a#copy-link');


		var _eventDelayer = function( method, delay ) {
			var timer

			return function( event ) {
				if (timer) clearTimeout( timer )
				timer = setTimeout( $.proxy( method, this ), delay )
			}
		}

		var _htmlEntities = function(str) {
			return $("<div></div>").text( str ).html()
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

		var _prettyfy = function( code, target, preformatted ) {
			var	formatted = []
			,	language = ( _language.length && _language.val() ) || null
			,	line = Math.max( 1, parseInt( _firstLine && _firstLine.val() || 1, 10 ) )
			,	$target = $(target)

			$target.empty()

			if (!preformatted) code = _htmlEntities( code ).replace(/\r\n?/g, "\n" )
			if (!code) return false

			$target.html( code )

			// Remove any lang-??? or linenum:??? class
			$target.prop( 'className', $target.prop('className').replace( /\blinenums(\:[0-9]*)?\b|\blang-[a-z]*\b/gi, '' ).replace(/\s+/g, ' ') )

			// Add lang
			if (language) $target.addClass( 'lang-'+language.toLowerCase() )

			// Add linenum
			if (line > 1) $target.addClass( 'linenums:'+line )
			else $target.addClass('linenums')

			// TODO: specify target to prettyPrint
			prettyPrint();

			return true
		}

		var _updateLanguage = function(event) {

			var extension = ''
			,	label = _languageLabel.val()
			,	i
			,	j

			for (i in config.availableLanguages)
			{
				// IE support
				if (!Array.prototype.indexOf) {
					for (j=0; j<config.availableLanguages[i].length; j++)
					{
						if (config.availableLanguages[i][j] === extension) {
							extension = i
							break;
						}
						if (extension) break;
					}
				} else {
					if ( config.availableLanguages[i].indexOf( label ) > -1 )
					{
						extension = i
						break
					}
				}
			}

			_language.val( extension )

			_refreshPreview()
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

			var langList = []
			,	i

			for (i in config.availableLanguages)
			{
				langList = langList.concat( config.availableLanguages[i] )
			}

			_languageLabel.typeahead({
				source: langList
			});


			_codeTextArea.on({
				'keyup'		: _eventDelayer( _refreshPreview, 100 ),
				'change'	: _refreshPreview,
				'blur'		: _refreshPreview,
				'keydown'	: _captureTab
			})

			_firstLine.on({
				'keyup'		:  _eventDelayer( _refreshPreview, 100 ),
				'blur'		: _refreshPreview
			})

			_languageLabel.on({
				'keyup'		:  _eventDelayer( _refreshPreview, 100 ),
				'change'	: _updateLanguage
			})

			if (_viewPrettyContainer && _viewPrettyContainer.length)
			{
				_prettyfy( _viewPrettyContainer.html(), _viewPrettyContainer, true )
			}

			_alertUrlLink.zclip({
				path:'./assets/js/zClip/ZeroClipboard.swf',
				copy: window.location.href,
				afterCopy:function(){
					_alertUrlLink.addClass("disabled").html($(this).data('copied'));
				}
			});
		};

		return {
			init: init
		};

	})( config );

	$(document).ready(function() {
		Application.init(config);
	});

})( window.jQuery )