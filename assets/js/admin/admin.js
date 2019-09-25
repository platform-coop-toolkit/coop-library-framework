const { __, sprintf } = wp.i18n;
const doi = require( 'doi-regex' );
const ISBN = require( 'simple-isbn' ).isbn;
const issn = require( 'issn' );
const schemify = require( 'url-schemify' );

jQuery( document ).ready( function( $ ) {
	const $form = $( '#post' );
	const $urlFields = $( '.cmb2-text-url' );
	const $publicationDate = $( '#lc_resource_publication_date' );
	const $publicationYear = $( '#lc_resource_publication_year' );
	const $toValidate = $( '[data-validation]' );

	$urlFields.blur( ( e ) => {
		const val = $( e.target ).val();
		if ( 0 !== val.length ) {
			$( e.target ).val( schemify( val ) );
		}
	} );

	$publicationDate.change( ( e ) => {
		const val = $( e.target ).val();
		$publicationYear.val( val.substring( 0, 4 ) );
	} );

	if ( !$toValidate.length ) {
		return;
	}

	/**
	 * Ensure that a user-supplied URL is a valid URL.
	 *
	 * @param {string} value The URL that the user has entered.
	 */
	function isUrl( value ) {
		try {
			new URL( value );
			return true;
		} catch ( e ) {
			return false;
		}
	}

	/**
	 * Ensure that a user-supplied URL matches an expected domain.
	 *
	 * @param {string} expectedDomain The expected domain of the URL.
	 * @param {string} actualUrl The actual URL that the user has entered.
	 */
	function checkUrlDomain( expectedDomain, actualUrl ) {
		const actualDomain = new URL( actualUrl ).hostname;
		if ( actualDomain === expectedDomain ) {
			return true;
		}
		return false;
	}

	/**
	 * Ensure that a user-supplied datetime string matches the ISO 8601 format for a date or a datetime.
	 *
	 * @see https://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param {string} val The value that the user has entered.
	 * @param {string} type The type of datetime string expected (date or datetime).
	 */
	function checkDateTime( val, type ) {
		if ( 'date' === type  ) {
			return /^\d{4}[/-](0?[1-9]|1[012])[/-](0?[1-9]|[12][0-9]|3[01])$/.test( val );
		}
		// TODO: Add datetime validation.
		return false;
	}

	/**
	 * Ensure that a unique identifier string matches one of DOI, ISBN, ISSN formats.
	 *
	 * @see https://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param {string} val The value that the user has entered.
	 * @param {string} type The type of datetime string expected (date or datetime).
	 */
	function checkIdentifier( val, type ) {
		switch( type ) {
				case 'doi':
					return doi( { exact: true } ).test( val );
				case 'isbn':
					return ISBN.isValidIsbn( val );
				case 'issn':
					return issn( val );
		}
		return false;
	}

	/**
	 * Validate the form.
	 *
	 * @param {Event} event
	 */
	function validateForm( event ) {
		const $errorFields = [];
		let $firstError = null;
		$( '.error' ).remove();

		$urlFields.each( ( i, e ) => {
			if ( $( e ).is( ':visible' ) ) {
				const val = $( e ).val();
				if ( 0 !== val.length ) {
					$( e ).val( schemify( val ) );
				}
			}
		} );

		/**
		 * Validate a form element.
		 *
		 * @param {*} element
		*/
		function validateRow( element ) {
			const $this = $( element );
			const val = $this.val();
			const $row = $this.parents( '.cmb-row' );
			let valid = false;

			if ( $this.data( 'domain' ) && $this.is( ':visible' ) ) {
				if ( 0 !== val.length && ( ! isUrl( val ) || ! checkUrlDomain( $this.data( 'domain' ), val ) ) ) {
					addDomainMismatchError( $row, $this, $this.data( 'domain' ) );
					valid = false;
				} else {
					valid = true;
				}
			}

			if ( $this.data( 'datetime' ) && $this.is( ':visible' ) ) {
				if ( 0 !== val.length && ! checkDateTime( val, $this.data( 'datetime' ) ) ) {
					addDateTimeError( $row, $this, $this.data( 'datetime' ) );
					valid = false;
				} else {
					valid = true;
				}
			}

			if ( $this.data( 'identifier' ) && $this.is( ':visible' ) ) {
				if ( 0 !== val.length && ! checkIdentifier( val, $this.data( 'identifier' ) ) ) {
					addIdentifierError( $row, $this, $this.data( 'identifier' ) );
					valid = false;
				} else {
					valid = true;
				}
			}

			if ( $this.data( 'required' ) && $this.is( ':visible' ) ) {
				if ( 0 === val.length ) {
					addRequiredError( $row );
					valid = false;
				} else {
					valid = true;
				}
			}

			if ( valid ) {
				removeError( $row );
			}
		}

		/**
		 * Add required flag to a form row.
		 *
		 * @param {jQuery} $row
		 */
		function addRequiredError( $row ) {
			const $label = $row.find( '.cmb-th label' );
			$errorFields.push(
				{ id: $label.attr( 'for' ), label: $label.text().replace( ' (Required)', '' ), type: 'required' }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The label of the required field. */
			const errorText = sprintf( __( 'A %s is required.', 'learning-commons-framework' ), $label.text().replace( ' (Required)', '' ).toLowerCase() );
			const error = $( `<p class="error">${errorText}</p>` );
			$row.children( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		/**
		 * Add domain mismatch flag to a form field.
		 *
		 * @param {jQuery} $row
		 * @param {jQuery} $field
		 * @param {string} expectedDomain
		 */
		function addDomainMismatchError( $row, $field, expectedDomain ) {
			const $label = $row.find( '.cmb-th label' );

			$errorFields.push(
				{ id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ), label: $label.text(), type: 'domain', expected: expectedDomain }
			);
			if ( $row.hasClass( 'cmb-repeat' ) ) {
				$field.parent( '.cmb-td' ).parent( '.cmb-repeat-row' ).addClass( 'form-invalid' );
			} else {
				$row.addClass( 'form-invalid' );
			}
			/* translators: %s: The expected domain name for the URL input. */
			const errorText = sprintf( __( 'The URL must be an address at the domain <em>%s</em>.', 'learning-commons-framework' ), expectedDomain );
			const error = $( `<p class="error">${errorText}</p>` );
			$field.parent( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		/**
		 * Add datetime error flag to a form field.
		 *
		 * @param {jQuery} $row
		 * @param {string} type
		 */
		function addDateTimeError( $row, $field, type ) {
			const $label = $row.find( '.cmb-th label' );

			$errorFields.push(
				{ id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ), label: $label.text(), type: 'datetime', expected: type }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The type of the datetime input field (date or datetime). */
			const errorText = sprintf( __( 'The supplied %1$s is not valid.', 'learning-commons-framework' ), type );
			const error = $( `<p class="error">${errorText}</p>` );
			$field.parent( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		/**
		 * Add unique identifier error flag to a form field.
		 *
		 * @param {jQuery} $row
		 * @param {string} type
		 */
		function addIdentifierError( $row, $field, type ) {
			const $label = $row.find( '.cmb-th label' );

			$errorFields.push(
				{ id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ), label: $label.text(), type: 'identifier', expected: type }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The type of the identifier input field (DOI, ISBN, or ISSN). */
			const errorText = sprintf( __( 'The supplied %1$s is not in a valid format.', 'learning-commons-framework' ), type.toUpperCase() );
			const error = $( `<p class="error">${errorText}</p>` );
			$field.parent( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		/**
		 * Remove error class from a form row.
		 *
		 * @param {jQuery} $row
		 */
		function removeError( $row ) {
			$row.removeClass( 'form-invalid' );
		}

		$toValidate.each( function() {
			validateRow( this );
		} );

		if ( $firstError ) {
			event.preventDefault();
			$( '#validation-message' ).remove();
			const errorMessage = __( 'The form contains errors:', 'learning-commons-framework' );
			const errorList = $errorFields.reduce( ( html, field, index ) => {
				let errorText;
				if ( 'required' === field.type ) {
					/* translators: %s: The label of the required field. */
					errorText = sprintf( __( 'A %s is required.', 'learning-commons-framework' ), field.label.toLowerCase() );
				}
				if ( 'domain' == field.type ) {
					/* translators: %s: The expected domain name for the URL field. */
					errorText = sprintf( __( 'The URL must be an address at the domain <em>%s</em>.', 'learning-commons-framework' ), field.expected );
				}
				if ( 'datetime' === field.type ) {
					/* translators: %s: The type of the datetime input field (date or datetime). */
					errorText = sprintf( __( 'The supplied %s is not valid.', 'learning-commons-framework' ), field.expected );
				}
				if ( 'identifier' === field.type ) {
					/* translators: %s: The type of the identifier input field (DOI, ISBN, or ISSN). */
					errorText = sprintf( __( 'The supplied %s is not valid.', 'learning-commons-framework' ), String.prototype.toUpperCase.call( field.expected ) );
				}
				if ( 0 < index ) {
					return `${html}<li><a href="#${field.id}">${errorText}</a></li>`;
				} else {
					return `<li><a href="#${field.id}">${errorText}</a></li>`;
				}
			}, $errorFields[0] );
			const errorContainer = $( '<div role="alert"></div>' );
			const error = $(
				`<div id="validation-message" class="notice notice-error">
					<p>${errorMessage}</p>
					<ul>${errorList}</ul>
				</div>` );
			$( '.wp-header-end' ).after( errorContainer );
			$( errorContainer ).append( error );
		}
		return true;
	}

	$form.on( 'submit', validateForm );
} );
