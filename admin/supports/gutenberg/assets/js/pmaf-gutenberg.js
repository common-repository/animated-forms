/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */

const t = window.React,
        a = window.wp.i18n,
        l = window.wp.blocks,
        r = window.wp.blockEditor,
		c = window.wp.element,
        n = window.wp.components;
		
var e = {
	n: (t) => {
		var a = t && t.__esModule ? () => t.default : () => t;
		return e.d(a, { a }), a;
	},
	d: (t, a) => {
		for (var l in a) e.o(a, l) && !e.o(t, l) && Object.defineProperty(t, l, { enumerable: !0, get: a[l] });
	},
	o: (e, t) => Object.prototype.hasOwnProperty.call(e, t),
};

const createElement = function(){};

let pmaf_formList = null;
let pmaf_forms = [];
let isFetching = false;

const pmafapp = {
	
	getIcon() {
		return createElement(
			'svg',
			{ width: 20, height: 20, viewBox: '0 0 612 612', className: 'dashicon' },
			createElement(
				'path',
				{
					fill: 'currentColor',
					d: 'M544,0H68C30.445,0,0,30.445,0,68v476c0,37.556,30.445,68,68,68h476c37.556,0,68-30.444,68-68V68 C612,30.445,581.556,0,544,0z M464.44,68L387.6,120.02L323.34,68H464.44z M288.66,68l-64.26,52.02L147.56,68H288.66z M544,544H68 V68h22.1l136,92.14l79.9-64.6l79.56,64.6l136-92.14H544V544z M114.24,263.16h95.88v-48.28h-95.88V263.16z M114.24,360.4h95.88 v-48.62h-95.88V360.4z M242.76,360.4h255v-48.62h-255V360.4L242.76,360.4z M242.76,263.16h255v-48.28h-255V263.16L242.76,263.16z M368.22,457.3h129.54V408H368.22V457.3z',
				},
			),
		);
	},
	async getForms() {
		// If a fetch is already in progress, exit the function.
		if ( isFetching ) {
			return;
		}

		// Set the flag to true indicating a fetch is in progress.
		isFetching = true;

		try {
			// Fetch forms.
			const response = await wp.apiFetch( {
				path: '/pmaf/v1/forms/',
				method: 'GET',
				cache: 'no-cache',
			} );

			// Update the form list.
			pmaf_formList = response;
			
			//pmaf_forms = pmafapp.getFormOptions();

		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( error );
		} finally {
			isFetching = false;
		}
	},
	getFormOptions() {
		
		const formOptions = pmaf_formList.map( ( value ) => (
			{ value: value.ID, label: ( value.post_title ? value.post_title : 'Form #'+ value.ID ) }
		) );

		formOptions.unshift( { value: '', label: 'Select Form' } );

		return formOptions;
	},
}


wp.blocks.registerBlockType('pmaf/animated-forms', {
	title: 'Animated Forms',
	icon: pmafapp.getIcon(),
	category: 'widgets',
	attributes: {
		formId: {
			type: 'string',
			default: '',
		},
	},
  
/* This configures how the content and color fields will work, and sets up the necessary elements */
  
  edit: function(props) {
	
	pmafapp.getForms();

	
    function updateContent(event) {
      props.setAttributes({formId: event.target.value})
    }
	
    return React.createElement(
		"div",
		null,
		React.createElement(
			"h3",
			null,
			"Animated Form"
		),
		/*React.createElement( "input", { 
			type: "text", value: props.attributes.formId, onChange: updateContent 
		}),*/
		React.createElement( n.ComboboxControl , { 
			label: "Select a contact form:",
			options: pmaf_forms,
			value: '',
			onFilterValueChange: () => {
				pmafapp.getFormOptions();
			}
			//onChange: (e) => l({ id: parseInt(e), hash: s.get(parseInt(e))?.hash, title: s.get(parseInt(e))?.title }),
		}),
    );
  },
  save: function(props) {
    return wp.element.createElement(
      "h3",
      props.attributes.formId
    );
  }
})