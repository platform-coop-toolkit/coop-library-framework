/* global acf */

const { __, sprintf } = wp.i18n;
const { speak } = wp.a11y;
const daysInMonth = require( 'days-in-month' );

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
	const $titleRow = $( '#titlewrap' );

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
		 * Add required flag to a form row.
		 *
		 * @param {jQuery} $row
		 */
		function addRequiredError( $row ) {
			const $label = $row.find( 'label' );
			const labelText = $label.text().replace( 'Add title', 'Title' );
			$errorFields.push(
				{ id: $label.attr( 'for' ), label: labelText, type: 'required' }
			);
			$row.addClass( 'form-invalid' );
			/* translators: %s: The label of the required field. */
			const errorText = sprintf( __( 'A %s is required.', 'coop-library-framework' ), labelText.toLowerCase() );
			const error = $( `<p class="error">${errorText}</p>` );
			$row.append( error );
			$firstError = $firstError ? $firstError : $row;
		}

		if ( 0 === $titleRow.children( 'input' ).val().length ) {
			addRequiredError( $titleRow );
		}

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
