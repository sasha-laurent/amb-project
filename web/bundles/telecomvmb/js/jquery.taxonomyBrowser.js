;(function($, window, document){

    /**
    * Miller Column jQuery plugin 
    *
    * @class taxonomybrowser
    * @constructor
    * @param el {Object} The Container element
    * @param {Object} [base] To avoid scope issues, use 'base' instead of 'this' to reference this class from internal events and functions
    *   @param {Object} [base.taxonomy] Object (Json array), containing all the object nodes of ontology
    *                                   e.g [{"id":"1","label":"Auteur","parent":null,"type":"simple"}, {}]
    *   @param {Object} [base.taxonomyHash] id => Object (json Object), same as base.taxonomy, but with {id: {nodeObject}, id2 : {nodeObject}}.
                                            Used as a hash table to quickly get node by id
    *   @param {Object} [base.taxonomyChildren] Json object, in form of id => Json array with children
    *   @param {Object} [base.taxonomyColumns] Json array, containing Json arrays with ids of all columns, that must be visible in the ui.
    *   @param {Object} [base.tags] Json object in form of id of video => Json object in form id of ontology node => value 
    *   @param {String} [base.currentActive] Represents the data-id of the current node the user has focus.
    *   @param {String} [base.activeTags]  Json Object in form of: Ontology Node id: value. Can be applied to one ore many videos. 
    *   @param {Object} [base.nodeInfo] Json Object, contains info about the node we edit or add
    *   @param {Object} [base.activeSearch] Json Object, contains nodes we have selected. node-id => column where we will place the children
    *   @param {Object} [base.activeSearchChildren] 
    *   @param {Object} [base.searchValueHash]
    *   @param {Object} [base.liEdges]  JSON Object, contains the nodes that we will add a top edge. Used for the search, to visually seperate children.
    *   @param {Object} [base.searchHash] JSON Object, for reverse search, in form of {nodeID1: {videoID1:value, videoID2:value,...}, nodeID2:{},....}
    *   @param {Object} [base.searchVideo] JSON Object, showing video a node or his children contain {1:[8,9], 2:[2,3,4],...}
    *   @param {Boolean} [base.safeExit] False if we have unsaved changes.
                                         Used to display a message to the user if he leaves window with unsaved changes.
    * @param {Object} [base.options] Default Options for Taxonomy Browser
    *   @param {String} [base.options.json] JSON File with the taxonomy structure of the ontology || Required Properties: id, label, type, parent
    *   @param {String} [base.options.rootvalue] Top parents have the attribute parent set to 'null'
    *   @param {String} [base.options.columnclass] Class name of generated column
    *   @param {String} [base.options.ontologyMode] Can be edit for edit an ontology, or add if we add an index
    *   @param {Number} [base.options.columns] Maximum number of columns
    *   @param {Number} [base.options.columnheight] Height of the columns
    *   @param {String} [base.options.save] Url to make the post request for save
    */
    

    $.taxonomyBrowser = function(el, options){

        /*
          Push to Element Cache Function so it can be access by external libraries
         */
        
        $.fn.taxonomyBrowser.elementCache.push(el);

        /* 
         * To avoid scope issues, use 'base' instead of 'this'
         * to reference this class from internal events and functions.
         */

        var base = this;
        
        /**
         * Access to jQuery and DOM versions of element
         */

        base.$el = $(el);

        base.el = el;
       

        /**
         * Add a Column Wrapper
         */
        
        base.$wrap = $('<div class="miller--column--wrap" />').appendTo(base.$el);

        
        /**
         * Options
         */
        
        base.options = $.extend({},$.taxonomyBrowser.defaultOptions, options, base.$el.data());        
        
         /**
         * Add a min-height to container
         */
        
        base.$el.css({
          minHeight: base.options.columnheight
        });


        /**
         * Parent Array
         */
        
        base.parentArray = [];

        /*
        * Template
        */

        base.template = Handlebars.compile(document.getElementById(base.options.template).innerHTML);

        /* 
         * Add a reverse reference to the DOM object
         */

        base.$el.data("taxonomyBrowser", base);

        base.taxonomy = []; 
        base.taxonomyHash = {}; 
        base.currentActive = undefined; 
        base.taxonomyColumns = []; 
        base.taxonomyChildren = {}; 
        base.nodeInfo = {}; // dialog stores info here
        base.activeTags = {}; // active tags as id => label
        base.safeExit = true;
        base.activeSearch = {};
        base.activeSearchChildren = {};

        window.onbeforeunload = function() {
            if (!base.safeExit) return '';
        }
        /**
        * Initializes the Plugin. Ajax call for the ontology json (to base.taxonomy), and calls to buildMiller and clickEvents. If we are in index mode
        * Ajax call for the index Json file (to base.tags) and calls to buildTags. Calls initTagit.
        * @method base.init
        */
        base.init = function(){     
              $.ajax({
                url: base.options.json,
                dataType: 'text',
                success: function(tax){
                  base.taxonomy = $.parseJSON(tax);
                  base.buildMiller();
                  base.clickEvents();
                  if (base.options.ontologyMode == 'add') { // initialize tags here
                      $.ajax({
                        url: base.options.jsonIndex,
                        dataType: 'text',
                        success: function(tax2){
                          base.tags = $.parseJSON(tax2);
                          base.buildTags();
                        }

                      });
                    initTagit();
                  }
                  if (base.options.ontologyMode == 'search') { // load index file
                      $.ajax({
                        url: base.options.jsonIndex,
                        dataType: 'text',
                        success: function(tax2){
                          base.tags = $.parseJSON(tax2);
                          base.initSearchHash();
                          base.buildHtml();
                        }

                      });
                    
                  }
                }
              }); 
        };

        /**
        * inits the tagit plugin. After remove of a tag, cleans base.activeTags, and selects the node of the tag that was removed,
        * and on click of a tag selects the node of the ontology.
        * @method initTagit
        */
        initTagit = function() {
          //https://github.com/aehlke/tag-it

          var takeSplit = function (ui) {
            split = ui.tag.attr('class').split('tag-');
            split = split[1].split(' ');
            split = split[0];
            return split;
          }

          var selectNode = function (split) {
            if (base.currentActive != split) {
              base.currentActive = split;
              base.buildMiller();
            }
          }

          $("#myTags").tagit({
              afterTagRemoved: function(event, ui) {
                split = takeSplit(ui);
                delete base.activeTags[split];
                selectNode(split);
                base.safeExit = false;
              },
              onTagClicked: function(event, ui) {
                split = takeSplit(ui);
                selectNode(split);
              },
              allowDuplicates: true
            });
          $("li.tagit-new input.ui-widget-content").prop('disabled', true);
        }

        /**
        * Saves the index json file. Can save for multiple videos and one video. For one video replaces tags[id] with activeTags,
        * and for multiple videos, for each extends the tags with activeTags. Can add a check for overriding here later.
        * Saves index json to base.options.save
        * @method Selector(.save-tags)
        */
        $(".save-tags").click(function( event ) {
          if (base.options.videoID.length > 1) { // save to multiple
            base.options.videoID.forEach(function(value) {
                if (typeof base.tags[value] != 'undefined') {
                  $.extend(base.tags[value], base.activeTags);
                }
                else {
                  base.tags[value] = base.activeTags;
                }
                
            });
          }
          else { // save to one
            value = base.options.videoID[0];
            base.tags[value] = base.activeTags;
          }
          $.ajax ({
              type: "POST",
              async: false,
              url: base.options.save,
              data: { data: JSON.stringify(base.tags),
                      id: base.options.id,
                      videoID: base.options.videoID
                     },
              success: function () {
                base.safeExit = true;
                alert("Saved"); 
              },
              failure: function() {alert("Error");}
          });
        });

        /**
        * Saves the ontology to base.options.save
        * @method Selector(.save-node)
        */
        $(".save-node").click(function( event ) { 
          base.ajax_save();
        });

        base.ajax_save = function(){
          $.ajax ({
            type: "POST",
            async: true,
            url: base.options.save,
            beforeSend: function(){
              $(".main-top-menu").append('<li><div class="loading_indicator_inline"></div></li>');
            },
            data: { 
              data: JSON.stringify(base.taxonomy),
              id: base.options.id,
              title: $('#vmb_presentationbundle_ontology_name').val(),
              topic: $('#vmb_presentationbundle_ontology_topic').val()
            },
            success: function(data) {
              base.options.id = data;
              base.safeExit = true;
              // $(".main-top-menu") remove loading indicator
              $(".save-node").removeClass('btn-primary').addClass('btn-info');
              $(".save-node").removeClass('glyphicon-floppy-disk').addClass('glyphicon-floppy-saved');
              setTimeout(function(){ 
                $(".save-node").removeClass('btn-info').addClass('btn-primary');
                $(".save-node").removeClass('glyphicon-floppy-saved').addClass('glyphicon-floppy-disk');
              }, 2000);
            } ,
            failure: function () {
              // $(".main-top-menu") remove loading indicator
              $('.main-top-menu').append('<li><div class="alert alert-danger alert-dismissible fade in" role="alert" style="padding: 6px 12px"><strong>Error while saving.</strong></div></li>');
              setTimeout(function(){
              $('.alert-danger').alert('close');
              }, 3000);
            }
          });
        }
        // Expose function
        $.fn.taxonomyBrowser.ajax_save = base.ajax_save;


        $(".search-button").click(function( event ) {
          if (base.finalSearchVideo.length > 0) {
            $("#search-value").val(base.finalSearchVideo.toString());
            $("#search-form").submit();
          }
          else {
            alert("No videos selected");
          }
        });


        /**
        * Used for indexing, if there is a string attribute opens a dialog to write the text. Adds the tag, or remove it if it is empty
        * @method Selector(.dialog-add-text)
        */
        $( '#dialog-add-text' ).dialog({
          autoOpen: false,
          height: 250,
          width: 350,
          modal: true,
          buttons: [{
            text : "OK",
            id: "accept-dialog-add-text",
            click: function() {
              id = base.currentActive;
              if ($('#text-value').val().length > 0) {
                base.addTag(id, $('#text-value').val());
              } 
              else if (typeof base.activeTags != 'undefined') { // we want to empty it
                base.removeTag(id);
              }
              $( this ).dialog( "close" );
            }
          },
          {
            text: "Cancel",
            click: function() {
              $( this ).dialog( "close" );
            }
          }],
          open: function() {

            $("#dialog-add-text").keypress(function(e) {
              if (e.keyCode == $.ui.keyCode.ENTER && $('#dialog-add-text').dialog("isOpen")) {
                e.preventDefault();
                $("#accept-dialog-add-text").trigger("click");
              }
            });
          },
          close: function() {
            $('#text-value').val('');
          }
        }); // end dialog-add-text

        /**
        * dialog form to add or edit a node. puts parameters to base.nodeInfo, and calls nodeEdit. 
        * @method Selector(.dialog-form)
        */
        $( "#dialog-form" ).dialog({
          autoOpen: false,
          height: 400,
          width: 350,
          modal: true,
          buttons: [{
            text: "OK",
            id: "accept-dialog-form",
            click: function() {
              bValid = true;
              if ($('#name').val().length < 1) {
                bValid = false;
              }
              if ( bValid ) {
                base.nodeInfo.label = $('#name').val();
                base.nodeInfo.type = $('#type').val();
                nodeEdit();
              }
              $( this ).dialog( "close" );
            }
          },
          {
            text: "Cancel",
            click: function() {
              $( this ).dialog( "close" );
            }
          }],
          open: function() {
            $("#dialog-form").keypress(function(e) {
              if (e.keyCode == $.ui.keyCode.ENTER && $('#dialog-form').dialog("isOpen")) {
                $("#accept-dialog-form").trigger("click");
              }
            });
          },
          close: function() {
            $('#name').val('');
            $('#parent').val('');

          }
        });
        // end dialog box

        /**
        * open the dialog box for add or edit a node. Initializes nodeInfo, and prepares fields.
        * @method nodeDialog
        * @param {Integer} id id of the node that is about to edit or add
        * @param {String} edit id defined we want to edit else new node
        */
        base.nodeDialog = function (id, edit) {
          if(typeof id == 'undefined') return;
          if (typeof edit == 'undefined') { // new node
            $("span.ui-dialog-title").text("Add new node");
            if (typeof base.taxonomyHash[id] != 'undefined') $('#parent').val(base.taxonomyHash[id].label);
            if (typeof base.lastID == 'undefined') { // the bigger id in json file
              base.lastID = 0;
              $.each(base.taxonomy, function(i, item) { // find bigger id
                tempID = parseInt(base.taxonomy[i].id);
                if (tempID > base.lastID) {
                  base.lastID = tempID;
                }
              });
            }

            tempID = base.lastID;
            tempID++;
            tempParent = id;
            tempEdit = null;
          }
          else { // edit node
            $("span.ui-dialog-title").text("Edit node");
            tempID = id;
            tempParent = base.taxonomyHash[id].parent;
            if (tempParent != null) {
              $('#parent').val(base.taxonomyHash[tempParent].label);
            }
            $('#name').val(base.taxonomyHash[id].label);
            tempEdit = 'edit';
          }

          tempID = tempID.toString();
          tempType = 'simple';
          base.nodeInfo = {"id" : tempID, "parent" : tempParent, "edit" : tempEdit};
          if (typeof base.taxonomyHash[id] != 'undefined') { // change later
            if (typeof base.taxonomyHash[id].type != 'undefined') {
              tempType = base.taxonomyHash[id].type;
            }
            
            $("#type").val(tempType).change(); // get type for the selected node
            $("input:checkbox").prop('checked', false); // clear dates

            if (typeof base.taxonomyHash[id].dateEnd != 'undefined') { // initialize dateEnd boxes
              for (var i in base.taxonomyHash[id].dateEnd) {
                value = base.taxonomyHash[id].dateEnd[i];
                $("input[name='date-end[]'][value='"+value+"']").prop('checked', true);
              }
            }
          }         
          
          $( "#dialog-form" ).dialog( "open" );
        }

        /**
        * function to update the taxonomy, with nodeInfo
        * @method nodeEdit
        */
        nodeEdit = function () {
          if (base.nodeInfo.edit == null) { // new node
            base.taxonomy.push({"id" : base.nodeInfo.id, "label" : base.nodeInfo.label, 
            "parent" : base.nodeInfo.parent, "type" : base.nodeInfo.type});
            base.lastID = base.nodeInfo.id;
            base.currentActive = base.nodeInfo.parent;
            if (base.currentActive == null) base.currentActive = "0";
          }
          else { // edit node
            var lim = base.taxonomy.length;
            for (var i = 0; i < lim; i++){
             if (base.taxonomy[i].id == base.nodeInfo.id){ 
                 base.taxonomy[i] = {"id" : base.nodeInfo.id, "label" : base.nodeInfo.label, 
                "parent" : base.nodeInfo.parent, "type" : base.nodeInfo.type};
                 break;
             }
            }
            base.currentActive = base.nodeInfo.id;
          }
          base.buildMiller();
          base.nodeInfo = {};
          base.safeExit = false;
        }

        /**
        * dialog form to confirm delete of nodes. 
        * @method Selector(.dialog-confirm)
        */
         $( "#dialog-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:200,
            modal: true,
            buttons: {
              "Delete": function() {
                base.nodeDelete($(this).data('id'), true);
                $( this ).dialog( "close" );
              },
              Cancel: function() {
                $( this ).dialog( "close" );
              }
            }
          });

        /**
        * returns an array with the node and all the subchildren
        * @method nodeDeleteChildren
        * @param {Integer} id 
        */

         nodeDeleteChildren = function (id) {
            helper = [id];
            nodesToDelete = [];

            do {
              remove = helper.shift();
              if (typeof base.taxonomyChildren[remove] != 'undefined') {
                helper = helper.concat(base.taxonomyChildren[remove]); // duplicates? not with the current tree
              }
              nodesToDelete.push(remove);
            } while (helper.length > 0);
            
            return nodesToDelete;
         }


        /**
        * deletes the node with the specific id, from taxonomy
        * @method nodeEdit
        * @param {Integer} id 
        */
        base.nodeDelete = function (id, confirm) {

          if (id == null || id == "0") return;

          if (typeof confirm == 'undefined') { // open confirm  dialog, with info on how many nodes we delete
            p = nodeDeleteChildren(id);
            $("#node-delete-number").html(p.length);
            $( "#dialog-confirm" ).data('id', id).dialog( "open" );
            return;
          }

          start = base.taxonomyHash[id].parent;

          console.log(base.taxonomy.length);
          p = nodeDeleteChildren(id);
          $.each(p, function(i,item) {
            delete base.taxonomyHash[item];
          });
          base.changeTaxonomy();
          console.log(base.taxonomy.length);
          
            if (start == null || start == "0") {
              if (typeof base.taxonomyChildren[0] != 'undefined' && base.taxonomyChildren[0].length > 0) {
                start = 0;
              }
              else {
                base.buildMiller();
                return;
              }
            } 
            if (base.taxonomyChildren[start].length > 1) {
              // select first
            if (base.taxonomyChildren[start][0] != base.currentActive) start = base.taxonomyChildren[start][0];
            else start = base.taxonomyChildren[start][1];
            }
            base.currentActive = start;
            base.buildMiller();
            base.safeExit = false;
        }

        /**
        * moves the current active node according to direction from the keyboard
        * @method base.move
         @param {String} direction
        */
        base.move = function(direction) {
          switch (direction) {
            case 'left':
            if (typeof base.taxonomyHash[base.currentActive] != 'undefined' && base.taxonomyHash[base.currentActive].parent != null) {
              base.currentActive = base.taxonomyHash[base.currentActive].parent;
              base.buildMiller();
            }
            else {
              base.currentActive = "0";
              base.buildMiller();
            }
            break;
            case 'right':
            if (typeof(base.taxonomyChildren[base.currentActive]) != 'undefined') {
              base.currentActive = base.taxonomyChildren[base.currentActive][0];
              base.buildMiller();
            }
            break;
            case 'up':
            if ( typeof $(".currentActive").prev().attr('data-id') != 'undefined') {
              base.currentActive = $(".currentActive").prev().attr('data-id');
              base.buildMiller();
            }
            break;
            case 'down':
            if ( typeof $(".currentActive").next().attr('data-id') != 'undefined') {
              base.currentActive = $(".currentActive").next().attr('data-id');
              base.buildMiller();
            }
            break;
          }
        }

        /**
        * helper function to check the ontology mode outside the class
        * @method isOntologyMode
        * @param {String} mode edit or add
        */
        base.isOntologyMode = function(mode) {
          if (base.options.ontologyMode == mode) return true;
          return false;
        }

        /**
        * selects a tag, or deletes it, if it is already selected.
        * @method selectTag
        * @param {Integer} id tag id that represents node id of the ontology
        */
        base.selectTag = function(id) {
          if (typeof id == 'undefined' || id == "0") return;
          if (typeof base.taxonomyChildren[id] != 'undefined') return;

          // check here for type (simple etc.)
          if (typeof base.taxonomyHash[id].type != 'undefined' && 
            base.taxonomyHash[id].type != 'simple') {
            if (base.activeTags[id]) {
              $('input#text-value').val(base.activeTags[id]);
            }

            if (base.taxonomyHash[id].type == "multiple") {
              $("#text-value").val('');
              $("#text-value").hide();
              $("#multiple-text-value").show();
              $("#multiple-text-value").tagit({
                singleField: true,
                //allowDuplicates: true,
                allowSpaces: true,
                removeConfirmation: true,
                singleFieldNode: "#text-value"
              });
              $("#multiple-text-value").tagit("removeAll");
              if (typeof base.activeTags != 'undefined' && typeof base.activeTags[id] != 'undefined') {
                tags = base.activeTags[id].split(",");
                $.each(tags, function (i, tag) {
                  $("#multiple-text-value").tagit("createTag", tag);
                });
              }
            }
            else {
              $("#multiple-text-value").hide();
              $("#text-value").show();
            }

            switch (base.taxonomyHash[id].type) {
              case "text":
                $("#text-value-label").html("Value Attribute");
                $( "#dialog-add-text" ).dialog( "open" );
                return;
                break;
              case "date":
                $("#text-value-label").html("Date");
                $( "#dialog-add-text" ).dialog( "open" ); // temp dates are treated like text
                return;
                break;
              case "dateInterval":
                $("#text-value-label").html("Date Interval");
                 $( "#dialog-add-text" ).dialog( "open" ); // temp dates are treated like text
                return;
                break;
              case "multiple":
                $("#text-value-label").html("Multiple Value Attribute");
                 $( "#dialog-add-text" ).dialog( "open" );
                 $("#dialog-add-text").css("min-height", "250px");
                return;
                break;
            }
          }

          if (typeof base.activeTags[id] == 'undefined') { // add it to tags
            base.addTag(id, base.taxonomyHash[id].label);
          }
          else { // remove it
            base.removeTag(id);
          }
        }
        
        /**
        * adds the tag
        * @method base.addTag
        * @param {Integer} id of the tag to be added
        * @param {Integer} text value of the tag to be added
        */
        base.addTag = function(id, text) {
          base.safeExit = false;
          base.activeTags[id] = text;
          if ($(".tag-"+id).length == 0) {
            $("#myTags").tagit("createTag", base.taxonomyHash[id].label, "tag-"+id);
          }
          base.buildHtml();            
        }
        /**
        * removes the tag
        * @method base.removeTag
        * @param {Integer} id of the tag to be removed
        */
        base.removeTag = function(id) {
            base.safeExit = false;
            $("li.tag-"+id).remove();
            delete base.activeTags[id];
            base.buildHtml();
        }

        /**
        * Used at the init of tags, does not put tags if multiple videos are selected
        * @method base.buildTags
        */
        base.buildTags = function () {
          if (base.options.videoID.length > 1) return; // no build tags
          if (typeof base.tags[base.options.videoID[0]] != 'undefined') { // build tags
            base.activeTags = base.tags[base.options.videoID[0]];
            $(".tagit-choice").remove();
            $.each(base.activeTags, function(id, value) {
              if (typeof base.taxonomyChildren[id] == 'undefined' && typeof base.taxonomyHash[id] != 'undefined') {
                // second typeof added for bug if we delete a node in ontology,  that we have an index for it
                $("#myTags").tagit("createTag", base.taxonomyHash[id].label, "tag-"+id);
              }
            });
            base.buildHtml();
          } 
        }

        // expose functions
        $.fn.taxonomyBrowser.nodeDialog = base.nodeDialog; 
        $.fn.taxonomyBrowser.nodeDelete = base.nodeDelete;
        $.fn.taxonomyBrowser.move = base.move; 
        $.fn.taxonomyBrowser.isOntologyMode = base.isOntologyMode; 
        $.fn.taxonomyBrowser.selectTag = base.selectTag;

        /**
        * Basic method used to build the html
        * @method base.buildHtml
        */
        base.buildHtml = function() {
          len = base.taxonomyColumns.length;
          if (len < 4) len = 4;
          width = 100/len;
          $(".miller--column--wrap").empty();
          $(".miller--placeholder").remove();
          left = 0;
          $.each(base.taxonomyColumns, function(i, item) { 
            dom = $( '<div class="miller--column" data-depth="'+i+'" tabindex="'+i+'" style="height: 450px; width: '+width+'%;"></div>' ).appendTo( ".miller--column--wrap" );
            dom = $('<div class="miller--terms--container"></div').appendTo(dom);
            dom = $('<ul class="terms"></ul>').appendTo(dom);
            $.each(item, function(key, value) {
              li = $('<li class="term"  data-id="'+value+'"></li>').appendTo(dom);
              ahref = $('<a href="#"></a>').appendTo(li);
              span = '<span class="title">'+base.taxonomyHash[value].label;
              if (typeof base.activeTags[value] != 'undefined' && base.taxonomyHash[value].type != 'simple') { // temp the simple
                span += '<br /><em style="color:blue">'+ base.activeTags[value] +'</em>'
              }
              span += '</span>';
              $(span).appendTo(ahref);
              em = $('<em class="icon"></em>').appendTo(ahref);
              if(typeof base.taxonomyChildren[value] != 'undefined' && base.options.ontologyMode != 'search'){
                li.addClass('has-children');
                em.addClass('icon-arrow');
              }
              else if (base.options.ontologyMode == 'search') { // search
                if (typeof base.activeSearch[value] != 'undefined') {
                  li.addClass("active");
                }

                if (typeof base.liEdges[value] != 'undefined') {
                  li.css("border-top", "3px solid black")
                }
                if (value == item[item.length - 1]) {
                  li.css("border-bottom", "3px solid black")
                }

                li.hover(
                  function (){
                    if (base.taxonomyHash[value].parent != null) {
                    $("ul").find('li[data-id="' + base.taxonomyHash[value].parent + '"]').toggleClass("currentActive");  
                    }
                  });
                if (typeof base.searchVideo != 'undefined') {
                  if (typeof base.searchVideo[value] != 'undefined') {
                    em.html(base.searchVideo[value].length);
                  }
                  else {
                    em.html("0");
                  }
                }
                


              } // end if search
              if (base.options.ontologyMode == 'add' && typeof base.activeTags[value] != 'undefined') {
                em.addClass('icon-tick');
              }
            });
            dom = $('<div class="miller--placeholder"></div>').appendTo('.miller-container');
            dom = $('<div class="miller--placeholder--column" style="height: 450px; width: '+width+'%; left: '+left+'%;"></div>').appendTo(dom);
            dom = $('<div class="miller--placeholder__background"></div>').appendTo(dom);
            left += width;
          });

          if (typeof base.taxonomyHash[base.currentActive] != 'undefined') {
            i = base.taxonomyHash[base.currentActive].id;
            $("ul").find('li[data-id="' + i + '"]').addClass("currentActive");
            do {
              $("ul").find('li[data-id="' + i + '"]').addClass("active");
              i = base.taxonomyHash[i].parent;
            } while (i != null)
          }
          $('.js-focus').removeClass('js-focus');
          $(".currentActive").closest(".miller--column").addClass('js-focus');
          
          dom = $('<div style="background-color: #F3F3F4; position: absolute; height: 450px; width: 0.5%; left: -1%;"></div>').appendTo('.miller-container');
          if (base.currentActive == null || base.currentActive == "0") {
            dom.css({"background-color" : "#CE8424"});
          }
          else {
            dom.css({"background-color" : "#F3F3F4"});
          }
        }

        /**
        * Basic method used to update taxonomy and values based on taxonomy. Calls buildHtml after
        * @method base.buildMiller
        */
        base.buildMiller = function () {

          if (typeof base.currentActive == 'undefined') base.currentActive = 1;
          base.changeTaxonomy('hash');

          i = base.currentActive;
          if (typeof base.taxonomyHash[i] == 'undefined') { // fixed
            i = 0;
            base.currentActive = 0;
          }

          if (base.options.ontologyMode == 'search') { // we define different taxonomyColumns for search
            base.buildSearchMiller(base.currentActive);
            return;
          }

          base.taxonomyColumns = [];
          while (i != "0") {
            i = base.taxonomyHash[i].parent;
            if (i==null) i = "0";
            base.taxonomyColumns.push(base.taxonomyChildren[i]);
          }
          base.taxonomyColumns.reverse();
          if (typeof base.taxonomyChildren[base.currentActive] != 'undefined') {
            base.taxonomyColumns.push(base.taxonomyChildren[base.currentActive]);
          }


          base.buildHtml();
        } // end buildMiller

        /**
        * Basic method used to update taxonomy and values based on taxonomy. Calls buildSearchHtml after. Used  for search (ontologyMode)
        * @method base.buildSearchMiller
        */
        base.buildSearchMiller = function (active) {
          base.currentActive = -1;
          base.activeSearch["0"] = 0;
          // check if it is selected
          if (typeof base.activeSearch[active] == 'undefined') {
            // select it here
            column = $("ul").find('li[data-id="' + active + '"]').closest(".miller--column").data('depth');
            if (typeof column != 'undefined') {
              base.activeSearch[active] = column+1;
              parent = active;
              do {
                parent = base.taxonomyHash[parent].parent;
                if(parent == null) parent = 0;

                if (typeof base.activeSearchChildren[parent] == 'undefined') base.activeSearchChildren[parent] = [];
                base.activeSearchChildren[parent].push(active);
              } while (parent != '0');
            }
            
          }
          else { 
          // unselect to the end of the tree
              if (typeof base.activeSearchChildren[active] != 'undefined') {
                $.each(base.activeSearchChildren[active], function (key,value) {
                  if(typeof base.activeSearch[value] != 'undefined') {
                    delete base.activeSearch[value];
                  }
                });
              }
              delete base.activeSearch[active];
          }
          base.taxonomyColumns = [];
          base.liEdges = {};
        

          $.each(base.activeSearch, function(item, column) {
            if (typeof base.taxonomyColumns[column] == 'undefined') {
              base.taxonomyColumns[column] = [];
            }
            if (typeof base.taxonomyChildren[item] != 'undefined') {
              $.merge(base.taxonomyColumns[column], base.taxonomyChildren[item]);
              base.liEdges[base.taxonomyChildren[item][0]] = "top";
            
            }

          });
          /*
          if (base.taxonomyHash[active].type != 'simple' && 
            typeof base.taxonomyChildren[active] == 'undefined' &&
            typeof base.searchHash[active] != 'undefined') {
            base.searchValueHash[active] = {};
            $.each(base.searchHash[active], function (video,value) {
              if (typeof base.searchValueHash[active][value] == "undefined") base.searchValueHash[active][value] = [];
              base.searchValueHash[active][value].push(video);
            });
          }
          */

          //console.log(base.taxonomyColumns);
          //console.log(active);
          //console.log(base.searchHash);
          base.finalSearchVideo = undefined;
          if (typeof base.searchVideo != 'undefined') {
            $.each(base.activeSearch, function (i,value) {
              if (i == "0") return; // continue jquery
              if (typeof base.searchVideo[i] == 'undefined') {
                base.finalSearchVideo = [];
                return false; // break loop
              }
              else { // find duplicates of the two arrays (better algorithm?)
                if (typeof base.finalSearchVideo == 'undefined') base.finalSearchVideo = base.searchVideo[i];
                temp = [];
                $.each(base.finalSearchVideo, function (k, firstVal) {
                  $.each(base.searchVideo[i], function (j, secondVal) {
                    if (firstVal == secondVal) {
                      temp.push(firstVal);
                      return false; // break from inner loop
                    }
                  });
                });
                base.finalSearchVideo = temp;
              }
            });
          }
          if (typeof base.finalSearchVideo == 'undefined') base.finalSearchVideo = [];

            $("#video-number").html(base.finalSearchVideo.length);
            if (base.finalSearchVideo.length == 1) $("#video-text").html("video was");
            else $("#video-text").html("videos were");
            //console.log(base.finalSearchVideo);

          base.buildHtml();

        } // end buildSearchMiller

        base.initSearchHash = function() {
          base.searchValueHash = {};
          base.searchHash = {};
          if (typeof base.tags != 'undefined') {
            $.each(base.tags, function(video,tags) {
              $.each(tags, function(node, value){
                if (typeof base.searchHash[node] == 'undefined') base.searchHash[node] = {};
                base.searchHash[node][video] = value;
              });
            });
            base.searchVideo = {};
            // complexity fix bug?
            i=0;
            $.each(base.searchHash, function(node, videos) {
              $.each(videos, function(video,value){
                parent = node;
                
                do {
                  i++;
                  if (typeof base.searchVideo[parent] == 'undefined') base.searchVideo[parent] = [];
                  if (base.searchVideo[parent].indexOf(video) < 0) { // complexity?
                    base.searchVideo[parent].push(video); 
                  }
                  parent = base.taxonomyHash[parent].parent;
                } while(parent != null);

              });
            });

          }
  
        }

        /**
        * Add events to the taxonomy browser
        * @method base.clickEvents
        */

        base.clickEvents = function(){
          /*
          Click Events for Terms
           */    

          base.$el.on('click', 'li', function(e){ 
              selected = $(this).attr('data-id');
              if (selected == base.currentActive) {
                // hack to catch double click, and trigger enter key
                var e = jQuery.Event("keydown");
                e.which = 13;
                $("body").trigger(e);
              }
              else {
                base.currentActive = selected;
                base.buildMiller();
              }
            e.preventDefault();
          });

        };

        /**
          * converts base.taxonomy to base.taxonomyHash, and vice versa. Init base.taxonomyChildren
          * @method base.changeTaxonomy
        */

        base.changeTaxonomy = function (hash) {
          if (typeof hash != 'undefined') { 
            // update base.taxonomyHash and base.taxonomyChildren here
            base.taxonomyHash = {};
            base.taxonomyChildren = {};
            
            $.each(base.taxonomy, function(i, item) { // update hash taxonomy array to keep track on id
              base.taxonomyHash[base.taxonomy[i].id.toString()] = base.taxonomy[i];
              temp = base.taxonomy[i].parent;
              if (temp == null) temp = 0;
              if (typeof base.taxonomyChildren[temp] == 'undefined') base.taxonomyChildren[temp] = [];
              base.taxonomyChildren[temp].push(base.taxonomy[i].id);
            });
            return;
          }

          // update base.taxonomy here. Used rare
          base.taxonomy = [];
          $.each(base.taxonomyHash, function(i, item) {
            base.taxonomy.push(item);
          });
          return;
        };
        
        /**
         *  Initializer
         */

        base.init();

    };
    
    
    // Default Options

    $.taxonomyBrowser.defaultOptions = {        
        source: 'json',
        json: 'json/taxonomy.json', 
        rootvalue: null, 
        columnclass: '.miller--column', 
        columns: 3, 
        columnheight: 400,
        start: '' /* ID or index of the Taxonomy Where you want to start */,
        template: 'taxonomy_terms'
    };

    $.fn.sanitize = function(){
      
      return $(this).text().replace(/[^a-z0-9]+/ig, "-").toLowerCase();

    };

    /**
    * jQuery Plugin method
    * @method fn.taxonomyBrowser
    */
    
    $.fn.taxonomyBrowser = function(options){
        return this.each(function(){
            (new $.taxonomyBrowser(this, options));
        });
    };
    
    // This function breaks the chain, but returns
    // the taxonomyBrowser if it has been attached to the object.
    $.fn.gettaxonomyBrowser = function(){
        this.data("taxonomyBrowser");
    };

    /*
      Element Cache
     */
    
    $.fn.taxonomyBrowser.elementCache = [];


/*
        base.initializeDrop = function(){
          // droppable
          $( ".draggable" ).draggable({ 
            revert: true,
            //helper: 'clone',
            appendTo: '.miller-container',
            zIndex: 1000,
            reverDuration: 0,
            scroll: true,
            cursorAt: {top: 20, left:10},
            cursor: "hand", 
            start: function( event, ui ) {
              //ui.helper.css({'position':'absolute','z-index':'10000'});
              //ui.helper.css({'padding':'20px', 'color':'red'});
            },
            stop: function( event, ui ) {
              //ui.helper.css({'position':'relative','z-index':'1'});
            }
           });

           $( ".droppable" ).droppable({
            activeClass: "ui-state-default",
            hoverClass: "ui-state-hover",
            drop: function( event, ui ) {
              dropID = $(this).attr('data-id');
              base.currentActive = dragID = ui.draggable.attr('data-id');
              var lim = base.taxonomy.length;
              for (var i = 0; i < lim; i++){
               if (base.taxonomy[i].id == dragID){ 
                   base.taxonomy[i].parent = dropID;
                   break;
               }
              }
              //ui.helper.css({'position':'relative','z-index':'1'});
              //ui.helper.remove();
              base.buildMiller();
            }
          });
        } 
        */
    
})(jQuery, window, document, undefined);
