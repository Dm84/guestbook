define(['jquery', 'marionette'], function ($, Marionette) {
	
	return Marionette.ItemView.extend({

		template: '#note-edit-template',		
		
		ui: {
			modal: '.modal',
			save_btn: '.modal-footer .save-btn',
			form: 'form'
		},
		
		initialize: function (options) {
			
			if (_.has(options, 'collection')) this.collection = options.collection;			
		},		
		
		showModal: function () {
			this.ui.modal.modal('show');
		},
		
		events: {
			"click @ui.save_btn": function (e) {
				this.ui.modal.modal('hide');
				this.collection.create({ 'text': this.ui.form.find('textarea').val() }, { wait: false });
			}
		}
		
	});
	
	
})