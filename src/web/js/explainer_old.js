$(document).ready(function() {
	
	
	// Fetch data about the item
	$.ajax({
  		url: www_root + '/translators/item.php',
  		dataType: 'json',
  		data: {query : uid, search_type : 'recordId', start : '0', limit : '1'},
  		async: false,
  		success: function(data){
  			
			var this_details = match_values(data);

			
			 draw_item_panel(this_details);
			
    }
	});

	$('#viewerCanvas').css('height', stackheight*.9).css('width', stackheight*.75);

	$(window).resize(function() {
		stackheight = $(window).height();
		$('.stackview').css('height', stackheight);
		$('#viewerCanvas').css('height', stackheight*.9).css('width', stackheight*.75);
	});

	if(uniform_count > 0) {
		$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'ut_id', query: uniform_id, ribbon: $('#uniform').text()});
		$('#uniform').addClass('selected-button');
	}
	else if (loc_call_num_sort_order) {
		$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'loc_call_num_sort_order', id: loc_call_num_sort_order, ribbon: 'Infinite Stack: the library arranged by call number'});
		$('#callview').addClass('selected-button');
	}
	else if(anchor_subject !== '') {
		$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'lcsh', query: anchor_subject, ribbon: anchor_subject});
		$('.subject-button:first').addClass('selected-button');
	}
	else if(anchor_subject === '') {
		$('#fixedstack').text('Sorry, no Library of Congress call number or subject neighborhood found.');
	}
	
	$('.stackview').css('height', stackheight);

	$('.slide-more').live('click', function() {
		$(this).next('.slide-content').slideToggle();
		$(this).find('.arrow').toggleClass('arrow-down');
	});
	
	$('.sms').live('click', function() {

		//find item locations
	
		var location = $(this).parent().find('.callno:first').text();
	
		//build form
		var html = ""; 
		if(location.length>0) {	
			html = "<div id='wrap'><p>" + location + "<br />" + title + "</p><br /><form id='form'><input id='smstitle' type='hidden' value='" + title + "' /><input id='smslibrary' type='hidden' value='" + location + "' /><input id='smsnumber' type='text' size='12' maxlength='12' placeholder='your number' />";
			html += "<select id='smscarrier'><option>Select a Carrier</option>";
			html += "<option value=@txt.att.net>AT&T</option>";
			html += "<option value=@message.alltel.com>Alltel</option>";
			html += "<option value=@myboostmobile.com>Boost</option>";
			html += "<option value=@mobile.mycingular.com>Cingular</option>";
			html += "<option value=@messaging.nextel.com>Nextel</option>";
			html += "<option value=@tmomail.net>T-Mobile USA</option>";
			html += "<option value=@vtext.com>Verizon Wireless</option>";
			html += "<option value=@vmobl.com>Virgin Mobile USA</option></select>";
			html += "</select></form></div>";
		} else {
			html += "<p>Something is amiss, are all the items at HD or networked?</p>";
		}
		launchDialog(html);
	});

    
    function draw_item_panel(item_details) {
		// set our global var
        if(current_school == 'PANO' && item_details.loc_call_num && item_details.loc_call_num[1]){
            loc_call_num_sort_order = item_details.loc_call_num[1];
        }else if(item_details.loc_call_num && item_details.loc_call_num[0]){
            loc_call_num_sort_order = item_details.loc_call_num[0];
        }
        
        
        //make sure that the title is not an array
        title = item_details.title
		
        //uid = item_details.isbn;
        uid = item_details.recordIdentifier;

		// update our window title
		document.title = title + ' | StackLife';

		// store this as an "also viewed"
		$.each(alsoviewed, function(i, item){
          $.ajax({
            type: "POST",
            url: slurl,
            data: "also="+ item + "&id=" + uid + "&function=set_also_viewed",
            success: function(){
            }
          });
        });
        alsoviewed.push(uid);

		// add to recently viewed
		$.ajax({
			type: "POST",
			url: slurl,
			data: "function=session_info&type=set&uid=" + item_details.recordIdentifier,
			async: false
		});
		recentlyviewed += '&recently[]=' + uid;
        
		// replace creator list
		item_details.creators = '';
		if(item_details.authors) {
			var creator_markup_list = [];
			$.each(item_details.authors, function(i, item){
                if(typeof item == 'string'){
                    creator_markup_list.push('<a class="creator" href="../../author/' + item + '">' + item + '</a>');
                }
			});

			item_details.creators = creator_markup_list.join('<span class="divider"> | </span>');
		}

       /** if(item_details.source_record.rsrc_key && item_details.source_record.rsrc_key.length > 0){
            var isArray = Array.isArray || function(obj) {
                return Object.prototype.call(obj) == '[object Array]';
            };

            if (!isArray(item_details.source_record.rsrc_key)){
                item_details.source_record.rsrc_key = [item_details.source_record.rsrc_key];
                item_details.source_record.rsrc_value = [item_details.source_record.rsrc_value];
            };

			$.each(item_details.source_record.rsrc_key, function(i, item){
                    if(item == 'wikipedia_org')
                        item_details.wp_url = item_details.source_record.rsrc_value[i];
                    if(item == 'npr_org_broadcast')
                        item_details.npr_url = item_details.source_record.rsrc_value[i];
			});
		}**/

		item_details.shelfrank = left_pad(item_details.shelfrank);
        
		// Translate a total score value to a class value (after removing the old class)
		$('.shelfRank, .itemData-container, .unpack').removeClass(function (index, css) {
		    return (css.match(/color\d+/g) || []).join(' ');
		});

		$('.shelfRank, .itemData-container, .unpack').addClass('color' + get_heat(item_details.shelfrank));

		// replace google books link
		// get the google books info for our isbn and oclc (and if those are empty, use 0s)
        
		var isbn = item_details.isbn;

		/**var oclc = '';
		if (item_details.id_oclc) {
			oclc = item_details.id_oclc;
		}

		item_details.oclc = oclc;**/

		//var gbsrc = 'http://books.google.com/books?jscmd=viewapi&bibkeys=OCLC:' + oclc + ',ISBN:' + isbn + '&callback=ProcessGBSBookInfo';
        
        var gbsrc = 'http://books.google.com/books?jscmd=viewapi&bibkeys=ISBN:' + isbn + '&callback=ProcessGBSBookInfo';
		$("#gbscript").attr('src', gbsrc);
        
		//GBSArray = ['ISBN:' + isbn, 'OCLC:' + oclc];
        GBSArray = ['ISBN:' + isbn];
		$.getScript($("#gbscript").attr('src'));

                
		if (item_details.lcsh != undefined && item_details.lcsh instanceof Array) {
            a = 0;
            extra_add = [];
			$.each(item_details.lcsh, function(i, item) {
                if(item){
                    if(item instanceof Array){
                        $.each(item, function(b,it){
                            //item_details.lcsh[a] = it.replace(/\.\s*$/, '');
                            extra_add.push(it.replace(/\.\s*$/, ''));
                        });
                    }else{
                        item_details.lcsh[a] = item.replace(/\.\s*$/, '');
                        a = a + 1;
                    }
                }
			});
            item_details.lcsh = item_details.lcsh.concat(extra_add);
            item_details.lcsh = eliminateDuplicatesStrings(item_details.lcsh);
		}

		// Redraw our tags
		drawTagNeighborhood();

		var source = $("#item-template").html();
        item_details.not_noble = (current_school != "NOBLE");
        
        if(item_details.loc_call_num){
            if(item_details.loc_call_num[1] != ""){
                if(isInArray(current_school,["PANO","PANA","PANB","PANC","PANG","PANI","PANK","PANP"])){
                    item_details.loc_call_num_match_school = true;
                }
            }

            if(item_details.loc_call_num[0] != ""){
                if(!isInArray(current_school,["PANO","PANA","PANB","PANC","PANG","PANI","PANK","PANP"])){
                    item_details.loc_call_num_match_school = true;
                }
            }
        }
        
        
		var template = Handlebars.compile(source);
    $('#item-panel').html(template(item_details));

    var source = $("#shelves-template").html();
		var template = Handlebars.compile(source);
    $('#shelves-panel').html(template(item_details));

    //NEED TO FIX THIS!!!
        
    /**$.getJSON(www_root + '/translators/availability.php?id=' + item_details.id_inst, function(data) {
      if(data) {
        var source = $("#availability-template").html();
        var template = Handlebars.compile(source);
        $('#availability-panel').html(template(data));
      }
    });**/
        
    var libs = ['BEVERLY','BUNKERHILL','DANVERS','ENDICOTT','EVERETT','GLOUCESTER','GORDON','LYNNFIELD','LYNN','MARBLEHEAD','MELROSE','MERRIMACK','MIDDLESEX','MONTSERRAT','NOBLE','NORTHSHORE','NORTHERNESSEX','PEABODY','READING','REVERE','SALEM','SALEMSTATE','SAUGUS','STONEHAM','SWAMPSCOTT','WAKEFIELD','WINTHROP','PANO','PANA','PANB','PANC', 'PANG', 'PANI', 'PANK','PANP'];
    
    var pretty_libs = ['BEVERLY','BUNKERHILL','DANVERS','ENDICOTT','EVERETT','GLOUCESTER','GORDON','LYNNFIELD','LYNN','MARBLEHEAD','MELROSE','MERRIMACK','MIDDLESEX','MONTSERRAT','NOBLE','NORTHSHORE','NORTHERNESSEX','PEABODY','READING','REVERE','SALEM','SALEMSTATE','SAUGUS','STONEHAM','SWAMPSCOTT','WAKEFIELD','WINTHROP','PA-OWHL','PA-ADDIS','PANB','PANC', 'PANG', 'PANI', 'PANK','PANP'];
        
     var sc = "";
     for(school in libs){
         sc = sc + "<p><a href='' onclick='changeSchool(&quot;" + libs[school] + "&quot;);'>" + pretty_libs[school] + "</a></p>";
     }
    $("#school").html(sc)

    $("#toc").html('');
    if(item_details.tableOfContents) {
        toc = '<br /> ' + item_details.tableOfContents;
        toc = toc.replace(/--/g, '<br />').replace(/- -/g, '<br />').replace(/-/g, '<br />').replace(/;/g, '<br />').replace(/[\/]/g, '<br />').replace(/[\\]/g, '<br />');
        if(toc) {
            $("#toc").html('<p>' + toc + '</p>')
            $(".toc-title").show();
        }
    } else {
        $(".toc-title").hide();
    }
        
        
    //add summary or abstract if available
    $("#summary").html('');
    if(item_details.abstract) {
        summary = item_details.abstract;
        $("#summary").html('<p>' + summary + '</p>')
        $(".summary-title").show();
    } else {
        $(".summary-title").hide();
    }
        
        
        //NEED TO ADD!!! 
        
		// If we have our first isbn, get affiliate info. if not, hide the DOM element
		if (isbn) {
			/**$.ajax({
				type: "GET",
				url: slurl,
				data: "isbn=" + isbn + "&function=check_amazon",
				success: function(response){
					if(response != 'false') {
					  $('#amzn').attr('href', 'http://www.amazon.com/dp/' + response);
						$('.buy').show();
					} else {
						$('.buy').hide();
					}
				}
		  });**/
            
         $('#amzn').attr('href', 'http://www.amazon.com/gp/search?index=books&linkCode=qs&keywords=' + isbn);
		} else {
			$('.buy').hide();
		}
         
    if(item_details.this_button) {
        $(".reload:contains('" + item_details.this_button + "')").parent().addClass('selected-button');
    }   

	}
    
	// When an item in the stack is clicked, we update the book panel here
	function draw_item_panel_old(item_details) {
	
		// set our global var
		loc_call_num_sort_order = item_details.loc_call_num_sort_order;
		title = item_details.title;
		uid = item_details.id;
		
		// update our window title
		document.title = title + ' | StackLife';

		// store this as an "also viewed"
		$.each(alsoviewed, function(i, item){
      $.ajax({
        type: "POST",
        url: slurl,
        data: "also="+ item + "&id=" + item_details.id + "&function=set_also_viewed",
        success: function(){
        }
      });
    });
    alsoviewed.push(item_details.id);

		// add to recently viewed
		$.ajax({
			type: "POST",
			url: slurl,
			data: "function=session_info&type=set&uid=" + item_details.id,
			async: false
		});
		recentlyviewed += '&recently[]=' + uid;

		// replace creator list
		item_details.creators = '';
		if(item_details.creator && item_details.creator.length > 0) {
			var creator_markup_list = [];
			$.each(item_details.creator, function(i, item){
				creator_markup_list.push('<a class="creator" href="../../author/' + item + '">' + item + '</a>');
			});

			item_details.creators = creator_markup_list.join('<span class="divider"> | </span>');
		}
		
 if(item_details.rsrc_key && item_details.rsrc_key.length > 0) {
			$.each(item_details.rsrc_key, function(i, item){
				if(item == 'wikipedia_org')
				  item_details.wp_url = item_details.rsrc_value[i];
			});
		}

		item_details.shelfrank = left_pad(item_details.shelfrank);

		// Translate a total score value to a class value (after removing the old class)
		$('.shelfRank, .itemData-container, .unpack').removeClass(function (index, css) {
		    return (css.match(/color\d+/g) || []).join(' ');
		});

		$('.shelfRank, .itemData-container, .unpack').addClass('color' + get_heat(item_details.shelfrank));

		// replace google books link
		// get the google books info for our isbn and oclc (and if those are empty, use 0s)
		var isbn = '';
		isbn = item_details.isbn;

		/**var oclc = '';
		if (item_details.id_oclc) {
			oclc = item_details.id_oclc;
		}
		
		item_details.oclc = oclc;**/
		
		var gbsrc = 'http://books.google.com/books?jscmd=viewapi&bibkeys=ISBN:' + isbn + '&callback=ProcessGBSBookInfo';
		$("#gbscript").attr('src', gbsrc);		
		
		GBSArray = ['ISBN:' + isbn];
		$.getScript($("#gbscript").attr('src'));


		// Redraw our tags
		drawTagNeighborhood();

		var source = $("#item-template").html();
		var template = Handlebars.compile(source);
    $('#item-panel').html(template(item_details));
    
    var source = $("#shelves-template").html();
		var template = Handlebars.compile(source);
    $('#shelves-panel').html(template(item_details));
    
   /** $.getJSON(www_root + '/translators/availability.php?id=' + item_details.id_inst, function(data) {
      if(data) {
        var source = $("#availability-template").html();
        var template = Handlebars.compile(source);
        $('#availability-panel').html(template(data));
      }
    });**/
    
    $("#toc").html('');
    if(item_details.tableOfContents) {
        toc = item_details.tableOfContents;
        toc = toc.replace(/--/g, '<br />').replace(/- -/g, '<br />').replace(/-/g, '<br />').replace(/[\/]/g, '<br />').replace(/[\\]/g, '<br />');
        if(toc) {
            $("#toc").html('<p>' + toc + '</p>')
            $(".toc-title").show();
        }
    } else {
        $(".toc-title").hide();
    }
		
		if(item_details.this_button) {
      $(".reload:contains('" + item_details.this_button + "')").parent().addClass('selected-button');
    }

	} //end draw item panel

	// When a new anchor book is selected
	$('.stack-item a').live('click', function(e){
	  var this_details = $(this).parent().data('stackviewItem');
	  var this_button = $('.selected-button').text();
		$.ajax({
  		url: www_root + '/translators/item.php',
  		dataType: 'json',
  		data: {query : this_details.id, search_type : 'id', start : '0', limit : '1'},
  		async: false,
  		success: function(data){
			  var this_details = data.docs[0];
			  data.docs[0].this_button = this_button;
			  
			 
        	draw_item_panel(data.docs[0]);
        
      }
	  });
		$('.active-item').removeClass('active-item');
		$(this).parent().addClass('active-item');
		e.preventDefault();
	});

	$('.stack-button').live('click', function() {
	  $('.selected-button').removeClass('selected-button');
	  $(this).addClass('selected-button');
		var compare = $.trim($(this).attr('id'));
		if(compare === 'recentlyviewed') {
			$('#fixedstack').stackView({url: www_root + '/translators/recently.php?' + recentlyviewed, search_type: 'recently', ribbon: 'You recently viewed these'});
		}
		else if(compare === 'callview') {
			$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'loc_call_num_sort_order', id: loc_call_num_sort_order, ribbon: 'Infinite Stack: the library arranged by call number'});
		}
		else if(compare === 'alsoviewed') {
			$('#fixedstack').stackView({url: www_root + '/translators/also.php', query: uid, search_type: 'also', ribbon: 'People who viewed this also viewed these'});
		}
		else if(compare === 'uniform') {
			$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'ut_id', query: uniform_id, ribbon: 'All editions'});
		}
	});

	$('.subject-button').live('click',function() {
		$('.selected-button').removeClass('selected-button');
	  $(this).addClass('selected-button');
		$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'lcsh', query: $(this).text(), ribbon: $(this).text()});
	});
	
	$('.wp_category-button').live('click',function() {
	  $('.selected-button').removeClass('selected-button');
	  $(this).addClass('selected-button');
		$('#fixedstack').stackView({url: www_root + '/translators/cloud.php', search_type: 'wp_categories', query: $(this).text(), ribbon: $(this).text()});
	});

	$('.tag-button').live('click', function() {
	  $('.selected-button').removeClass('selected-button');
	  $(this).addClass('selected-button');
		$('#fixedstack').stackView({url: www_root + '/translators/tag.php', query: $('span', this).text(), search_type: 'tag', ribbon: $('span', this).text()});
	});

    //
    //	User Generated Content
    //

    $("#book-tags-form").validate({
    	errorPlacement: function(error, element) {
    		error.insertAfter( element.next("input") );
    	},
		messages: {
			bookTags: "tag?"
		},
		submitHandler: function(form) {
			var tags     = encodeURIComponent($('#bookTags').attr('value'));
			$.ajax({
				type: "POST",
				url: slurl,
				data: "tags="+ tags + "&uid=" + uid + "&function=set_book_tag",
				success: function(){
					var phrases = ['Nice!', 'Good one!', 'Woot!', 'Rock n\' roll!', 'Hey thanks.', 'Super cool!', 'Yeah, that seems like a good one-', 'Smart.', 'Keep \'em coming!', 'They say the darkest hour is right before the dawn', 'en fuego!'];
					var number = Math.floor(Math.random()*phrases.length);
					$('#book-tags').attr('value', '');
					$('.book-tag-success span').text(phrases[number]);
					$('.book-tag-success span').fadeIn().delay(750).fadeOut(400);
					drawTagNeighborhood();
				}
			});
			return false;
		}
	});
}); //end document ready

// We heatmap our shelfrank fields based on the scaled value
function get_heat(scaled_value) {
  return scaled_value === 100 ? 10 : Math.floor(scaled_value / 10) + 1;
}

function drawTagNeighborhood(){
	$.getJSON(slurl + "?callback=?&function=fetch_tag_cloud", $.param({ 'uid' : uid }), function(data) {
		$("#tagGraph").empty();
		var tagList = '';
		if(data.tags.length > 0) {
			$.each(data.tags, function(i, val) {
				var percentage = val.freq/val.biggest * 100;
				percentage = Math.round(percentage) + '%';
				tagList += '<li class="tag-button button"><span class="reload">' + val.tag + '</span> (' + val.freq + ')</li>';
			});

			$('#tagGraph').append('<span class="heading">Tags</span><ul>' + tagList + '</ul>');
		}
	});
}

function ProcessGBSBookInfo(booksInfo) {
	$('.button-google').hide();
	$('.button-google-disabled').show();
	for (isbn in booksInfo) {
		var GBSParts = isbn.split(':');
		var bookInfo = booksInfo[isbn];
		if (bookInfo) {
			if ((bookInfo.preview == "full" || bookInfo.preview == "partial") && bookInfo.embeddable) {
				$('.button-google-disabled').hide();
				$('.button-google').css('display', 'block');
				$("a#gviewer").fancybox({
					'onStart' : initialize
				});
			} 
        } 
    }
}

function alertNotFound() {
 	document.getElementById('viewerCanvas').innerHTML = '<p>Sorry, no preview available for this book.</p>';
}

function initialize() {
  var viewer = new google.books.DefaultViewer(document.getElementById('viewerCanvas'));
  viewer.load(GBSArray, alertNotFound);
}

function launchDialog(html){ 
	var $dialog = $('<div class="remove"></div>')
		.html(html)
		.dialog({
			autoOpen: false,
			title: 'Text Book Location',
			modal: true,
			resizable: false,
			width: 450 ,
			buttons: { 'Text me': function() { 
				var data = 'number=' + $('#smsnumber').val();
				data += '&carrier=' + $('#smscarrier').val();
				data += '&library=' + $('#smslibrary').val();
				data += '&title=' + $('#smstitle').val();
				$.ajax({
					url: www_root + "/sl_funcs.php?func=text_call_num",
					type: "get",
					data: data,
					success: function(){
						$('#wrap').html("<p>Done!</p>");
					}
				});
				$(this).dialog('close');
			}} 
		});
	$dialog.dialog('open');
	kill = 0;	
}

// Here we pad any values less than 10 with a 0
function left_pad(value) {
	if (value < 10) {
		return '0' + value;
	}
	return value;
}

function match_values(data){
    
    var subject = [];
    
    this_details = data.record;
    this_details.electronic=false;
    title_nf = "";
    sub_title = "";
    this_details.classification = ["",""];
    this_details.authors = []
    
    this_details.datafield.forEach(function (field){
        
            //Field 650
            //Get list of subjects from book

            if(field['@attributes'].tag == '650'){
                if(field.subfield instanceof Array) {
                    anchor_subject = field.subfield[0];

                    field.subfield.forEach(function(obj){
                        if(!(obj.indexOf("NOBLE") >= 1 || obj.indexOf("OCoLC") >= 1)){
                            subject.push(obj);
                        }
                    });
                }
            }
        
            //field 651 contains geographically located data
        
            if(field['@attributes'].tag == '651'){
                if(field.subfield instanceof Array) {
                    anchor_subject = field.subfield[0];

                    field.subfield.forEach(function(obj){
                        if(!(obj.indexOf("NOBLE") >= 1 || obj.indexOf("OCoLC") >= 1) ){
                            subject.push(obj);
                        }
                    });
                }
            }
            
            if(field['@attributes'].tag == '245'){
                if(field.subfield instanceof Array && field.subfield.length >= 2){
                    sub_title = field.subfield[1];
                    title_nf = field.subfield[0];
                }else{
                    title_nf = field.subfield;
                }
                
                if(title_nf instanceof Array){
                    title_nf = title_nf.join("");
                }
            }
            
            //test feature to render lightning logo in front of electronic resources
            if(field['@attributes'].tag == '655'){
              if(field.subfield instanceof Array){
                  field.subfield.forEach(function(each){
                       if(each == 'Electronic books.'){
                           this_details.electronic = true;
                       }
                  });
                }else if(field.subfield == 'Electronic books.'){
                    this_details.electronic = true;
                }
            }
            
            //set up classification using the 050 tag
            if(field['@attributes'].tag == '050' && this_details.classification){
                if(field.subfield instanceof Array){
                  field.subfield.forEach(function(each){
                       this_details.classification[0] = this_details.classification[0] + each + " ";
                  });
                }else{
                    this_details.classification[0] = field.subfield;
                }
            }
            
            //we're setting classification to be an array now, so it can store both the 092 and 050 fields
            if(field['@attributes'].tag == '092'){
                if(field.subfield instanceof Array){
                  field.subfield.forEach(function(each){
                       this_details.classification[1] = this_details.classification[1] + each + " ";
                  });
                }else{
                    this_details.classification[1] = field.subfield;
                }
            }
            
            if(field['@attributes'].tag == '260'){
                place = field.subfield[0];
                this_details.pub_location = place;
                this_details.publisher = field.subfield[1];
                this_details.pub_date = field.subfield[2];
            }
            
            if(field['@attributes'].tag == '901'){
                this_details.recordIdentifier = field.subfield[0];
            }
            
            if(field['@attributes'].tag == '020'){
                if(field.subfield instanceof Array){
                    this_details.identifier = field.subfield[0].replace(/\s.*/,"");
                }else{
                    this_details.identifier = field.subfield.replace(/\s.*/,"");
                }
            }
            
            if(field['@attributes'].tag == '100'){
                if(field.subfield instanceof Array){
                    this_details.authors = this_details.authors.concat(field.subfield[0]);
                }else{
                    this_details.authors = this_details.authors.concat(field.subfield);
                }
            }
        
            if(field['@attributes'].tag == '700'){
                if(field.subfield instanceof Array){
                    this_details.authors = this_details.authors.concat(field.subfield[0]);
                }else{
                    this_details.authors = this_details.authors.concat(field.subfield);
                }
            }
    });
    console.log(this_details);
    //Try to remove some unneeded stuff to lighten computational load!
    //delete this_details.datafield;
    
    //Setting up some variables to make it easier for templating
    this_details.lcsh = subject;
    this_details.title = title_nf.replace(/\//g,"");  
    //.replace(/\belectronic\b/, "").replace(/\bresource\b/,"").replace(/[\[\]']+/g,"")
    //append subtitle to title
    this_details.title = this_details.title + " " + sub_title;
    
    if(!this_details.electronic){
        if(this_details.classification){
            this_details.loc_call_num = this_details.classification;
            loc_call_num_sort_order = this_details.loc_call_num;
        }
    }
   
    //---------------
    // Shelfrank Beta
    //---------------
    this_details.circ_count = this_details.shelfrank;
    if(this_details.shelfrank >= 400){
        this_details.shelfrank=100;
    }else{
        this_details.shelfrank = Math.floor(this_details.shelfrank/4);
    }
    
    this_details.title_link_friendly = title_nf.toLowerCase().replace(/[^a-z0-9_\s-]/g,"");
    this_details.title_link_friendly = this_details.title_link_friendly.replace(/[\s-]+/g, " ");
    this_details.title_link_friendly = this_details.title_link_friendly.replace(/\s+$/g, "");
    this_details.title_link_friendly = this_details.title_link_friendly.replace(/[\s_]/g, "-");
    
    
    //add noblenet permalink support
    this_details.noble_link = "http://evergreen.noblenet.org/eg/opac/record/" + this_details.recordIdentifier;

    if(this_details.identifier){
        link = "../" + this_details.title_link_friendly + "/" + this_details.recordIdentifier;   
        isbn = this_details.identifier;
        this_details.id = this_details.recordIdentifier; 
    }else{
        //NEED TO FIX
        this_details.identifier = "N/a";
        isbn = this_details.identifier;
        this_details.id = this_details.recordIdentifier; 
        link = "../" + this_details.title_link_friendly + "/3631519"; 
    }
    
    return this_details;
}

