/*
 * v 2.0.0
 */

var uf$ = jQuery.noConflict();

var UserFilesForm = {
    config: {
        dropzone: {
        }
    },

    initialize: function(opts) {
        var config =  uf$.extend({}, this.config, opts);

        console.log('initialize');

        console.log(config);

        if (!uf$.Dropzone) {
            document.writeln('<style data-compiled-css>@import url('+config.assetsUrl + 'vendor/dropzone/dist/min/dropzone.min.css); </style>');
            document.writeln('<script src="' + config.assetsUrl + 'vendor/dropzone/dist/min/dropzone.min.js"><\/script>');
        }

        uf$(document).ready(function() {

            uf$('#' + config.propkey).each(function() {

                if (!this.id) {
                    console.log('[UserFiles:Error] Initialization Error. Id required');
                    return;
                }

                var dropzoneConfig = uf$.extend({}, config.dropzone, uf$(this).data());

                if (dropzoneConfig.clickable && !uf$(dropzoneConfig.clickable).get(0)) {
                    delete dropzoneConfig.clickable;
                }

                dropzoneConfig.url=  config.actionUrl;
                dropzoneConfig.params = {
                    action: 'web/file/upload',
                    propkey: config.propkey,
                    ctx: config.ctx
                };




                console.log(
                    dropzoneConfig
                );


                var dropzone = new Dropzone('#'+this.id, dropzoneConfig);

                var DropzoneEvents = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile",
                    "addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple",
                    "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled",
                    "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"];
                DropzoneEvents.filter(function (event) {
                    if (UserFilesForm['_'+event]) {
                        dropzone.on(event, UserFilesForm['_'+event]);
                    }
                },this);
            });

        });

    },

    _addedfile:function(file) {

        console.log('_addedfile');
    },


    _processing: function(file) {

    },

    _uploadprogress: function(file, progress, bytesSent) {

    },

    _complete: function(file) {

    },

    _queuecomplete: function() {

    },

    _error: function(file, message) {

        console.log('_error');

        console.log(message);
    },

    _success: function(file, response) {

    },

};
