
var switchEditors = {

	switchto: function(el) {
		var aid = el.id, l = aid.length, id = aid.substr(0, l - 5), mode = aid.substr(l - 4);

		this.go(id, mode);
	},

	go: function(id, mode) { // mode can be 'html', 'tmce', or 'toggle'
		id = id || 'content';
		mode = mode || 'toggle';

		var t = this, ed = tinyMCE.get(id), wrap_id, txtarea_el, dom = tinymce.DOM;

		wrap_id = 'wp-'+id+'-wrap';
		txtarea_el = dom.get(id);

		if ( 'toggle' == mode ) {
			if ( ed && !ed.isHidden() )
				mode = 'html';
			else
				mode = 'tmce';
		}

		if ( 'tmce' == mode || 'tinymce' == mode ) {
			if ( ed && ! ed.isHidden() )
				return false;

			if ( typeof(QTags) != 'undefined' )
				QTags.closeAllTags(id);

			if ( tinyMCEPreInit.mceInit[id] && tinyMCEPreInit.mceInit[id].wpautop )
				txtarea_el.value = t.wpautop( txtarea_el.value );

			if ( ed ) {
				ed.show();
			} else {
				ed = new tinymce.Editor(id, tinyMCEPreInit.mceInit[id]);
				ed.render();
			}

			dom.removeClass(wrap_id, 'html-active');
			dom.addClass(wrap_id, 'tmce-active');
			setUserSetting('editor', 'tinymce');

		} else if ( 'html' == mode ) {

			if ( ed && ed.isHidden() )
				return false;

			if ( ed ) {
				txtarea_el.style.height = ed.getContentAreaContainer().offsetHeight + 20 + 'px';
				ed.hide();
			}

			dom.removeClass(wrap_id, 'tmce-active');
			dom.addClass(wrap_id, 'html-active');
			setUserSetting('editor', 'html');
		}
		return false;
	},

	_wp_Nop : function(content) {
		var blocklist1, blocklist2;

		// Protect pre|script tags
		if ( content.indexOf('<pre') != -1 || content.indexOf('<script') != -1 ) {
			content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				a = a.replace(/<br ?\/?>(\r\n|\n)?/g, '<wp_temp>');
				return a.replace(/<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp_temp>');
			});
		}

		// Pretty it up for the source editor
		var blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|p';
		content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('\\s*<(('+blocklist1+')[^>]*)>', 'mg'), '\n<$1>');
		
		content = content.replace(new RegExp('<p( [^>]*)?>[\\s\\n]*<(/?script[^>]*)>', 'mg'), '\n<$2>');
		content = content.replace(new RegExp('<(/?script[^>]*)>[\\s\\n]*</p>', 'mg'), '\n<$1>');

		// Fix some block element newline issues
		content = content.replace(new RegExp('\\s*<div', 'mg'), '\n<div');
		content = content.replace(new RegExp('</div>\\s*', 'mg'), '</div>\n');
		content = content.replace(new RegExp('\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*', 'gi'), '\n\n[caption$1[/caption]\n\n');
		content = content.replace(new RegExp('caption\\]\\n\\n+\\[caption', 'g'), 'caption]\n\n[caption');

		var blocklist2 = 'blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre';
		content = content.replace(new RegExp('\\s*<(('+blocklist2+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
		content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('<li([^>]*)>', 'g'), '\t<li$1>');

		if ( content.indexOf('<object') != -1 ) {
			content = content.replace(new RegExp('\\s*<param([^>]*)>\\s*', 'mg'), "<param$1>");
			content = content.replace(new RegExp('\\s*</embed>\\s*', 'mg'), '</embed>');
		}

		// Unmark special paragraph closing tags
		content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
		content = content.replace(new RegExp('\\s*(<p [^>]+>.*</p>)', 'mg'), '\n$1');
		content = content.replace(new RegExp('<p>\\s*</p>', 'mg'), "<p>&nbsp;</p>\n");

		// put back the line breaks in pre|script
		content = content.replace(/<wp_temp>/g, '\n');

		return content;
	},

	_wp_Autop : function(pee) {
		// filtered when switching html to visual
		var blocklist = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]|script';
		var blocklist2 = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|h[1-6]|script';
		pee = pee + "\n\n";
		pee = pee.replace(new RegExp('(<(?:'+blocklist+')[^>]*>)', 'gi'), "\n$1");
		pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), "$1\n\n");
		pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
		pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
		pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
		pee = pee.replace(new RegExp('<p( [^>]*)?>[\\s\\n]*</p>', 'mg'), "<p$1>&nbsp;</p>\n");
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
		pee = pee.replace(new RegExp("<p ?[^>]*>(<!--(.*)?-->)", 'gi'), "$1");
		pee = pee.replace(new RegExp("(<!--(.*)?-->)</p>", 'gi'), "$1");
		pee = pee.replace(new RegExp('<p>\\s*<blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
		pee = pee.replace(new RegExp('</blockquote>\\s*</p>', 'gi'), '</p></blockquote>');
		pee = pee.replace(new RegExp('<p>[\\s\\n]*(<(?:'+blocklist+')[^>]*>)', 'gi'), "$1");
		pee = pee.replace(new RegExp('<p>[\\s\\n]*(</(?:'+blocklist2+')[^>]*>)', 'gi'), "$1");
		pee = pee.replace(new RegExp('(<(?:'+blocklist2+')[^>]*>)[\\s\\n]*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp('(</(?:'+blocklist+')[^>]*>)[\\s\\n]*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
		pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
		pee = pee.replace(new RegExp('(?:<p>|<br ?/?>)*\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*(?:</p>|<br ?/?>)*', 'gi'), '[caption$1[/caption]');

		// Fix the pre|script tags
		pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
			a = a.replace(/<br ?\/?>[\r\n]*/g, '\n');
			return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '\n');
		});

		return pee;
	},

	pre_wpautop : function(content) {
		var t = this, o = { o: t, data: content, unfiltered: content },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforePreWpautop', [o]);
		o.data = t._wp_Nop(o.data);
		if ( q )
			jQuery('body').trigger('afterPreWpautop', [o]);

		return o.data;
	},

	wpautop : function(pee) {
		var t = this, o = { o: t, data: pee, unfiltered: pee },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforeWpautop', [o]);
		o.data = t._wp_Autop(o.data);
		if ( q )
			jQuery('body').trigger('afterWpautop', [o]);

		return o.data;
	}
}

