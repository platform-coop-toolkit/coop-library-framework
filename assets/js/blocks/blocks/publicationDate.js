const { registerBlockType } = wp.blocks;
const { TextControl } = wp.components;

registerBlockType( 'learning-commons-framework/publication-date', {
	title: 'Publication Date',
	icon: 'calendar',
	category: 'common',

	attributes: {
		publicationDate: {
			type: 'string',
			source: 'meta',
			meta: 'lc_resource_publication_date',
		},
	},

	/**
	 * Block edit method.
	 *
	 * @param {string} className
	 * @param {function} setAttributes
	 * @param {object} attributes
	 */
	edit( { className, setAttributes, attributes } ) {
		/**
		 * Update the publication date.
		 *
		 * @param {string} publicationDate
		 */
		function updatePublicationDate( publicationDate ) {
			setAttributes( { publicationDate } );
		}

		return (
			<div className={ className }>
				<TextControl
					label="Publication Date"
					value={ attributes.publicationDate }
					onChange={ updatePublicationDate }
					help="Enter date in YYYY-MM-DD format."
				/>
			</div>
		);
	},

	/**
	 * Block save method. Null because data is saved via post attributes.
	 */
	save() {
		return null;
	}
} );
