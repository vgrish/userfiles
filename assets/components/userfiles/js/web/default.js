/*
 * v 2.0.0
 */

var UserFilesTemplate = {

    get: function(name) {
        var template = [];
        var all = {
            base: [
                '<div class="dz-preview dz-file-preview">',
                '<div class="dz-image"><img data-dz-thumbnail /></div>',
                '<div class="dz-details">',
                '<div class="dz-size"><span data-dz-size></span></div>',
                '<div class="dz-filename"><span data-dz-name></span></div>',
                '</div>',
                '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>',
                '<div class="dz-error-message"><span data-dz-errormessage></span></div>',
                '<div class="dz-success-mark">',
                '<svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">',
                '<title>Check</title>',
                '<defs></defs>',
                '<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">',
                '<path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>',
                '</g>',
                '</svg>',
                '</div>',
                '<div class="dz-error-mark">',
                '<svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">',
                '<title>Error</title>',
                '<defs></defs>',
                '<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">',
                '<g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">',
                '<path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>',
                '</g>',
                '</g>',
                '</svg>',
                '</div>',
                '</div>',
            ]
        };

        if (all[name]) {
            template = all[name];
        }

        return template.join('');
    }

};

var UserFilesForm = {
    config: {
        dropzone: {

            dictDefaultMessage: UserFilesLexicon.dropzone.dictDefaultMessage || '',
            dictFallbackMessage: UserFilesLexicon.dropzone.dictFallbackMessage || '',
            dictFileTooBig: UserFilesLexicon.dropzone.dictFileTooBig || '',
            dictInvalidFileType: UserFilesLexicon.dropzone.dictInvalidFileType || '',
            dictResponseError: UserFilesLexicon.dropzone.dictResponseError || '',
            dictCancelUpload: UserFilesLexicon.dropzone.dictCancelUpload || '',
            dictCancelUploadConfirmation: UserFilesLexicon.dropzone.dictCancelUploadConfirmation || '',
            dictRemoveFile: UserFilesLexicon.dropzone.dictRemoveFile || '',
            dictMaxFilesExceeded: UserFilesLexicon.dropzone.dictMaxFilesExceeded || '',
            dictDefaultCanceled: UserFilesLexicon.dropzone.dictDefaultCanceled || '',

            maxFilesize: 1,
            maxFiles: 1,
            parallelUploads: 1,
            addRemoveLinks: true,

            createImageThumbnails: true,
            maxThumbnailFilesize: 9999999999,
            thumbnailWidth: 120,
            thumbnailHeight: 90,

            errors: []
        }
    },

    initialize: function(opts) {
        var config = $.extend(true, {}, this.config, opts);

        if (!$.Dropzone) {
            document.writeln('<style data-compiled-css>@import url(' + config.assetsUrl + 'vendor/dropzone/dist/min/dropzone.min.css); </style>');
            document.writeln('<script src="' + config.assetsUrl + 'vendor/dropzone/dist/dropzone.js"><\/script>');
        }

        if (!$.pnotify) {
            document.writeln('<style data-compiled-css>@import url(' + config.assetsBaseUrl + 'components/modpnotify/build/pnotify.custom.css); </style>');
            document.writeln('<script src="' + config.assetsBaseUrl + 'components/modpnotify/build/pnotify.custom.js"><\/script>');
        }

        if (!$.ui) {
            document.writeln('<style data-compiled-css>@import url(' + config.assetsUrl + 'vendor/jqueryui/custom/jquery-ui.min.css); </style>');
            document.writeln('<script src="' + config.assetsUrl + 'vendor/jqueryui/custom/jquery-ui.min.js"><\/script>');
        }

        $(document).ready(function() {

            $('#' + config.propkey).each(function() {
                if (!this.id) {
                    console.log('[UserFiles:Error] Initialization Error. Id required');
                    return;
                }

                var dropzoneConfig = $.extend({}, config.dropzone, $(this).data());
                if (dropzoneConfig.clickable && !$(dropzoneConfig.clickable).get(0)) {
                    delete dropzoneConfig.clickable;
                }

                dropzoneConfig.previewTemplate = UserFilesTemplate.get(dropzoneConfig.template || 'base');

                dropzoneConfig.init = function() {
                    var thisDropzone = this;

                    $.ajax({
                        type: 'GET',
                        url: config.actionUrl,
                        data: {
                            action: 'file/getlist',
                            propkey: config.propkey,
                            ctx: config.ctx
                        },
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(r) {

                            if (r.success && r.results) {
                                $.each(r.results, function(i, item) {

                                    var addFile = {
                                        name: item.file,
                                        size: item.size,
                                        type: item.mime
                                    };

                                    thisDropzone.options.addedfile.call(thisDropzone, addFile);
                                    if (item.dyn_thumbnail) {
                                        thisDropzone.options.thumbnail.call(thisDropzone, addFile, item.dyn_thumbnail);
                                    }
                                    addFile.previewElement.classList.add('dz-complete');
                                    $(addFile.previewElement).attr('data-userfiles-id', item.id);
                                    thisDropzone.files.push(addFile);

                                    thisDropzone.options.maxFiles--;
                                });
                            } else if (!r.success) {
                                UserFilesMessage.error('', r.message);
                            }
                        }
                    });

                };


                dropzoneConfig.removedfile = function(file) {
                    var _ref;
                    var thisDropzone = this;

                    if (!file.previewElement) {
                        return;
                    }

                    var id = $(file.previewElement).attr('data-userfiles-id');
                    if (!id && (_ref = file.previewElement) != null) {
                        _ref.parentNode.removeChild(file.previewElement);
                        return;
                    }

                    thisDropzone.files.push(file);

                    $.ajax({
                        type: 'POST',
                        url: this.options.url,
                        data: {
                            action: 'file/remove',
                            id: id,
                            propkey: this.options.params.propkey || '',
                            ctx: this.options.params.ctx || ''
                        },
                        dataType: "json",
                        success: function(r) {
                            if (!r.success) {
                                UserFilesMessage.error('', r.message);
                            } else {
                                if ((_ref = file.previewElement) != null) {
                                    _ref.parentNode.removeChild(file.previewElement);
                                }
                                thisDropzone.options.maxFiles++;
                                return thisDropzone._updateMaxFilesReachedClass();
                            }
                        },
                        failure: function(r) {

                        }
                    });
                };

                dropzoneConfig.canceled = function(file) {
                    return this.emit("error", file, dropzoneConfig.dictDefaultCanceled);
                };

                dropzoneConfig.url = config.actionUrl;
                dropzoneConfig.params = {
                    action: 'file/upload',
                    propkey: config.propkey,
                    ctx: config.ctx
                };

                var dropzone = new Dropzone(this, dropzoneConfig);
                var DropzoneEvents = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile",
                    "addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple",
                    "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled",
                    "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"
                ];
                DropzoneEvents.filter(function(event) {
                    if (UserFilesForm['_' + event]) {
                        dropzone.on(event, UserFilesForm['_' + event]);
                    }
                }, this);

                /* add sort */
                if (!!dropzoneConfig.sorting) {

                    $(this).sortable({
                        items:'.dz-preview',
                        cursor: 'move',
                        opacity: 0.5,
                        containment: 'parent',
                        distance: 20,
                        tolerance: 'pointer',

                        start: function (e, ui) {
                            var start = [];
                            $($(e.target).get(0)).children().each(function (i) {
                                var $this = $(this);
                                if (!$this.data('userfilesId')) {
                                    return true;
                                }
                                start.push($this.data('userfilesId'));
                            });

                            $(this).data().uiSortable.start = start;
                        },

                        update: function(e, ui) {
                            var start = $(this).data().uiSortable.start || [];
                            var end = [];

                            $($(e.target).get(0)).children().each(function (i) {
                                var $this = $(this);

                                if (!$this.data('userfilesId')) {
                                    return true;
                                }

                                end.push($this.data('userfilesId'));
                            });

                            var ids =UserFilesForm._array_diff_assoc(
                                start, end
                            );

                            if (ids.length >= 2) {
                                $.ajax({
                                    type: 'POST',
                                    url: dropzoneConfig.url,
                                    data: {
                                        action: 'file/sort',
                                        ids: ids,
                                        propkey: dropzoneConfig.params.propkey || '',
                                        ctx: dropzoneConfig.params.ctx || ''
                                    },
                                    dataType: "json",
                                    success: function(r) {
                                        if (!r.success) {
                                            UserFilesMessage.error('', r.message);
                                        }
                                    },
                                    failure: function(r) {

                                    }
                                });
                            }
                        }
                    });
                }


            });

            PNotify.prototype.options.styling = "bootstrap3";

        });

    },

    _addedfile: function(file) {
        this.errors = [];
    },

    _queuecomplete: function() {
        if (this.errors.length > 0) {
            UserFilesMessage.error('', this.errors.join('<br>'));
        }
    },

    _error: function(file, message) {
        UserFilesMessage.error('', message);

        setTimeout(function() {
            this.removeFile(file);
        }.bind(this), 1000);
    },

    _success: function(file, response) {
        response = response ? JSON.parse(response) : {};
        if (response.success == false && response.message != '') {
            this.errors.push(file.name + ': ' + response.message);
            setTimeout(function() {
                this.removeFile(file);
            }.bind(this), 1000);
        } else {
            $(file.previewElement).attr('data-userfiles-id', response.object.id);
        }
    },

    _processing: function(file) {

    },

    _uploadprogress: function(file, progress, bytesSent) {

    },

    _complete: function(file) {

    },

    _removedfile: function(file) {

    },

    _array_diff_assoc: function(arr1, arr2) {
        var results = [];
        for (var i = 0; i < arr1.length; i++) {
            if (arr1[i] != arr2[i]) {
                results.push(arr2[i]);
            }
        }
        return results;
    }


};


var UserFilesMessage = {
    defaults: {
        delay: 4000,
        addclass: 'userfiles-message'
    },
    success: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'success';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.message.title.success : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    error: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'error';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.message.title.error : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    info: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'info';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.message.title.info : title;
        new PNotify($.extend({}, this.defaults, notify));
    },
    remove: function() {
        PNotify.removeAll();
    }
};

var UserFilesConfirm = {
    defaults: {
        hide: false,
        addclass: 'userfiles-сonfirm',
        icon: 'glyphicon glyphicon-question-sign',
        confirm: {
            confirm: true,
            buttons: [{
                text: UserFilesLexicon.defaults.yes,
                addClass: 'btn-primary'

            }, {
                text: UserFilesLexicon.defaults.no,
                addClass: 'btn-danger'

            }]
        },
        buttons: {
            closer: false,
            sticker: false
        },
        history: {
            history: false
        }
    },
    success: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'success';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.confirm.title.success : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    error: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'error';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.confirm.title.error : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    info: function(title, message) {
        if (!message) return false;
        var notify = {};
        notify.type = 'info';
        notify.text = message;
        notify.title = (!title) ? UserFilesLexicon.defaults.confirm.title.info : title;
        return new PNotify($.extend({}, this.defaults, notify));
    },
    form: function(form, type, title, message) {
        if (!type) return false;
        if (form) {
            $.extend(this.defaults, {
                before_init: function(opts) {
                    $(form).find('input[type="button"], button, a').attr('disabled', true);
                },
                after_close: function(PNotify, timer_hide) {
                    $(form).find('input[type="button"], button, a').attr('disabled', false);
                }
            });
        }

        switch (type) {
            case 'success':
                return this.success(title, message);
            default:
            case 'error':
                return this.error(title, message);
            case 'info':
                return this.info(title, message);
        }
    },
    remove: function() {
        return PNotify.removeAll();
    }
};
