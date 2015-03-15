define(['underscore', 'backbone'], function (_, Backbone) {
	
	var Model = Backbone.Model.extend({
		
		defaults: {			
			'text': ''
		}	
	});
	
	return Model;
});