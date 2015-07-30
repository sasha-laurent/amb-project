(function($){
    
    if(!$.taxonomyBrowser){
        $.taxonomyBrowser = new Object();
    };
    
    $.taxonomyBrowser.keys = function(el, options){
        
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.

        var base = this;
        
        // Access to jQuery and DOM versions of element

        base.$el = $(el);

        base.el = el;

        var KEYUP = 38,
            KEYDOWN = 40,
            KEYLEFT = 37,
            KEYRIGHT = 39,
            KEYTAB = 9, //vmb added
            KEYENTER = 13, //vmb added
            KEYDELETE = 68, // vmb del key
            KEYSAVE = 83; // vmb s key
        
        
        // Add a reverse reference to the DOM object

        base.$el.data("taxonomyBrowser.keys", base);

        
        base.init = function(){
            
            base.options = $.extend({},base.$el.data('taxonomyBrowser').options, options);
            
            /*
              Initialize once taxonomyBrowser Root column has been added
             */

            
            base.KeyEvents.init();            

        };
        
        


        /**
        * Add events to the taxonomy browser
        * @method initKeyEvents
        */
       
        base.KeyEvents = {

          init: function(){

            /*
              Reverse Lookup
             */
            
            var self = this;
            
                        
            /*
              Add focus to the First Column              
             */                        
            
            //base.$el.find(base.options.columnclass).eq(0).focus();
            
            $($.fn.taxonomyBrowser.elementCache[0])
              .find(base.options.columnclass)
              .eq(0)
              .focus();

           
            /**
             * KeyDown Event Handler
             * @param  {[type]} event             
             */
            
            //vmb event added
            $(".add-node").click(function( event ) { 
              //self.columns = base.$el.find(base.options.columnclass);
              self.pressTab.apply(self);
            });
            $(".edit-node").click(function( event ) { 
              //self.columns = base.$el.find(base.options.columnclass);
              self.pressShiftEnter.apply(self);
            });
            $(".delete-node").click(function( event ) { 
              self.pressDelete.apply(self);
            });
            


            // end vmb event added

            $('body').on('keydown', function(event){ // vmb changed to body to catch all
              if ($('#dialog-form').length > 0 && $('#dialog-form').dialog("isOpen")) { // vmb cancel keys on dialog open
                return;
              }
              if ($('#dialog-add-text').length > 0 && $('#dialog-add-text').dialog("isOpen")) { // vmb cancel keys on dialog open
                return;
              }
              self.columns = base.$el.find(base.options.columnclass);
              switch(event.which){

                case KEYUP:
                  self.moveUp.apply(self, event);
                  break;

                case KEYRIGHT:
                  self.moveRight.apply(self);
                  break;

                case KEYDOWN:
                  self.moveDown.apply(self);  
                  break;

                case KEYLEFT:
                  self.moveLeft.apply(self);    
                  break;

                case KEYTAB: // vmb case added
                  self.pressTab.apply(self);
                  break;

                case KEYDELETE: // vmb case added
                  if (event.shiftKey) {
                    self.pressDelete.apply(self);
                  }
                  break;

                case KEYENTER: // vmb case added
                  if (event.shiftKey) {
                    self.pressShiftEnter.apply(self);
                  }
                  else {
                    self.pressEnter.apply(self);
                  }
                    break;

                case KEYSAVE:
                  if(event.shiftKey){
                    $.fn.taxonomyBrowser.ajax_save();
                  }
                    break;

              };


              /*
              Prevent Default Browser Actions
               */
              
              if( event.which == KEYUP ||
                  event.which == KEYDOWN ||
                  event.which == KEYLEFT ||
                  event.which == KEYRIGHT ||
                  event.which == KEYTAB || // vmb added
                  event.which == KEYENTER ||
                  event.which == KEYDELETE){ // vmb added
                // We can't prevent KEYSAVE as it corresponds to the 'S' key -
                // needed for content creation

                event.preventDefault();
              }
              
              
            });

          },

          moveUp: function(){            

            this.move('up');

          },

          moveDown: function(){

            
            this.move('down');


          },

          moveRight: function(){

            this.move('right');
            
          },

          moveLeft: function(){

            this.move('left');
            
          },
          pressTab: function(){ // vmb added
            this.keyHit('tab');
          },
          pressShiftEnter: function(){
            this.keyHit('shiftEnter');
          },
          pressDelete: function(){
            this.keyHit('delete');
          },
          pressEnter: function(){
            this.keyHit('enter');
          },

          // edit the move controls here
          move: function(direction){
            $.fn.taxonomyBrowser.move.call(base, direction);
            /*
               Add Focus to the Link: So the container scrolls
            */
            $(".currentActive").find('a').focus();
          },

          keyHit: function(key) { // vmb added
            if (typeof $(".currentActive") != 'undefined') {
              var dataID = $(".currentActive").attr('data-id');
              if ($.fn.taxonomyBrowser.isOntologyMode.call(base, 'edit')) { // edit mode for our keys
                if (key == 'tab') {
                  if (typeof dataID == 'undefined') {
                    dataID = null;
                  }
                  $.fn.taxonomyBrowser.nodeDialog.call(base, dataID);
                }
                else if (key == 'shiftEnter') {
                  $.fn.taxonomyBrowser.nodeDialog.call(base, dataID,'edit');
                }
                else if (key == 'delete') {
                  $.fn.taxonomyBrowser.nodeDelete.call(base, dataID);
                }
              }
              else { //add mode for our keys
                if (key == 'enter') {
                  $.fn.taxonomyBrowser.selectTag.call(base, dataID);
                }
              }
              
            }
            else { // currentActive not found
            }
            
          }

        }
        
        // Run initializer

        base.init();
        
    };

    /* Loop Through All Elements to Instantiate the plugin */
    
    $.fn.taxonomybrowser_keys = function(options){
        var elements = $.fn.taxonomyBrowser.elementCache;            		

    		$.each(elements, function(index, ele){
    			(new $.taxonomyBrowser.keys(ele, options));
    		});

    };


    
    // Initialize

    $(window).load(function(){

    	$(document).taxonomybrowser_keys();

    });
    
    
})(jQuery);