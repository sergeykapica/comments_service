<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/init_files/controller_urls.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/misc_functions/data_handlers.php');

$oDataHandlers = new \DataHandlers\ODataHandlers;

class ControllerUrls
{
    function __construct($page)
    {
        global $oDataHandlers;
        $this->oDataHandlers = $oDataHandlers;
        
        if(preg_match('/admin/', $page))
        {
            session_start();
            
            if(!preg_match('/admin_authorizate/', $page) && !preg_match('/admin_authorizate_handler/', $page))
            {
                if(!isset($_SESSION['ADMIN_AUTHORIZED']))
                {
                    header('Location: /admin_authorizate');
                    die();
                }
            }
            else
            {
                if(isset($_SESSION['ADMIN_AUTHORIZED']))
                {
                    header('Location: /admin_panel');
                    die();
                }
            }
        }
    }
    
	public function get_index_page($page)
	{
        require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');
        
        $DBObject = new \DBObjectScope\DBObject;
        $arResult = array();
        $arResult['COMMENTS_LIST'] = $DBObject->getCommentsList();
        
		$this->renderingPage($page, 'Главная', $arResult);
	}
    
    public function get_ajax_comment_add_handler_page()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/misc_functions/images_upload.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');
            
            $oUploadImages = new \UploadImagesScope\UploadImages;
            $DBObject = new \DBObjectScope\DBObject;
            
            if(!empty($_FILES))
            {
                $result = array();
                
                foreach($_FILES as $fileName => $file)
                {
                    if($file['error'] <= 0)
                    {
                        if($fileName == 'USER_PHOTO')
                        {
                            $result[] = $oUploadImages->uploadImage($file, $_SERVER['DOCUMENT_ROOT'] . '/sources/images/users-images/', true);
                        }
                        else
                        {
                            $result[] = $oUploadImages->uploadImage($file, $_SERVER['DOCUMENT_ROOT'] . '/sources/images/other-images/');
                        }
                    }
                }
                
                if(isset(end($result)['ERROR']))
                {
                    echo json_encode(array(
                       'ERROR' => end($result)['ERROR']
                    ));
                    
                    die();
                }
                else
                {
                    $attachmentImages = '';
                    $userPhoto = '';
                    
                    foreach($result as $imageKey => $imageData)
                    {
                        if(isset($imageData['FILE_NAME']))
                        {
                            if(isset($imageData['USER_PHOTO']))
                            {
                                $userPhoto = $imageData['FILE_NAME'];
                            }
                            else if($imageKey < ( count($result) - 1 ))
                            {
                                $attachmentImages .= $imageData['FILE_NAME'] . ',';
                            }
                            else
                            {
                                $attachmentImages .= $imageData['FILE_NAME'];
                            }
                        }
                    }
                    
                    // request to DB and insert data
                    
                    $commentsListAddedFields = array(
                        'USER_NAME' => $this->oDataHandlers->stringCleanFromXSS($_POST['USER_NAME']),
                        'USER_EMAIL' => $this->oDataHandlers->stringCleanFromXSS($_POST['USER_EMAIL']),
                        'USER_COMMENT_TEXT' => $this->oDataHandlers->stringCleanFromXSS($_POST['USER_TEXT']),
                        'COMMENT_DATE' => time()
                    );
                    
                    if($userPhoto !== '')
                    {
                        $commentsListAddedFields['USER_PHOTO'] = $userPhoto;
                    }
                    
                    if($attachmentImages !== '')
                    {
                        $commentsListAddedFields['USER_ATTACHMENT_PHOTOS'] = $attachmentImages;
                    }
                    
                    if($DBObject->insertToCommentsList($commentsListAddedFields))
                    {
                        foreach($commentsListAddedFields as $fieldName => $fieldValue)
                        {
                            switch($fieldName)
                            {
                                case 'COMMENT_DATE':
                                    $commentsListAddedFields[$fieldName] = date('d.m.Y H:i:s', $fieldValue);
                                break;
                                    
                                case 'USER_PHOTO':
                                    $commentsListAddedFields[$fieldName] = '/sources/images/users-images/' . $fieldValue;
                                break;
                                    
                                case 'USER_ATTACHMENT_PHOTOS':
                                    $photoList = explode(',', $fieldValue);
                                    
                                    foreach($photoList as &$photo)
                                    {
                                        $photo = '/sources/images/other-images/' . $photo;
                                    }
                                    
                                    $commentsListAddedFields[$fieldName] = $photoList;
                                break;
                            }
                        }
                        
                        if(!isset($commentsListAddedFields['USER_PHOTO']))
                        {
                            $commentsListAddedFields['USER_PHOTO'] = '/sources/images/users-images/default.png';
                        }
                        
                        echo json_encode(array(
                            'DATA_ADD_SUCCESS' => true,
                            'ADDED_COMMENT' => $commentsListAddedFields
                        ));
                    }
                    else
                    {
                        echo json_encode(array(
                            'DATA_ADD_FAILED' => true
                        ));
                    }
                }
            }
        }
    }
    
    public function get_ajax_comment_assorted_list_page($page)
    {
        if(isset($_GET['SORT_BY']))
        {
            $sortBy = $this->oDataHandlers->stringCleanFromXSS($_GET['SORT_BY']);
            
            require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');
        
            $DBObject = new \DBObjectScope\DBObject;
            $arResult = array();
            $arResult['COMMENTS_LIST'] = $DBObject->getCommentsList($sortBy);
            
            $this->renderingAjaxPage($page, $arResult);
        }
    }
    
    public function get_admin_authorizate_page($page)
    {
        $this->renderingAdminPanelPage($page, 'Авторизация в админ панели');
    }
    
    public function get_admin_authorizate_handler_page($page)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');
            
            $DBObject = new \DBObjectScope\DBObject;
            
            $userLogin = $this->oDataHandlers->stringCleanFromXSS($_POST['USER_LOGIN']);
            $userPassword = $this->oDataHandlers->setValueHash($this->oDataHandlers->stringCleanFromXSS($_POST['USER_PASSWORD']));
            
            $fieldsToDB = array(
                'USER_LOGIN' => $userLogin,
                'USER_PASSWORD' => $userPassword
            );
            
            $result = $DBObject->checkAdminData($fieldsToDB);
            
            if($result)
            {
                $_SESSION['ADMIN_AUTHORIZED'] = $result;
                
                header('Location: /admin_panel');
                die();
            }
            else
            {
                header('Location: /admin_authorizate');
                die();
            }
        }
    }
    
    public function get_admin_panel_page($page)
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');
        
        $DBObject = new \DBObjectScope\DBObject;
        $arResult = array();
        $arResult['COMMENTS_LIST'] = $DBObject->getCommentsList();
        
        $this->renderingAdminPanelPage($page, 'Админ панель', $arResult);
    }
    
    public function get_admin_comment_detail_page($page)
    {
        if(isset($_GET['COMMENT_ID']))
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');

            $commentID = $this->oDataHandlers->stringCleanFromXSS($_GET['COMMENT_ID']);
            $DBObject = new \DBObjectScope\DBObject;
            $arResult = array();
            $arResult['COMMENT_DATA'] = $DBObject->getCommentByID($commentID);
            $arResult['CURRENT_PAGE'] = $page . '?COMMENT_ID=' . $commentID;
            
            if(isset($_GET['UPDATE_COMMENT']))
            {
                $arResult['UPDATE_COMMENT'] = $this->oDataHandlers->stringCleanFromXSS($_GET['UPDATE_COMMENT']);
            }
            
            if(!empty($arResult['COMMENT_DATA']))
            {
                if($arResult['COMMENT_DATA']['USER_ATTACHMENT_PHOTOS'] !== '')
                {
                    $arResult['COMMENT_DATA']['USER_ATTACHMENT_PHOTOS'] = explode(',', $arResult['COMMENT_DATA']['USER_ATTACHMENT_PHOTOS']);
                }
            }

            $this->renderingAdminPanelPage($page, 'Редактирование комментария', $arResult);
        }
    }
    
    public function get_admin_comment_edit_handler_page()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');

            $DBObject = new \DBObjectScope\DBObject;
            $commentID = $this->oDataHandlers->stringCleanFromXSS($_POST['COMMENT_ID']);
            $userName = $this->oDataHandlers->stringCleanFromXSS($_POST['USER_NAME']);
            $userCommentText = $this->oDataHandlers->stringCleanFromXSS($_POST['USER_COMMENT_TEXT']);
            $currentPage = '/' . preg_replace('/\?.+/', '', $this->oDataHandlers->stringCleanFromXSS($_POST['CURRENT_PAGE']));
            
            $fieldsToUpdate = array(
                'ID' => $commentID,
                'USER_NAME' => $userName,
                'USER_COMMENT_TEXT' => $userCommentText
            );
            
            if(isset($_POST['COMMENT_STATUS']))
            {
                $fieldsToUpdate['COMMENT_STATUS'] = $this->oDataHandlers->stringCleanFromXSS($_POST['COMMENT_STATUS']);
            }
            
            if($DBObject->updateComment($fieldsToUpdate))
            {
                header('Location: ' . $currentPage . '?COMMENT_ID=' . $commentID . '&UPDATE_COMMENT=1');
                die();
            }
            else
            {
                header('Location: ' . $currentPage . '?COMMENT_ID=' . $commentID . '&UPDATE_COMMENT=0');
                die();
            }
        }
    }
    
    public function renderingPage($page, $title, $arResult = false)
    {
        $pageTitle = $title;
        
        //header
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/static_views/header.php');
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/' . $page . '.php');
        
        //footer
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/static_views/footer.php');
    }
    
    public function renderingAjaxPage($page, $arResult = false)
    {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/' . $page . '.php');
    }
    
    public function renderingRandomPage($pathToPage, $title, $arResult = false)
    {
        $pageTitle = $title;
        
        include_once($pathToPage);
    }
    
    public function renderingAdminPanelPage($page, $title, $arResult = false)
    {
        $pageTitle = $title;
        
        //header
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/admin-panel/static_views/header.php');
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/admin-panel/' . $page . '.php');
        
        //footer
        
        include_once($_SERVER['DOCUMENT_ROOT'] . '/views/admin-panel/static_views/footer.php');
    }
}

if(isset($_GET['PAGE']))
{
	$page = $oDataHandlers->stringCleanFromXSS($_GET['PAGE']);
}
else
{
	$page = 'index';
}

if(in_array($page, \ControllerUrls\getUrlHandlers()))
{
	$method = 'get_' . $page . '_page';
	
	$ControllerUrls = new ControllerUrls($page);
	$ControllerUrls->$method($page);
}
else
{
?>
Страница не найдена
<?	
}
?>