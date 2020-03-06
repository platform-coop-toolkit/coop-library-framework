<?php
/**
 * Hidden field for Advanced Custom Field Pro.
 *
 * @package CoopLibraryFramework
 */

namespace CoopLibraryFramework;

/**
 * Class which registers a hidden field for Advanced Custom Field Pro.
 */
class HiddenField extends \acf_field {
	/**
	 * Constructor.
	 *
	 * @param array $settings The settings for this field.
	 */
	public function __construct( $settings = [] ) {
		$this->name     = 'hidden';
		$this->label    = __( 'Hidden', 'coop-library-framework' );
		$this->category = 'layout';
		$this->l10n     = array();
		$this->settings = $settings;

		parent::__construct();
	}

	/**
	 *  Render the field's HTML.
	 *
	 *  @param array $field The $field being rendered.
	 *  @return void
	 */
	public function render_field( $field ) { ?>
		<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
		<?php
	}
}
