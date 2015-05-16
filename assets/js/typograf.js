$ = jQuery.noConflict();

/**
 * Typograf text
 *
 * @param content
 * @param fragment
 * @param callback
 * @returns {boolean}
 */
function typograf(content, fragment, callback) {
	var command_url = ajaxurl + '?action=cl-tpf';

	if (getByteLen(content) >= 32768) {
		alert('Текст слишком большой для типографа. Отправляйте текст на обработку частями');
		return false;
	}

	$.post(command_url, {
		content: content,
		fragment: fragment
	}, function (data) {
		if (data.success == true) {
			callback(data.data);
		} else
			alert('Ошибка! ' + data.data);

	});
}

/**
 *
 * @param editor_id
 * @returns {*}
 */
function getSelectionEditor(editor_id) {
	var textComponent = document.getElementById(editor_id);
	var selectedText;
	// IE version
	if (document.selection != undefined) {
		textComponent.focus();
		var sel = document.selection.createRange();
		selectedText = sel.text;
	}
	// Mozilla version
	else if (textComponent.selectionStart != undefined) {
		var startPos = textComponent.selectionStart;
		var endPos = textComponent.selectionEnd;
		selectedText = textComponent.value.substring(startPos, endPos)
	}
	return selectedText;
}

/**
 * Count bytes in a string's UTF-8 representation.
 *
 * @param   string
 * @return  int
 */
function getByteLen(normal_val) {
	// Force string type
	normal_val = String(normal_val);

	var byteLen = 0;
	for (var i = 0; i < normal_val.length; i++) {
		var c = normal_val.charCodeAt(i);
		byteLen += c < (1 << 7) ? 1 :
			c < (1 << 11) ? 2 :
				c < (1 << 16) ? 3 :
					c < (1 << 21) ? 4 :
						c < (1 << 26) ? 5 :
							c < (1 << 31) ? 6 : Number.NaN;
	}
	return byteLen;
}

/**
 * Typograf content via code editor
 */
function initQtag() {
	QTags.addButton('typograf', 'Типограф', '');
	setTimeout(
		function () {
			$('#qt_content_typograf').click(function () {
				var content = getSelectionEditor('content');
				var fragment = content ? true : false;
				if (!fragment) {
					content = $('textarea#content').val();
				}
				typograf(content, fragment, function (result) {
					if (fragment) {
						$('textarea#content').val($('textarea#content').val().replace(content, result));
					} else {
						$('textarea#content').val(result);
					}
					alert('Текст успешно обработан');
				});
				return false;
			});
		}, 2000);
}

$(document).ready(function () {
	$('#tpf-dialog-ok').click(function () {
		var content = $('textarea#tpf-one').val();

		typograf(content, false, function (result) {
			$('textarea#tpf-one').val(result);
			alert('Текст успешно обработан');
		});

		return false;
	});
});