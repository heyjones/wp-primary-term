const {jQuery} = window;

( function( $ ) {

	$( document ).ready( function() {

		$.each( window.primaryTerm.taxonomies, function( taxonomy, data ){

			const metabox = $( `#${data.metabox}` );
			const inside = metabox.find( '.inside' );

			const select = document.createElement( 'select' );
			select.id = `primary-term_${ taxonomy }`;
			select.className = 'widefat';
			select.name = `primary-term_${ taxonomy }`;

			const option = document.createElement( 'option' );
			option.text = 'Select one...';
			select.append( option );

			$.each( data.terms, function( value, text ){
				const option = document.createElement( 'option' );
				option.value = value;
				option.text = text;
				select.append( option );
			} );

			const label = document.createElement( 'label' );
			label.className = 'post-attributes-label';
			label.for = select.id;
			label.innerText = `Primary ${data.label}`;

			const wrap = document.createElement( 'p' );
			wrap.append( label );

			inside.append( wrap );

			inside.append( select );

		} );
	} );

} )( jQuery );