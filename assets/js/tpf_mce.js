(function () {
    tinymce.create('tinymce.plugins.ClTpf', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init: function (ed, url) {
            ed.addButton('tpf', {
                title: 'Типограф',
                cmd: 'tpf',
                image: url + '/../img/type_btn.svg'
            });
            ed.addCommand('tpf', function () {
                var command_url = ed.documentBaseUrl + 'admin-ajax.php?action=cl-tpf';
                var content = (ed.selection.getContent() == '') ? ed.getContent() : ed.selection.getContent();

                jQuery.post(command_url, {content: content}, function (data) {
                    if (data.success == true) {
                        if (ed.selection.getContent() == '')
                            ed.setContent(data.data);
                        else
                            ed.execCommand('mceInsertContent', 0, data.data);

                        alert('Текст успешно обработан');
                    } else
                        alert('Ошибка! ' + data.data);
                });
            });

        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl: function (n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo: function () {
            return {
                longname: 'Cl_Typograf',
                author: 'Const Lab',
                authorurl: 'http://constlab.ru',
                infourl: 'http://constlab.ru',
                version: "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('tpf', tinymce.plugins.ClTpf);
})();