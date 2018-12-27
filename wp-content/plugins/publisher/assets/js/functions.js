(function () {
	'use strict';

	let form_changed = false;

	function cl( data, clear ) {
		if ( true === clear ) {
			console.clear();
		}
		console.log( data );
	}

	/**
	 * Serialize form to an object including empty fields.
	 * defineProperty - to avoid jQuery conflict.
	 */
	Object.defineProperty( Object.prototype, 'serializeObject', {
		value : function ( format ) {
			let inputs = this.querySelectorAll( '[name]' );
			let obj    = {};
			let arr    = [];
			let s      = [];
			let a      = [];
			let out;
			let val;

			for ( let i in inputs ) {
				if ( inputs.hasOwnProperty( i ) ) {
					let that = inputs[ i ];

					val = that.value ? that.value : '';
					if ( undefined !== that.getAttribute( 'type' ) && 'checkbox' === that.getAttribute( 'type' ) ) {

						if ( that.checked ) {
							arr.push( { name : that.getAttribute( 'name' ), value : val } );
						} else {
							arr.push( { name : that.getAttribute( 'name' ), value : '' } );
						}
					} else {
						if ( undefined !== that.getAttribute( 'multiple' ) ) {
							if ( null === val ) {
								arr.push( { name : that.getAttribute( 'name' ), value : '' } );
							} else {
								arr.push( { name : that.getAttribute( 'name' ), value : val } );
							}
						} else {
							arr.push( { name : that.getAttribute( 'name' ), value : val } );
						}
					}
				}
			}

			// turn to associative array
			for ( let i in arr ) {
				if ( arr.hasOwnProperty( i ) ) {

					// if we haven't an object item with that name
					if ( undefined === obj[ arr[ i ].name ] ) {

						// set simple value at the first time for that name
						obj[ arr[ i ].name ] = arr[ i ].value;
					} else {

						// if it is not an array yet
						if ( !Array.isArray( obj[ arr[ i ].name ] ) ) {

							// make an array from simple value
							obj[ arr[ i ].name ] = [ obj[ arr[ i ].name ] ];
						}
						obj[ arr[ i ].name ].push( arr[ i ].value );
					}
				}
			}

			if ( 'string' === format || 'attributes' === format ) {

				// build query
				for ( let key in obj ) {
					if ( obj.hasOwnProperty( key ) ) {
						let value = obj[ key ];
						if ( true === Array.isArray( value ) ) {
							value = value.join( ',' );
						}
						s.push( key + '=' + value );
						a.push( key + '="' + value + '"' );
					}
				}
			}

			// choose output format
			switch ( format ) {
				case 'string':
					out = encodeURI( s.join( '&' ) );
					break;
				case 'attributes':
					out = a.join( ' ' );
					break;
				case 'array':
					out = arr;
					break;
				default:
					out = obj;
			}

			return out;
		},
		enumerable : false
	} );


	function on( e, selector, func ) {
		e = e.split( ' ' );
		//console.log( e );
		for ( let i = 0, count = e.length; i < count; i++ ) {
			document.addEventListener( e[ i ], function ( event ) {

				// if cart button clicked
				if ( event.target.closest( selector ) !== null ) {

					func( event, selector );
				}
			} );
		}
	}


	/**
	 * Function, that put the data to template block, and return complete HTML.
	 *
	 * @param str
	 * @param data
	 * @returns {Function}
	 */
	function tmpl( str, data ) {
		// Figure out if we're getting a template, or if we need to
		// load the template - and be sure to cache the result.
		let fn = !/\W/.test( str ) ?
			cache[ str ] = cache[ str ] ||
				tmpl( document.getElementById( str ).innerHTML ) :

			// Generate a reusable function that will serve as a template
			// generator (and which will be cached).
			new Function( "obj",
				"var p=[],print=function(){p.push.apply(p,arguments);};" +

				// Introduce the data as local variables using with(){}
				"with(obj){p.push('" +

				// Convert the template into pure JavaScript
				str
				//.toString()
					.replace( /[\r\t\n]/g, " " )
					.split( "<%" ).join( "\t" )
					.replace( /((^|%>)[^\t]*)'/g, "$1\r" )
					.replace( /\t=(.*?)%>/g, "',$1,'" )
					.split( "\t" ).join( "');" )
					.split( "%>" ).join( "p.push('" )
					.split( "\r" ).join( "\\'" )
				+ "');}return p.join('');" );
		// Provide some basic currying to the user
		return data ? fn( data ) : fn;
	}

	/**
	 * Get request.
	 *
	 * @param options
	 * @returns {Promise<string>}
	 */
	function ajax( options ) {
		return new Promise( function ( resolve, reject ) {
			let xhr    = new XMLHttpRequest();
			let params = options.data;
			let url    = options.url;
			// We'll need to stringify if we've been given an object
			// If we have a string, this is skipped.
			if ( params && 'object' === typeof params ) {
				params = Object.keys( params ).map( function ( key ) {
					return encodeURIComponent( key ) + '=' + encodeURIComponent( params[ key ] );
				} ).join( '&' );
			} else {
				params = '';
			}

			if ( params && 'POST' !== options.method ) {
				url = options.url + '?' + params;
			}

			xhr.open( options.method, url );
			xhr.onload  = function () {
				if ( this.status >= 200 && this.status < 300 ) {
					resolve( xhr.response );
				} else {
					reject( {
						status : this.status,
						statusText : xhr.statusText
					} );
				}
			};
			xhr.onerror = function () {
				reject( {
					status : this.status,
					statusText : xhr.statusText
				} );
			};
			if ( 'POST' === options.method ) {
				xhr.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
			}
			if ( options.headers ) {
				Object.keys( options.headers ).forEach( function ( key ) {
					xhr.setRequestHeader( key, options.headers[ key ] );
				} );
			}

			xhr.send( params );
		} );
	}

	/* --------------------------- */

	function htmlToElement( html ) {
		let div       = document.createElement( 'div' );
		div.innerHTML = html.trim();

		return div.firstChild;
	}

	/**
	 * список состояний элемента
	 *
	 * @param element
	 * @param action
	 */
	function statesList( element, action ) {
		switch ( action ) {
			case 'hide':
				element.classList.add( 'hidden-block' );
				break;
			case 'show':
				element.classList.remove( 'hidden-block' );
				break;
			case 'active':
				element.classList.add( 'active' );
				break;
			case 'deactive':
				element.classList.remove( 'active' );
				break;
			case 'enable':
				element.classList.remove( 'disabled' );
				element.removeAttribute( 'disabled' );
				break;
			case 'disable':
				element.classList.add( 'disabled' );
				element.setAttribute( 'disabled', 'disabled' );
				break;
			case 'wait':
				// режим ожидания - элемент блокируется и на нем появляется прелоудер
				element.classList.add( 'disabled' );
				element.setAttribute( 'disabled', 'disabled' );
				element.classList.add( 'preloader' );
				break;
			case 'unwait':
				// разблокировка
				element.classList.remove( 'disabled' );
				element.removeAttribute( 'disabled' );
				element.classList.remove( 'preloader' );
				break;
		}
	}

	/**
	 * Функция установки состояния для указанного элемента
	 *
	 * @param element
	 * @param action
	 */
	function setState( element, action ) {

		if ( 'string' === typeof element ) {
			let elements = document.querySelectorAll( element );
			elements.forEach( function ( val, i, element ) {
				statesList( element[ i ], action );
			} );
		} else {
			statesList( element, action );
		}
	}

	/**
	 * Функция для установки url'а в строку адреса браузера
	 *
	 * @param data
	 * @param url
	 */
	function set_page_url( data, url ) {
		document.title = data.title;
		window.history.pushState( { 'html' : data.html, 'pageTitle' : data.title }, '', url );
	}

	/**
	 * Функция вывода временного сообщения в указанное место
	 *
	 * @param message
	 * @param timeout
	 * @param selector
	 */
	function add_form_message( message, timeout, selector ) {
		//console.log( 'add_form_message' );
		if ( undefined === message ) {
			return;
		}
		let parent;

		if ( 'object' !== typeof(selector) ) {
			if ( undefined === selector ) {
				selector = '.js-form-info';
			}
			parent = document.querySelector( selector );
		} else {
			parent = selector;
		}

		parent.appendChild( htmlToElement( '<p>' + message + '</p>' ) );
		if ( undefined === timeout ) {
			timeout = 5000;
		} else {
			timeout *= 1000;
		}
		setTimeout( function () {
			//let parent  = document.querySelector( selector );
			let element = parent.querySelector( 'p' );
			element.parentNode.removeChild( element );
		}, timeout );
	}


	function formSubmit( event ) {
		event.preventDefault();

		let form    = event.target;
		let submit  = form.querySelector( '[type=submit]' );
		let infobox = form.querySelector( '.js-form-info' );
//cl(infobox);
		setState( submit, 'wait' );

		let data = form.serializeObject();
		data.js_disabled = 0;
		cl( data );
		ajax( {
			method : 'POST',
			url : publisher.ajax_url,
			data : data,
		} ).then( function ( result ) {
			let data = JSON.parse( result );
			cl( data );
			if ( true === data.success ) {
				add_form_message( 'Данные сохранены', 10, infobox );
			} else {

				// если есть ошибки
				if ( data.errors.length > 0 ) {

					// информация о них выводится
					data.errors.forEach( function ( error ) {
						add_form_message( error, 30, infobox );
					} );
				}
			}
			setState( submit, 'unwait' );
		} ).catch( function ( err ) {
			setState( submit, 'unwait' );
			cl( err );
			// если есть ошибки, информация о них выводится
			if ( err.hasOwnProperty( 'statusText' ) ) {
				add_form_message( 'Ошибка ' + err.status + ': ' + err.statusText, 30, infobox );
			}
		} );
	}

	/**
	 * Сохранение настроект букмекеров
	 */
	on( 'submit', '.js-publisher', formSubmit );



}());


