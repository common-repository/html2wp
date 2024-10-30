(function( $ ) {
	'use strict';

  $(function() {

  		// events
  		let parent 		 		= $(document).find(".html2wp"),
  			import_html  		= parent.find("input[name='import_html']"),
  			remove_html  		= parent.find(".remove_html_files"),
  			convert2type 		= parent.find("#convert2type"),
  			additional_settings = parent.find(".additional_settings"),
  			import_content_by   = parent.find(".import_content_by"),
  			content_by_tag      = parent.find(".content_by_tag"),
  			import_title_by     = parent.find(".import_title_by"),
  			title_by_tag        = parent.find(".title_by_tag"),
  			convert_html 		= ".convert_html_files",
  			reconvert_html 		= ".html_converted",
  			ajax_action  		= "html_actions";


		import_html.click(function(){
			let name   = $(this).attr("id"),
			imp    = $("."+name),
			inp_op = $(".import_options");
			inp_op.find("p").addClass("h2p_hide");
			imp.removeClass("h2p_hide");
			var attr = imp.find("input").attr("data-val");
			inp_op.find("input").val("");
			if (typeof attr !== 'undefined' && attr !== false) {
				imp.find("input").val(attr);
			}
		});


		remove_html.click(function(){
			let path   = $(this).parent().attr("data-path"),
				cid	   = $(this).parent().attr("data-cid");
			$(this).parents("tr").remove();
			$.ajax({
				url:  ajaxurl,
				type: "POST",
				data: {action:ajax_action,type:"remove_html",path:path,cid:cid},
				success:function(r){
					if (r != 1) {
						console.log("remove failed")
					}
				}
			});
		});

		$(document).on("click",convert_html,function(){
			let path  = $(this).parent().attr("data-path"),
				name  = $(this).parent().attr("data-name"),
				_this = $(this);
				_this.text("...");
			$.ajax({
				url:  ajaxurl,
				type: "POST",
				data: {action:ajax_action,type:"html_convert",path:path,name:name},
				success:function(r){
					if (r) {
						_this.parents("tr").find(".page_slug a").text(r["slug"]);
						_this.parents("tr").find(".page_slug a").attr("href",r["slug"]);
						_this.addClass("html_converted");
						_this.text("Reconvert");
						_this.removeClass("convert_html_files");
						_this.parent().attr("data-pid",r["post_id"]);
						_this.parent().attr("data-cid",r["insert_id"]);
					}
				}
			});
		});

		$(document).on("click",reconvert_html,function(){
			let post_id  = $(this).parent().attr("data-pid"),
				c_id	 = $(this).parent().attr("data-cid"),
				_this    = $(this);
				_this.text("...");
				$.ajax({
					url:  ajaxurl,
					type: "POST",
					data: {action:ajax_action,type:"html_reconvert",post_id:post_id,c_id:c_id},
					success:function(r){
						_this.parents("tr").find(".page_slug a").text("");
						_this.addClass("convert_html_files");
						_this.text("Convert");
						_this.removeClass("html_converted");
						_this.parent().attr("data-pid",0);
						_this.parent().attr("data-cid",0);
					}
				});
		});

		convert2type.change(function(){
			let type = $(this).val();
			if (type == "page") {
				additional_settings.show();
			}else{
				additional_settings.hide();
			}
		});

		import_content_by.find("input[type='radio']").change(function(){
			let val = $(this).val();
			if (val == "all_file") {
				content_by_tag.addClass("inactive");
			}else{
				content_by_tag.removeClass("inactive");
			}
		});

		import_title_by.find("input[type='radio']").change(function(){
			let val = $(this).val();
			if (val == "file_name") {
				title_by_tag.addClass("inactive");
			}else{
				title_by_tag.removeClass("inactive");
			}
		});





  });



})( jQuery );
