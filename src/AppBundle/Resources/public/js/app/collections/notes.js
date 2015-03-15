define(['backbone', 'models/note'], function (Backbone, NoteModel) {

	var Collection = Backbone.Collection.extend({			

		initialize: function (items, options) {			
			if (options !== 'undefined' && options.url !== 'undefined') this.url = options.url;
		}
	
	});
	
	return Collection;
});
