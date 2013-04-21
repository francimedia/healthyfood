var carousel = new Carousel("#carousel");
carousel.init();

$('.nav a').on('click', function(e){
	var self = $(this);
	carousel.showPane(self.index());
	$('.nav a').removeClass('active');
	self.addClass('active');
});

HF = new Backbone.Marionette.Application();
