<div class="page-header">
    <h1>Комментарии пользователей</h1>
</div>
<div class="content-separator"></div>
<div class="btn-group sort-button-wrapper">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Сортировка</button>
    <div class="dropdown-menu sort-button-menu">
        <a class="dropdown-item sort-by-name" href="#">По имени автора</a>
        <a class="dropdown-item sort-by-email" href="#">По электронной почте автора</a>
        <a class="dropdown-item sort-by-date" href="#">По дате добавления</a>
        <div class="dropdown-divider"></div>
    </div>
    <div class="spinner-border text-info" role="status" id="spinner-for-sort">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="content-separator"></div>
<div class="comments-list">
    <?if(!empty($arResult['COMMENTS_LIST'])):?>
        <?foreach($arResult['COMMENTS_LIST'] as $comment):?>
            <section class="comments-item">
                <div class="comments-item-head">
                    <div class="comments-icon rounded-circle" style="background-image: url(<?=( $comment['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $comment['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                    <div class="comments-head-data">
                        <div class="head-data head-data-headline"><?=$comment['USER_NAME'];?></div>
                        <div class="head-data head-data-date"><?=date('d.m.Y H:i:s', $comment['COMMENT_DATE']);?></div>
                    </div>
                    <button data-comment-id="<?=$comment['ID'];?>" class="comments-reply-button btn btn-info">
                        <div class="comments-reply-text">Цитировать</div>
                        <div class="comments-reply-icon"></div>
                    </button>
                </div>
                <div class="comments-item-content"><?=$comment['USER_COMMENT_TEXT'];?></div>
                
                <?
                    if($comment['USER_ATTACHMENT_PHOTOS'] !== '')
                    {
                        $comment['USER_ATTACHMENT_PHOTOS'] = explode(',', $comment['USER_ATTACHMENT_PHOTOS']);
                    }
                ?>
                
                <?if(is_array($comment['USER_ATTACHMENT_PHOTOS'])):?>
                    <div class="comments-attachment-photos">
                        <?foreach($comment['USER_ATTACHMENT_PHOTOS'] as $photoKey => $photoValue):?>
                                <div class="comments-attachment-photo <?=( ( ( $photoKey + 1 ) % 2 ) !== 0 ? 'attachment-photo-indentation' : '' );?>" style="background-image: url(/sources/images/other-images/<?=trim($photoValue);?>);"></div>
                        <?endforeach;?>
                    </div>
                <?endif;?>
                
                <?// reply comments list one level ?>

                <?if(isset($comment['REPLY_COMMENTS_LIST'])):?>
                    <div class="reply-comments-wrapper">
                        <?foreach($comment['REPLY_COMMENTS_LIST'] as $replyComment):?>
                            <section class="comments-item">
                                <div class="comments-item-head">
                                    <div class="comments-icon rounded-circle" style="background-image: url(<?=( $replyComment['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $replyComment['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                                    <div class="comments-head-data">
                                        <div class="head-data head-data-headline"><?=$replyComment['USER_NAME'];?></div>
                                        <div class="head-data head-data-date"><?=date('d.m.Y H:i:s', $replyComment['COMMENT_DATE']);?></div>
                                    </div>
                                </div>
                                <div class="comments-item-content"><?=$replyComment['USER_COMMENT_TEXT'];?></div>

                                <?
                                    if($comment['USER_ATTACHMENT_PHOTOS'] !== '')
                                    {
                                        $comment['USER_ATTACHMENT_PHOTOS'] = explode(',', $replyComment['USER_ATTACHMENT_PHOTOS']);
                                    }
                                ?>

                                <?if(is_array($replyComment['USER_ATTACHMENT_PHOTOS'])):?>
                                    <div class="comments-attachment-photos">
                                        <?foreach($replyComment['USER_ATTACHMENT_PHOTOS'] as $photoKey => $photoValue):?>
                                                <div class="comments-attachment-photo <?=( ( ( $photoKey + 1 ) % 2 ) !== 0 ? 'attachment-photo-indentation' : '' );?>" style="background-image: url(/sources/images/other-images/<?=trim($photoValue);?>);"></div>
                                        <?endforeach;?>
                                    </div>
                                <?endif;?>
                            </section>
                        <?endforeach;?>
                    </div>
                <?endif;?>
            </section>
        <?endforeach;?>
        <div class="clear-fix-separator"></div>
        <?=$arResult['PAGINATION_STRING'];?>
    <?else:?>
        <div class="comments-list-empty">Комментарии отсутствуют</div>
    <?endif;?>
</div>
<div class="clear-fix-separator"></div>
<div class="comments-form-wrapper">
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <form action="/ajax_comment_add_handler" method="post" enctype="multipart/form-data" id="comments-form">
                <div class="row comments-form-section">
                    <div class="col">
                        <input type="text" name="USER_NAME" class="form-control form-input" placeholder="Имя">
                    </div>
                </div>
                
                <div class="row comments-form-section">
                    <div class="col mb-3-unsetmargin">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">@</span>
                            </div>
                            <input type="text" name="USER_EMAIL" class="form-control form-input" placeholder="Электронная почта" aria-label="Электронная почта" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
                
                <?/*Прикрепить одну фотографию*/?>
                
                <div class="row comments-form-section">
                    <div class="col mb-3-unsetmargin">
                        <div class="input-group mb-3">
                            <input type="file" name="USER_PHOTO" class="form-file-input" id="form-file-input1"/>
                            <label class="input-elements-consolidate">
                                <input type="text" class="form-control files-name-insert" placeholder="Выберите файл" aria-label="Электронная почта" aria-describedby="basic-addon2" id="basic-addon2">
                                <div class="input-group-prepend file-icon-wrapper">
                                    <span class="input-group-text" id="basic-addon2">
                                        <div class="form-file-icon"></div>
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <?/*Прикрепить несколько изображений*/?>
                
                <div class="row comments-form-section">
                    <div class="col mb-3-unsetmargin">
                        <div class="input-group mb-3">
                            <input type="file" name="USER_ATTACHMENT_PHOTOS" class="form-file-input" id="form-file-input2" multiple/>
                            <label class="input-elements-consolidate">
                                <input type="text" class="form-control files-name-insert" placeholder="Выберите файл" aria-label="Электронная почта" aria-describedby="basic-addon3" id="basic-addon3">
                                <div class="input-group-prepend file-icon-wrapper">
                                    <span class="input-group-text" id="basic-addon3">
                                        <div class="form-additional-icon"></div>
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="row comments-form-section">
                    <div class="col">
                        <textarea name="USER_TEXT" class="form-control form-input form-textarea" placeholder="Текст комментария"></textarea>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                    <div class="spinner-border text-info" role="status" id="spinner-for-form">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="/sources/js/validate-form.js"></script>
<script type="text/javascript" src="/sources/js/upload-and-send-data-ajax.js"></script>
<script type="text/javascript" src="/sources/js/move-to-element.js"></script>

<script type="text/javascript">
    $(window).ready(function()
    {
        var formFileInput = $('.form-file-input');
        
        formFileInput.on('change', function()
        {
            var thisFileInput = $(this);
            var fileNamesInsertInput = thisFileInput.parent().find('.files-name-insert');
            
            if(thisFileInput.attr('multiple') === undefined)
            {
                fileNamesInsertInput.val(thisFileInput[0].files[0].name);
            }
            else
            {
                var files = thisFileInput[0].files;
                var selectedFilesName = '';
                
                for(var i in files)
                {
                    if(files[i].size !== undefined)
                    {
                        if(i < ( files.length - 1 ))
                        {
                            selectedFilesName += files[i].name + ', ';
                        }
                        else
                        {
                            selectedFilesName += files[i].name;
                        }
                    }
                }
                
                fileNamesInsertInput.val(selectedFilesName);
            }
        });
        
        // validation for form
        
        var OValidator = new oValidator($('.form-input'), {
            
            'USER_NAME':
            {
                minSymbolLength: 3,
                maxSymbolLength: 100
            },
            
            'USER_EMAIL':
            {
                minSymbolLength: 3,
                maxSymbolLength: 200,
                isEmail: true
            },
            
            'USER_TEXT':
            {
                minSymbolLength: 3,
                maxSymbolLength: 2000
            }
        }, 'col', 'form-textarea');
        
        var commentsForm = $('#comments-form');
        
        commentsForm.on('submit', function()
        {
            var thisForm = $(this);
            var errors = thisForm.find('.validator-error');
            
            if(errors[0] !== undefined)
            {
                errors.remove();
            }

            OValidator.checkRequiredInputs();
            
            errors = thisForm.find('.validator-error');
            
            if(errors[0] !== undefined)
            {
                return false;
            }
            
            var formData = new FormData(thisForm[0]);
            var userPhotoFile = $('#form-file-input1');
            
            if(userPhotoFile[0].files.length > 0)
            {
                formData.append(userPhotoFile.attr('name'), userPhotoFile[0].files[0]);
            }
            
            var userPhotoFiles = $('#form-file-input2');
            
            if(userPhotoFiles[0].files.length > 0)
            {
                for(var f in userPhotoFiles[0].files)
                {
                    if(userPhotoFiles[0].files[f].size !== undefined)
                    {
                        if(f != 0)
                        {
                            formData.append(userPhotoFiles.attr('name') + f, userPhotoFiles[0].files[f]);
                        }
                        else
                        {
                            formData.append(userPhotoFiles.attr('name'), userPhotoFiles[0].files[f]);
                        }
                    }
                }
            }
            
            uploadAndSendData(thisForm.attr('action'), formData, function()
            {
                let result = JSON.parse(this.xhr.responseText);
                
                if(result.DATA_ADD_SUCCESS !== undefined)
                {
                    let messageElement = this.oValidator.setSuccessNotification($('textarea[name=USER_TEXT]'), 'Комментарий успешно добавлен');
                    
                    setTimeout(function()
                    {
                        $(messageElement).fadeOut(1000, function()
                        {
                            $(this).remove();
                        });
                    }, 3000);
                    
                    let attachmentPhotoList = '<div class="comments-attachment-photos">';
                    
                    for(var p in result.ADDED_COMMENT.USER_ATTACHMENT_PHOTOS)
                    {
                        let k = ( parseInt(p) + 1 );
                        let indentation = ( ( k % 2 ) != 0 ? 'attachment-photo-indentation' : '' );
                        
                        attachmentPhotoList +=
                        `
                        <div class="comments-attachment-photo ` + indentation + `" style="background-image: url(` + result.ADDED_COMMENT.USER_ATTACHMENT_PHOTOS[p] + `);"></div>
                        `;
                    }
                    
                    attachmentPhotoList += '</div>';
                    
                    let commentsList = $('.comments-list');
                    
                    let replyCommentID = thisForm.find('#reply-message-id');
                    
                    if(replyCommentID[0] === undefined)
                    {
                        /*let comment =
                        `
                        <section class="comments-item">
                            <div class="comments-item-head">
                                <div class="comments-icon rounded-circle" style="background-image: url(` + result.ADDED_COMMENT.USER_PHOTO + `);"></div>
                                <div class="comments-head-data">
                                    <div class="head-data head-data-headline">` + result.ADDED_COMMENT.USER_NAME + `</div>
                                    <div class="head-data head-data-date">` + result.ADDED_COMMENT.COMMENT_DATE + `</div>
                                </div>
                                <button class="comments-reply-button btn btn-info">
                                    <div class="comments-reply-text">Цитировать</div>
                                    <div class="comments-reply-icon"></div>
                                </button>
                            </div>
                            <div class="comments-item-content">` + result.ADDED_COMMENT.USER_COMMENT_TEXT + `</div>
                            ` + attachmentPhotoList + `
                        </section>
                        `;

                        commentsList.prepend(comment);*/
                    }
                    else
                    {
                        let comment =
                        `
                        <section class="comments-item">
                            <div class="comments-item-head">
                                <div class="comments-icon rounded-circle" style="background-image: url(` + result.ADDED_COMMENT.USER_PHOTO + `);"></div>
                                <div class="comments-head-data">
                                    <div class="head-data head-data-headline">` + result.ADDED_COMMENT.USER_NAME + `</div>
                                    <div class="head-data head-data-date">` + result.ADDED_COMMENT.COMMENT_DATE + `</div>
                                </div>
                            </div>
                            <div class="comments-item-content">` + result.ADDED_COMMENT.USER_COMMENT_TEXT + `</div>
                            ` + attachmentPhotoList + `
                        </section>
                        `;
                        
                        let replyCommentIDButton = $('button.comments-reply-button[data-comment-id=' + replyCommentID.val() + ']');
                        
                        while(!replyCommentIDButton.hasClass('comments-item'))
                        {
                            replyCommentIDButton = replyCommentIDButton.parent();
                        }
                        
                        let replyCommentsWrapper = replyCommentIDButton.find('.reply-comments-wrapper');
                        
                        if(replyCommentsWrapper[0] !== undefined)
                        {
                            replyCommentsWrapper.prepend(comment);
                        }
                        else
                        {
                            replyCommentsWrapper = '<div class="reply-comments-wrapper">' + comment + '</div>';
                            
                            replyCommentIDButton.append(replyCommentsWrapper);
                        }
                    }
                    
                    // set elements to default status
                    
                    this.thisForm.find('input').val('');
                    this.thisForm.find('textarea').val('');
                    this.spinner.removeClass('spinner-border-show');
                    this.spinner.addClass('spinner-border-hide');
                }
                else if(result.DATA_ADD_FAILED !== undefined)
                {
                    this.oValidator.setErrorNotification($('textarea[name=USER_TEXT]'), 'При добавлении комментария возникла ошибка');
                }
                else
                {
                    this.oValidator.setErrorNotification($('#form-file-input2'), result.ERROR);
                }
            }, OValidator, thisForm, $('#spinner-for-form'));
            
            return false;
        });
        
        function getSortAssortedComments(by, containerToInsert)
        {
            let spinnerForSort = $('#spinner-for-sort'); 
            spinnerForSort.removeClass('spinner-border-hide');
            spinnerForSort.addClass('spinner-border-show');
            
            $.ajax({
                url: '/ajax_comment_assorted_list?SORT_BY=' + by,
                method: 'GET',
                success: function(res)
                {
                    if(res != false)
                    {
                        containerToInsert.html(res);
                        spinnerForSort.removeClass('spinner-border-show');
                        spinnerForSort.addClass('spinner-border-hide');
                    }
                }
            });
        }

        var sortButtonMenu = $('.sort-button-menu');

        sortButtonMenu.on('click', function(e)
        {
            let target = $(e.target);
            let commentsList = $('.comments-list');

            if(target.hasClass('sort-by-name'))
            {
                getSortAssortedComments('name', commentsList);
            }
            else if(target.hasClass('sort-by-email'))
            {
                getSortAssortedComments('email', commentsList);
            }
            else if(target.hasClass('sort-by-date'))
            {
                getSortAssortedComments('date', commentsList);
            }
        });
        
        let commentsReplyButton = $('.comments-reply-button');
        
        commentsReplyButton.on('click', function()
        {
            let thisButton = $(this);
            let replyMessageID = commentsForm.find('#reply-message-id');
            
            if(replyMessageID[0] !== undefined)
            {
                replyMessageID.remove();
            }
            
            let replyMessageIDElement = document.createElement('input');
            replyMessageIDElement.type = 'hidden';
            replyMessageIDElement.id = 'reply-message-id';
            replyMessageIDElement.name = 'REPLY_COMMENT_ID';
            replyMessageIDElement.value = thisButton.attr('data-comment-id');
            
            commentsForm.append(replyMessageIDElement);
            
            moveToElement($(document.documentElement || document.body), commentsForm, 1000);
        });
    });
</script>