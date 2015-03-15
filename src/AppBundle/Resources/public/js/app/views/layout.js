define(['underscore', 'marionette', 'views/note'], function (_, Marionette, NoteItemView) {
	
	var Layout = Marionette.CompositeView.extend({
		template: '#layout-template',
	
		regions: {
			note_list: '#note-list'
		}
	});
	
	return Layout;
});