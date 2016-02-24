<?php

include_once 'errors.inc.php';
include_once 'manager.inc.php';
include_once 'setting.inc.php';


$_lang['userfiles'] = 'UserFiles';
$_lang['userfiles_desc'] = 'Файлы пользователя';

$_lang['userfiles_yes'] = 'Да';
$_lang['userfiles_no'] = 'Нет';

$_lang['userfiles_files'] = 'Файлы';
$_lang['userfiles_files_desc'] = 'Управление Файлами';
$_lang['userfiles_files_intro'] = 'Панель управления Файлами';

$_lang['userfiles_title_ms_success'] = 'Выполнено';
$_lang['userfiles_title_ms_error'] = 'Ошибка';
$_lang['userfiles_title_ms_info'] = 'Информация';

$_lang['userfiles_title_cms_success'] = 'Подтверждение';
$_lang['userfiles_title_cms_error'] = 'Подтверждение!';
$_lang['userfiles_title_cms_info'] = 'Подтверждение';

$_lang['userfiles_msg_select_files'] = 'Select files';
$_lang['userfiles_msg_needsclick'] = 'Drop files here or click to upload';

$_lang['userfiles_dz_dictMaxFilesExceeded'] = 'You can not upload any more files';
$_lang['userfiles_dz_dictFallbackMessage'] = 'Your browser does not support drag\'n\'drop file uploads';
$_lang['userfiles_dz_dictFileTooBig'] = 'File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB';
$_lang['userfiles_dz_dictInvalidFileType'] = 'You can\'t upload files of this type';
$_lang['userfiles_dz_dictResponseError'] = 'Server responded with {{statusCode}} code';
$_lang['userfiles_dz_dictCancelUpload'] = 'Cancel upload';
$_lang['userfiles_dz_dictCancelUploadConfirmation'] = 'Are you sure you want to cancel this upload?';
$_lang['userfiles_dz_dictRemoveFile'] = 'Remove file';
$_lang['userfiles_dz_dictDefaultCanceled'] = 'Upload canceled';
