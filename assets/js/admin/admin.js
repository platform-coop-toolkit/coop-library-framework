const { __, sprintf } = wp.i18n;

jQuery( document ).ready( function( $ ) {
	const $form = $( '#post' );
	const $toValidate = $( '[data-validation]' );

	if ( !$toValidate.length ) {
		return;
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

			if ( 'required' === $this.data( 'validation' ) ) {
				if ( ! val ) {
					addRequired( $row );
				} else {
					removeRequired( $row );
				}
			}
		}

		/**
		 * Add required flag to a form row.
		 *
		 * @param {jQuery} $row
		 */
		function addRequired( $row ) {
			const $label = $row.find( '.cmb-th label' );
			$errorFields.push(
				{ id: $label.attr( 'for' ), label: $label.text() }
			);
			$row.addClass( 'form-invalid' );
			const errorText = sprintf( __( 'A %s is required.', 'learning-commons-framework' ), $label.text().toLowerCase() );
			const error = $( `<p class="error">${errorText}</p>` );
			$row.children( '.cmb-td' ).append( error );
			$firstError = $firstError ? $firstError : $row;

		}

		/**
		 * Remove required flag from a form row.
		 *
		 * @param {jQuery} $row
		 */
		function removeRequired( $row ) {
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
				const errorText = sprintf( __( 'A %s is required.', 'learning-commons-framework' ), field.label.toLowerCase() );
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
