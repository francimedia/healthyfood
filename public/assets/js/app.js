var carousel = new Carousel("#carousel");
carousel.init();

$('.nav a').on('click', function(e){
	var self = $(this);
	carousel.showPane(self.index());
	$('.nav a').removeClass('active');
	self.addClass('active');
});

HF = new Backbone.Marionette.Application();

HF

// Initializer receives any options we send to our application when we call its “start” 
MyApp.addInitializer(function(options) {
  // Initialize a CompositeView using the collection passed in (options)
  var AppView = new View({
    collection: options.cats
  });
  MyApp.mainRegion.show(angryCatsView);
});