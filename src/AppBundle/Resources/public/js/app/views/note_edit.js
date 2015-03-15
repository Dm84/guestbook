define(['jquery', 'marionette'], function ($, Marionette) {
	
	return Marionette.ItemView.extend({

		template: '#note-edit-template',		
		
		ui: {
			modal: '.modal',
			save_btn: '.modal-footer .save-btn',
			form: 'form'
		},
		
		initialize: function (options) {			
			if (_.has(options, 'model')) this.model = options.model;
			if (_.has(options, 'url')) this.url = options.url;
		},
		
		showModal: function () {
        	this.render();
        	this.ui.modal.modal('show');
		},
		
		events: {
			"click @ui.save_btn": function (e) {
				this.ui.modal.modal('hide');
				this.model.save({ 'text': this.ui.form.find('textarea').val() });
			}
		}
		
	});
	
	
})