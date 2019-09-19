const { __, sprintf } = wp.i18n;
const schemify = require( 'url-schemify' );

jQuery( document ).ready( function( $ ) {
	const $form = $( '#post' );
	const $urlFields = $( '.cmb2-text-url' );
	const $toValidate = $( '[data-validation]' );

	$urlFields.blur( ( e ) => {
		const val = $( e.target ).val();
		$( e.target ).val( schemify( val ) );
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
	 * Validate the form.
	 *
	 * @param {Event} event
	 */
	function validateForm( event ) {
		const $errorFields = [];
		let $firstError = null;
		$( '.error' ).remove();

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

			if ( 'true' === $this.data( 'required' ) ) {
				if ( ! val ) {
					addRequiredError( $row );
					valid = false;
				} else {
					valid = true;
				}
			}

			if ( $this.data( 'domain' ) && $this.is( ':visible' ) ) {
				if ( 0 !== val.length && ( ! isUrl( val ) || ! checkUrlDomain( $this.data( 'domain' ), val ) ) ) {
					addDomainMismatchError( $row, $this, $this.data( 'domain' ) );
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
				{ id: $label.attr( 'for' ), label: $label.text(), type: 'required' }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The label of the required field. */
			const errorText = sprintf( __( 'A %s is required.', 'learning-commons-framework' ), $label.text().toLowerCase() );
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
			$row.addClass( 'form-invalid' );
			/* translators: %s: The expected domain name for the URL input. */
			const errorText = sprintf( __( 'The URL must be an address at the domain <em>%s</em>.', 'learning-commons-framework' ), expectedDomain );
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
