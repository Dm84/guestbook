define(['underscore', 'marionette', 'views/note'], function (_, Marionette, NoteItemView) {
	
	var NoteListView = Marionette.CollectionView.extend({

		tagName: 'div class="note-list"',
		childView: NoteItemView,
		
		emptyView: Marionette.ItemView.extend({ 
			template: _.template('Пока нет комментариев'),			
		}),
		
		initialize: function (options) {
			if (_.has(options, 'editView')) {
				this.editView = options.editView;
			}
			
			this.collection.bind("request", this.onRequest, this);
			this.collection.bind("sync", this.onSync, this);
			
			this.collection.fetch();
		},
		
		onRequest: function () {
			this.$el.prepend('<div class="loader"></div>');			
		},
		
		onSync: function () {
			this.$el.find('.loader').remove();
		},
		

		buildChildView: function (child, ChildViewClass, childViewOptions) {
			
			return new ChildViewClass(_.extend({ model: child, editView: this.editView }, childViewOptions));
		}		
	});
	
	return NoteListView;
});