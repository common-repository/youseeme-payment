/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import View from './view';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

const settings = window.wc.wcSettings.getSetting( 'youseeme_data', {} );

const label =
	window.wp.htmlEntities.decodeEntities( settings.title ) ||
	window.wp.i18n.__( 'Youseeme Crypto', 'youseeme' );

const BlockGateway = {
	name: 'youseeme',
	label,
	content: Object( window.wp.element.createElement )( View, null ),
	edit: Object( window.wp.element.createElement )( Edit, null ),
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: settings.supports,
	},
};

window.wc.wcBlocksRegistry.registerPaymentMethod( BlockGateway );
