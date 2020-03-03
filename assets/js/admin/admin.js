/* global acf */

const { __, sprintf } = wp.i18n;
const { speak } = wp.a11y;
const daysInMonth = require( 'days-in-month' );
const doi = require( 'doi-regex' );
const ISBN = require( 'simple-isbn' ).isbn;
const issn = require( 'issn' );
const schemify = require( 'url-schemify' );

jQuery( document ).ready( function( $ ) {
	const yearField = acf.getField( 'field_5e56ee8953584' );
	const monthField = acf.getField( 'field_5e56eef559a76' );
	const dayField = acf.getField( 'field_5e56f04ee5a20' );

	/**
	 * Populate options for the publication day field that are appropriate
	 * to the selected year and month.
	 */
	const setupDays = () => {
		const year = yearField.val();
		const month = monthField.val();
		if ( year && month ) {
			const day = dayField.val();
			const daySelect = $( `#acf-${dayField.data.key}` );
			const dayCount = daysInMonth( year, month );
			if ( day > dayCount ) {
				const errorText = sprintf( __( 'The publication day has been reset as there are only %1$d days in the selected month, and your choice of %2$d is no longer valid.', 'coop-library-framework' ), dayCount, day );
				dayField.showNotice( {
					text: errorText,
					type: 'warning',
					dismiss: true,
				} );
				speak( errorText );
			}
			daySelect.children().not( '[value=""]' ).remove();
			for ( let i = 1; i < dayCount + 1; i++ ) {
				const option = document.createElement( 'option' );
				const val = 9 > i ? `0${i}` : i;
				option.setAttribute( 'value', val );
				option.innerText = i;
				daySelect.append( option );
			}
			if ( day ) {
				dayField.val( day );
			}
		}
	};

	yearField.on( 'change', setupDays );
	monthField.on( 'change', setupDays );

	const $form = $( '#post' );
	const $urlFields = $( '.cmb2-text-url' );
	const $rights = $( '#lc_resource_rights' );
	const $customRights = $( '#lc_resource_custom_rights' );
	const $titleRow = $( '#titlewrap' );
	const $toValidate = $( '[data-validation]' );

	$rights.on( 'change', ( e ) => {
		if ( 'custom' === e.target.value ) {
			$customRights.attr( 'disabled', false ).focus();
		} else {
			$customRights.attr( 'disabled', true ).val( '' );

		}
	} );

	$urlFields.blur( ( e ) => {
		const val = $( e.target ).val();
		if ( 0 !== val.length ) {
			$( e.target ).val( schemify( val ) );
		}
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
		// @see https://gist.github.com/dperini/729294
		return /^(?:(?:(?:https?):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\u00a1-\uffff][a-z0-9\u00a1-\uffff_-]{0,62})?[a-z0-9\u00a1-\uffff]\.)+(?:[a-z\u00a1-\uffff]{2,}\.?))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( value );
	}

	/**
	 * Ensure that a user-supplied URL matches an expected domain.
	 *
	 * @param {string} expectedDomain The expected domain of the URL.
	 * @param {string} actualUrl The actual URL that the user has entered.
	 */
	function checkUrlDomain( expectedDomain, actualUrl ) {
		if ( isUrl( actualUrl ) ) {
			const actualDomain = new URL( actualUrl ).hostname;
			if ( actualDomain === expectedDomain ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Ensure that a unique identifier string matches one of DOI, ISBN, ISSN formats.
	 *
	 * @param {string} val The value that the user has entered.
	 * @param {string} type The type of identifier expected (DOI, ISBN, or ISSN).
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

			let valid;

			if ( $this.is( ':visible' ) ) {
				if ( 0 !== val.length ) {
					if ( $this.data( 'domain' ) ) {
						if ( ! checkUrlDomain( $this.data( 'domain' ), val ) ) {
							addDomainMismatchError( $row, $this, $this.data( 'domain' ) );
							valid = false;
						} else {
							valid = true;
						}
					} else if ( $this.data( 'identifier' ) ) {
						if ( ! checkIdentifier( val, $this.data( 'identifier' ) ) ) {
							addIdentifierError( $row, $this, $this.data( 'identifier' ) );
							valid = false;
						} else {
							valid = true;
						}
					}
					if ( ! $this.data( 'domain' ) && $this.hasClass( 'cmb2-text-url' ) ) {
						if ( ! isUrl( val ) ) {
							addUrlError( $row, $this );
							valid = false;
						} else if ( valid ) {
							valid = true;
						}
					}
				} else if ( 0 === val.length ) {
					if ( $this.data( 'required' ) ) {
						addRequiredError( $row );
						valid = false;
					} else if ( valid ) {
						valid = true;
					}
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
			const $label = $row.find( 'label' );
			const labelText = $label.text().replace( ' (Required)', '' ).replace( 'Add title', 'Title' );
			$errorFields.push(
				{ id: $label.attr( 'for' ), label: labelText, type: 'required' }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The label of the required field. */
			const errorText = sprintf( __( 'A %s is required.', 'coop-library-framework' ), labelText.toLowerCase() );
			const error = $( `<p class="error">${errorText}</p>` );
			if ( $row.children( '.cmb-td' ).length ) {
				$row.children( '.cmb-td' ).append( error );
			} else {
				$row.append( error );
			}
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
				{
					id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ),
					label: $label.text(),
					type: 'domain',
					expected: expectedDomain
				}
			);
			if ( $row.hasClass( 'cmb-repeat' ) ) {
				$field.parent( '.cmb-td' ).parent( '.cmb-repeat-row' ).addClass( 'form-invalid' );
			} else {
				$row.addClass( 'form-invalid' );
			}
			/* translators: %s: The expected domain name for the URL input. */
			const errorText = sprintf( __( 'The URL must be an address at the domain <em>%s</em>.', 'coop-library-framework' ), expectedDomain );
			const error = $( `<p class="error">${errorText}</p>` );
			$field.parent( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		/**
		 * Add invalid URL flag to a form field.
		 *
		 * @param {jQuery} $row
		 * @param {jQuery} $field
		 */
		function addUrlError( $row, $field ) {
			const $label = $row.find( '.cmb-th label' );

			$errorFields.push(
				{
					id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ),
					label: $label.text(),
					type: 'url'
				}
			);
			if ( $row.hasClass( 'cmb-repeat' ) ) {
				$field.parent( '.cmb-td' ).parent( '.cmb-repeat-row' ).addClass( 'form-invalid' );
			} else {
				$row.addClass( 'form-invalid' );
			}
			const errorText = __( 'The supplied URL is not valid.', 'coop-library-framework' );
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
				{
					id: $row.hasClass( 'cmb-repeat' ) ? `${$label.attr( 'for' )}_repeat` : $label.attr( 'for' ),
					label: $label.text(),
					type: 'identifier',
					expected: type
				}
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The type of the identifier input field (DOI, ISBN, or ISSN). */
			const errorText = sprintf( __( 'The supplied %1$s is not in a valid format.', 'coop-library-framework' ), type.toUpperCase() );
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

		if ( 0 === $titleRow.children( 'input' ).val().length ) {
			addRequiredError( $titleRow );
		}

		$toValidate.each( function() {
			validateRow( this );
		} );

		if ( $firstError ) {
			event.preventDefault();
			$( '#validation-message' ).remove();
			const errorMessage = __( 'The form contains errors:', 'coop-library-framework' );
			const errorList = $errorFields.reduce( ( html, field, index ) => {
				let errorText;
				if ( 'required' === field.type ) {
					/* translators: %s: The label of the required field. */
					errorText = sprintf( __( 'A %s is required.', 'coop-library-framework' ), field.label.toLowerCase() );
				}
				if ( 'url' === field.type ) {
					errorText = __( 'The supplied url is not valid.', 'coop-library-framework' );
				}
				if ( 'domain' == field.type ) {
					/* translators: %s: The expected domain name for the URL field. */
					errorText = sprintf( __( 'The URL must be an address at the domain <em>%s</em>.', 'coop-library-framework' ), field.expected );
				}
				if ( 'identifier' === field.type ) {
					/* translators: %s: The type of the identifier input field (DOI, ISBN, or ISSN). */
					errorText = sprintf( __( 'The supplied %s is not valid.', 'coop-library-framework' ), String.prototype.toUpperCase.call( field.expected ) );
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
