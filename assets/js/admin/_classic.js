const {jQuery} = window;

( function( $ ) {

	$( document ).ready( function() {

		// Extract the localized data
		const { taxonomies, terms, meta } = window.primaryTerm;

		// Iterate through each taxonomy metabox
		$.each( taxonomies, function( taxonomy, data ){

			const metabox = $( `#${data.metabox}` );
			const inside = metabox.find( '.inside' );

			// Create the select
			const select = document.createElement( 'select' );
			select.id = `primary-term_${ taxonomy }`;
			select.className = 'widefat';
			select.name = `primary-term_${ taxonomy }`;

			// Create the empty option
			const option = document.createElement( 'option' );
			option.value = '';
			option.text = 'Select one...';
			select.append( option );

			// Iterate through each taxonomy term
			$.each( data.terms, function( value, text ){
				if( value in terms[taxonomy] ){
					// If the term is assigned to the post, create the option
					createOption( select, value, text, meta[select.id] == value );
				}
			} );

			// Create the label
			const label = document.createElement( 'label' );
			label.className = 'post-attributes-label';
			label.for = select.id;
			label.innerText = `Primary ${data.label}`;

			// Create the wrapper
			const wrap = document.createElement( 'p' );

			// Add it all to the page
			wrap.append( label );
			inside.append( wrap );
			inside.append( select );

		} );

		/**
         * When a hierarchical taxonomy term is selected / deselected, add or remove it from the select.
         * For the record, this could *all* be done much more efficiently, I just want to get it working for now.
         * This also needs to be made dynamic, but I'm running out of time.
         * This will get better.
         */
		$( document ).on( 'click', 'input[name="post_category[]"]', function(){
			const select = $( '#primary-term_category' );
			const termId = $( this ).val();
			if( $( this ).is( ':checked' ) ){
				// Add the item
				const text = taxonomies['category'].terms[termId];
				createOption( select, termId, text, false );
			}else{
				// Remove the item
				removeOption( select, termId );
			}
		} );

	} );

	/**
     * Create option for the primary term select element
     * @param {string} select 
     * @param {string} value 
     * @param {string} text 
     * @param {boolean} selected 
     */
	function createOption( select, value, text, selected ){
		const option = document.createElement( 'option' );
		option.value = value;
		option.text = text;
		option.selected = selected;
		select.append( option );
	}

	/**
     * Remove option from the primary term select element
     * @param {*} select 
     * @param {*} termId 
     */
	function removeOption( select, termId ){
		$.each( select.find( 'option' ), function(){
			const value = $( this ).val();
			if( value == termId ){
				$( this ).remove();
			}
		} );
	}

} )( jQuery );