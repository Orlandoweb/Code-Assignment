(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $( window ).load(function() {
        var title = ''
        $.fn.dataTable.ext.classes.sPageButton = 'button button-primary';
        $('.recent-posts-table').DataTable({
            'order': [[ 1, 'asc' ]],
            'pagingType': 'simple',
            'oSearch': {'sSearch': title}
        });

        $('body').on('click', '.add-to-posts', function(e){
            e.preventDefault();
            $(this).html('Adding...').prop('disabled', true);
            var token = ajax_post_object.tokens.default;
            var formdata =  new FormData();
            var id = $(this).attr('data-id');
            formdata.append('token', token);
            formdata.append('action', 'add_new_post');
            formdata.append('id', id);
            var form = new CAForm(formdata);
            form.submitForm(callback);
        });

        function CAForm(formdata) {
            this.formdata = formdata;
        }

        function callback(data) {
            if(data.message!='') {
                console.log(data.message);
            }
        }

        CAForm.prototype.submitForm = function(callback) {
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_post_object.ajaxurl,
                data: this.formdata,
                contentType: false,
                processData: false,
                success: function(data){
                    if(callback)
                        location.reload();
                }
            });
        }
    });


})( jQuery );
