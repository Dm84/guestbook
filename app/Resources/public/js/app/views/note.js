define(['jquery', 'marionette'], function ($, Marionette) {
	
	var NoteItemView = Marionette.ItemView.extend({
		
		tagName: 'div class="note-item"',
		template: '#note-item-template',		
		
		initialize: function (options) {
			
			if (_.has(options, 'editView')) {
				this.editView = options.editView;
			}

			this.model.bind("request", this.onRequest, this);
			this.model.bind("sync", this.onSync, this);
			
			this.model.fetch();			
		},
		
		onRequest: function () {
			this.$el.append('<div class="loader"></div>');			
		},
		
		onSync: function () {
			this.$el.find('.loader').remove();
		},
		
		onRender: function () {			
			
			if (this.model.user_id == this.$el.find('.panel').data('user_id'))
			{
				this.$el.find('.panel-footer').removeClass('hidden');
			}
		},
			
		ui: {
			edit_btn: '.btn-edit',
			form: 'form'
		},
		
		modelEvents: {
			"change": function () { this.render(); }
		},
		
		events: {
			"click @ui.edit_btn": function (e) {
				this.editView.model = this.model;
				this.editView.render();				
				this.editView.ui.modal.modal('show');
			},
			

		}
			
	
	});
	
	return NoteItemView;	
})