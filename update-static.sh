#!/usr/bin/env sh
mkdir public/vendor/
mkdir public/vendor/bootstrap/
mkdir public/vendor/bootstrap/css/
cp bower_components/bootstrap/dist/css/bootstrap.min.css public/vendor/bootstrap/css/
cp -R bower_components/bootstrap/fonts public/vendor/bootstrap/

mkdir public/vendor/bootstrap/js/
cp bower_components/bootstrap/dist/js/bootstrap.min.js public/vendor/bootstrap/js/

mkdir public/vendor/bootstrap-material-design
cp -R bower_components/bootstrap-material-design/dist/* public/vendor/bootstrap-material-design/

cp bower_components/jquery/dist/jquery.min.js public/vendor/

mkdir public/vendor/jqueryui/
cp bower_components/jqueryui/jquery-ui.min.js public/vendor/jqueryui/
cp bower_components/jqueryui/ui/i18n/datepicker-ru.js public/vendor/jqueryui/
cp bower_components/jqueryui/themes/ui-darkness/jquery-ui.min.css public/vendor/jqueryui/
cp -R bower_components/jqueryui/themes/ui-darkness/images public/vendor/jqueryui/

cp bower_components/jquery-form/jquery.form.js public/vendor/
cp bower_components/jquery-form/src/jquery.form.js public/vendor/
mkdir public/vendor/jquery-file-upload/
cp -R bower_components/jquery-file-upload/* public/vendor/jquery-file-upload/
cp bower_components/modernizr/modernizr.js public/vendor/

mkdir public/vendor/elevatezoom/
cp -R bower_components/elevatezoom/jquery.elevateZoom-2.2.3.min.js public/vendor/elevatezoom/

mkdir public/vendor/fancybox/
cp -R bower_components/fancybox/source/*.css public/vendor/fancybox/
cp -R bower_components/fancybox/source/*.gif public/vendor/fancybox/
cp -R bower_components/fancybox/source/*.png public/vendor/fancybox/
cp -R bower_components/fancybox/source/*.js public/vendor/fancybox/

mkdir public/vendor/bootstrap-tagsinput/
cp -R bower_components/bootstrap-tagsinput/dist/*.css public/vendor/bootstrap-tagsinput/
cp -R bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js public/vendor/bootstrap-tagsinput/
cp -R bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js public/vendor/bootstrap-tagsinput/

mkdir public/vendor/typeahead/
cp -R bower_components/typeahead.js/dist/*.min.js public/vendor/typeahead/

mkdir public/vendor/responsiveslides/
cp -R bower_components/ResponsiveSlides/responsiveslides.min.js public/vendor/responsiveslides/
cp -R bower_components/ResponsiveSlides/responsiveslides.css public/vendor/responsiveslides/

mkdir public/vendor/jquery_lazyload/
cp -R bower_components/jquery_lazyload/*.js public/vendor/jquery_lazyload/

mkdir public/vendor/jstree-bootstrap-theme/
cp -R bower_components/jstree-bootstrap-theme/dist/* public/vendor/jstree-bootstrap-theme/

mkdir public/vendor/bootstrap-fileinput/
mkdir public/vendor/bootstrap-fileinput/css/
mkdir public/vendor/bootstrap-fileinput/img/
mkdir public/vendor/bootstrap-fileinput/js/
mkdir public/vendor/bootstrap-fileinput/themes/
cp -R bower_components/bootstrap-fileinput/js/* public/vendor/bootstrap-fileinput/js
cp -R bower_components/bootstrap-fileinput/css/* public/vendor/bootstrap-fileinput/css/
cp -R bower_components/bootstrap-fileinput/img/* public/vendor/bootstrap-fileinput/img/
cp -R bower_components/bootstrap-fileinput/themes/* public/vendor/bootstrap-fileinput/themes/

mkdir public/vendor/tinymce/
cp -R bower_components/tinymce/* public/vendor/tinymce/

mkdir public/vendor/select2/
mkdir public/vendor/select2/css/
mkdir public/vendor/select2/js/
cp -R bower_components/select2/dist/js/* public/vendor/select2/js
cp -R bower_components/select2/dist/css/* public/vendor/select2/css/