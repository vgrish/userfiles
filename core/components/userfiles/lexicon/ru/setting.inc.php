<?php

$_lang['area_userfiles_main'] = 'Основные';

$_lang['setting_userfiles_source_default'] = 'Источник медиа для UserFiles';
$_lang['setting_userfiles_source_default_desc'] = 'Выберите источник медиа, который будет использован по умолчанию для загрузки файлов пользователя.';


$_lang['userfiles_source_thumbnail_desc'] = 'Закодированный в JSON массив с параметрами генерации превью изображений.';
$_lang['userfiles_source_maxUploadSize_desc'] = 'Максимальный размер загружаемых изображений (в байтах).';
$_lang['userfiles_source_imageNameType_desc'] = 'Этот параметр указывает, как нужно переименовать файл при загрузке. Hash - это генерация уникального имени, в зависимости от содержимого файла. Friendly - генерация имени по алгоритму дружественных url страниц сайта (они управляются системными настройками).';

$_lang['userfiles_source_thumbnail_name_desc'] = 'Шаблон имени превью изображения. <br>
{name} - name изображения.<br>
{id} - id изображения.<br>
{class} - class изображения.<br>
{list} - list изображения.<br>
{session} - session изображения.<br>
{createdby} - createdby изображения.<br>
{source} - source изображения.<br>
{context} - context изображения.<br>
{rand} - случайная строка.<br>

{w} - ширина превью.<br>
{h} - высота превью.<br>
{q} - качество превью.<br>
{f} - расширение превью.<br>

';

$_lang['userfiles_source_file_name_desc'] = 'Шаблон имени файла. <br>
{name} - name файла.<br>
{class} - class файла.<br>
{list} - list файла.<br>
{session} - session файла.<br>
{createdby} - createdby файла.<br>
{source} - source файла.<br>
{rand} - случайная строка.<br>
';
