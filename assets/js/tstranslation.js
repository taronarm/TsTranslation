/** 
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */

$(document).ready(function () {
    thisUrl = $('#tstranslationControllerUrl').val() + '/';
    $('.tstranslation-list table tbody').sortable();
    $('.tstranslation-list table tbody').disableSelection();
    orderCount = 0;
    $(document).on('sortupdate', '.tstranslation-list table tbody', function (event, ui) {
        if (orderCount == 0) {
            $('.ts-add-language').parents('span').prepend('<button type="button" class="btn btn-info ts-save-ordering" data-tstoggle="tooltip" title="save current ordering for frontend language widget"><i class="glyphicon glyphicon-list">&nbsp;&nbsp;</i>Save ordering</button>');
            orderCount++;
        }
    });

    $(document).on('click', '.ts-save-ordering', function () {
        //if(confirm('Save new ordering?')) {
        var languageList = new Array();
        var url = thisUrl + 'saveOrdering';
        $.each($('.tstranslation-list table tbody tr'), function (index) {
            languageList[index] = $(this).find('.ts-remove').attr('data-id');
        })

        $.ajax({
            type: "POST",
            url: url,
            beforeSend: tstranslationStartLoader($('.ts-save-ordering i')),
            data: {'languageList': languageList},
            success: function (dataJson) {
                data = JSON.parse(dataJson);
                if (data.ok == 1) {
                    $('.ts-save-ordering').tooltip('hide');
                    $('.ts-save-ordering').remove();
                    orderCount = 0;
                } else {
                    alert(data.message);
                }
                tstranslationEndLoader($('.ts-save-ordering i'));
            }
        }, false)
        //}
    });
    $(document).on('click', '.ts-remove', function () {
        var question = prompt('Delete translated messages also (no/yes)?', 'no');
        if (question == 'no' || question == 'yes') {
            var id = $(this).attr('data-id');
            var url = thisUrl + 'removeLanguage';
            var obj = $(this);
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: tstranslationStartLoader(obj.parents('tr').find('.for-loader')),
                data: {id: id, deleteTranslations: question},
                success: function (dataJson) {
                    data = JSON.parse(dataJson);
                    if (data.ok == 1) {
                        obj.parents('tr').remove();
                    } else {
                        alert(data.message);
                    }
                    tstranslationEndLoader(obj.parents('tr').find('.for-loader'));
                }
            }, false)
        }
    });

    $(document).on('click', '.ts-change-status', function () {
        var id = $(this).attr('data-id');
        var url = thisUrl + 'changeStatus';
        var obj = $(this);
        $.ajax({
            type: "POST",
            url: url,
            beforeSend: tstranslationStartLoader(obj.parents('tr').find('.for-loader')),
            data: 'id=' + id,
            success: function (dataJson) {
                data = JSON.parse(dataJson);
                if (data.ok == 1) {
                    var span = obj.parents('tr').find('.ts-status');
                    if (data.status == 1) {
                        span.removeClass('alert-danger').removeClass('badge-important').addClass('alert-success').addClass('badge-success');
                        span.html('Active');
                    } else {
                        span.removeClass('alert-success').removeClass('badge-success').addClass('alert-danger').addClass('badge-important');
                        span.html('Not active');
                    }
                } else {
                    alert(data.message);
                }
                tstranslationEndLoader(obj.parents('tr').find('.for-loader'));
            }
        }, false)
    });

    $(document).on('click', '.ts-make-default', function () {
        if (confirm('Change default language?')) {
            var id = $(this).attr('data-id');
            var url = thisUrl + 'makeDefault';
            var obj = $(this);
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: tstranslationStartLoader(obj.parents('tr').find('.for-loader')),
                data: 'id=' + id,
                success: function (dataJson) {
                    data = JSON.parse(dataJson);
                    if (data.ok == 1) {
                        defaultSpan = obj.parents('table').find('.ts-default').clone();
                        obj.parents('table').find('.ts-default').remove();
                        obj.parents('tr').find('.ts-status').after(defaultSpan);
                    } else {
                        alert(data.message);
                    }
                    tstranslationEndLoader(obj.parents('tr').find('.for-loader'));
                }
            }, false)
        }
    });

    $(document).on('change', '.ts-add-language', function () {
        if ($(this).val() != '' && confirm('Add new language?')) {
            $(this).parent('span').removeClass('open');
            var id = $(this).val();
            var url = thisUrl + 'addLanguage';
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: tstranslationStartLoader($('.ts-add-language').prev('button').find('i')),
                data: 'id=' + id,
                success: function (dataJson) {
                    data = JSON.parse(dataJson);
                    if (data.ok == 1) {
                        window.location = data.redirectUrl;
                    } else {
                        alert(data.message);
                    }
                    tstranslationEndLoader($('.ts-add-language').prev('button').find('i'));
                }
            }, false)
        }
    });

    $(document).on('click', '.ts-translate', function () {
        if (!$(this).hasClass('active')) {
            var translateButtonsArray = $('.ts-translate');
            $.each(translateButtonsArray, function () {
                $(this).removeClass('active');
                $(this).find('i').removeClass('glyphicon-check');
            })
            $(this).addClass('active');
            $(this).find('i').addClass('glyphicon-check');
            $('.tstranslation-category-tabs').css({"display": "block"})

            var obj = $(this);
            var language = obj.attr('data-language');
            var activeTab = $('.tstranslation-category-tabs li.active').find('a');
            if (typeof activeTab.attr('href') !== 'undefined') {
                var href = activeTab.attr('href').split('#').pop();
                var existedListLanguage = $('#' + href + '').attr('data-language');

                if (typeof existedListLanguage === 'undefined' || existedListLanguage != language) {
                    $('#' + href + '[data-language="' + existedListLanguage + '"]').remove();
                    var url = thisUrl + 'getTranslationsByCategory';
                    var category = activeTab.attr('data-category');

                    $.ajax({
                        type: "POST",
                        url: url,
                        beforeSend: function () {
                            tstranslationStartLoader(activeTab.parents('.tstranslation-category-tabs').next('div.tab-content').find('.for-loader'));
                            activeTab.parents('.tstranslation-category-tabs').next('div.tab-content').find('.tab-pane').each(function () {
                                $(this).removeClass('active');
                            });
                        },
                        data: {'language': language, 'category': category, 'listId': href},
                        success: function (data) {
                            activeTab.parents('.tstranslation-category-tabs').next('div.tab-content').append(data);
                            tstranslationEndLoader(activeTab.parents('.tstranslation-category-tabs').next('div.tab-content').find('.for-loader'));
                        }
                    }, false)
                }
            }
        }
    });
    $(document).on('show.bs.tab', '.tstranslation-category-tabs li', function (e) {
        var obj = e.target;
        var tmp = obj.href.split('#');
        var href = tmp.pop();
        var existedListLanguage = $('#' + href + '').attr('data-language');
        var language = $('.ts-translate.active').attr('data-language');

        if (typeof language === 'undefined') {
            alert('Select language!');
        } else if (typeof existedListLanguage === 'undefined' || existedListLanguage != language) {
            $('#' + href + '[data-language="' + existedListLanguage + '"]').remove();
            var url = thisUrl + 'getTranslationsByCategory';
            var category = $(obj).attr('data-category');

            $.ajax({
                type: "POST",
                url: url,
                beforeSend: function () {
                    tstranslationStartLoader($(obj).parents('.tstranslation-category-tabs').next('div.tab-content').find('.for-loader'));
                    $(obj).parents('.tstranslation-category-tabs').next('div.tab-content').find('.tab-pane').each(function () {
                        $(this).removeClass('active');
                    });
                },
                data: {'language': language, 'category': category, 'listId': href},
                success: function (data) {
                    $(obj).parents('.tstranslation-category-tabs').next('div.tab-content').append(data);
                    tstranslationEndLoader($(obj).parents('.tstranslation-category-tabs').next('div.tab-content').find('.for-loader'));
                }
            }, false)
        }
        return true;
    });
    $(document).on('click', '.editable-google-translate', function (e) {
        var url = thisUrl + 'googleTranslate';
        var obj = $(this).parents('.control-group').find('.editable-input').find('textarea');
        var translatedText = obj.val();
        var language = $(".tstranslation-list .ts-translate.active").attr('data-language');
        $.ajax({
            type: "POST",
            url: url,
            beforeSend: tstranslationStartLoader(obj.parents('.control-group').find('.for-loader')),
            data: {"value": translatedText, "language": language},
            success: function (dataJson) {
                var data = JSON.parse(dataJson);
                if (data.ok == 1) {
                    obj.val(data.message);
                } else {
                    alert(data.message);
                }
                tstranslationEndLoader(obj.parents('.control-group').find('.for-loader'));
            }
        }, false);
    })
    $(document).on('click', '.google-translate-all', function (e) {
        var url = thisUrl + 'googleTranslate';
        var objArray = $(this).parents('table').find('.translate-div').find('.translate-value:not(".editable-unsaved")');
        if (objArray.length != 0) {
            var progressDiv = $(this).parents('.tab-pane.active').find('.progress');
            progressDiv.attr('style', 'display:block');

            multiTranslate(objArray, url, progressDiv);
        }

    })
    $(document).on('click', '.save-all', function (e) {
        var url = thisUrl + 'saveTranslate';
        var objArray = $(this).parents('table').find('.translate-div').find('.translate-value.editable-unsaved');
        if (objArray.length != 0) {
            var progressDiv = $(this).parents('.tab-pane.active').find('.progress');
            progressDiv.attr('style', 'display:block');

            multiSave(objArray, url, progressDiv);
        }

    })

    $(document).on('click', '.ts-add-language', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '#tsLangChangerForm a', function (e) {
        e.prventDefault;
        var obj = $(this);
        obj.parents('.ts-language-widget').find('.open').removeClass('open');
        if (!obj.hasClass('ts-current-lang-link')) {
            var newLang = obj.attr('data-language');
            $('input#tsNewLang').val(newLang);
            $('#tsLangChangerForm').submit();
            return false;
        }
    })

})
function multiTranslate(objArray, url, progressDiv) {
    var translatedText = '';
    var barPercent = 0;
    var i = 0;
    var totalCount = objArray.length;
    var language = $('.tstranslation-list .ts-translate.active').attr('data-language');
    var progressBar = progressDiv.find('.progress-bar');
    $.each(objArray, function () {
        var obj = $(this);
        var translatedText = obj.text();
        $.ajax({
            type: "POST",
            url: url,
            data: {"value": translatedText, "language": language},
            success: function (dataJson) {
                var data = JSON.parse(dataJson);
                if (data.ok == 1) {
                    i++;
                    barPercent = (i / totalCount) * 100;
                    barPercent = barPercent + '%';
                    progressBar.css({"width": barPercent});
                    if (translatedText != 'Empty') {
                        obj.text(data.message);

                        if (translatedText != data.message) {
                            $(obj).editable('setValue', data.message);
                            obj.addClass('editable-unsaved');
                        }
                    } else {
                        $(obj).editable('setValue', '');
                    }
                } else {
                    alert(data.message);
                }
                if (i == totalCount) {
                    setTimeout(function () {
                        progressDiv.slideUp('slow');
                        progressBar.css({"width": "0%"});
                    }, 2000);
                }
            }
        }, false);

    });
}
function multiSave(objArray, url, progressDiv) {
    var savedText = '';
    var pk = 0;
    var barPercent = 0;
    var i = 0;
    var totalCount = objArray.length;
    var language = $('.tstranslation-list .ts-translate.active').attr('data-language');
    var progressBar = progressDiv.find('.progress-bar');
    $.each(objArray, function () {
        var obj = $(this);
        var savedText = obj.text();
        var pk = obj.attr('data-pk');
        $.ajax({
            type: "POST",
            url: url,
            data: {"pk": pk, "value": savedText, "name": language},
            success: function (dataJson) {
                var data = JSON.parse(dataJson);
                if (data.ok == 1) {
                    i++;
                    obj.removeClass('editable-unsaved');
                    barPercent = (i / totalCount) * 100;
                    barPercent = barPercent + '%';
                    progressBar.css({"width": barPercent});
                } else {
                    alert(data.message);
                }
                if (i == totalCount) {
                    setTimeout(function () {
                        progressDiv.slideUp('slow');
                        progressBar.css({"width": "0%"});
                    }, 2000);
                }
            }
        }, false);
    });
}

function tstranslationStartLoader(obj) {
    obj.html('<img src="' + $('#tstranslationAssetsUrl').val() + '/images/ajax-loader2.gif">');
    $('body').css({'cursor': 'wait'});
}
function tstranslationEndLoader(obj) {
    obj.html('&nbsp;&nbsp;');
    $('body').css({'cursor': 'auto'});
}