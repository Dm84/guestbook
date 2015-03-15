define(['jquery', 'marionette'], function ($, Marionette) {
    
    return Marionette.ItemView.extend({

        template: '#userbar-template',        
        
        ui: {
        	login: '#navbar-login',
            modal: '.modal',
            save_btn: '.modal-footer .save-btn',
            form: 'form',
            name: 'input[name="name"]'
        },
        
        initialize: function (options) {            
            if (_.has(options, 'model')) this.model = options.model;
            if (_.has(options, 'url')) this.url = options.url;
        },
        
        showModal: function () {
            this.ui.modal.modal('show');
        },
        
		modelEvents: {
			"change": function () { this.render(); }
		},        
        
        events: {
        	"click @ui.login": function (e) {
        		e.preventDefault();
        		this.showModal();
        	},
        	
            "click @ui.save_btn": function (e) {
            	e.preventDefault();
                this.ui.modal.modal('hide');
                this.ui.modal.on('hidden.bs.modal', _.bind(function () {                	
                	this.model.save({ 'name': this.ui.name.val() }, { wait: true });
                }, this));
                
            }
        }
        
    });
    
    
})