<?
namespace DBObjectScope;

class DBObject extends \PDO
{
    private $settingsToConnect = array(
        'CONNECTION_DATA' => 'mysql:host=localhost;port=3306;dbname=comments-service;charset=utf8',
        'LOGIN' => 'root',
        'PASSWORD' => ''
    );
    
    public $commentsTableTitle = 'comments_list';
    public $adminDataTableTitle = 'admin_data';
    public $replyCommentsTableTitle = 'reply_comments_list';
    
    public function __construct()
    {
        parent::__construct($this->settingsToConnect['CONNECTION_DATA'], $this->settingsToConnect['LOGIN'], $this->settingsToConnect['PASSWORD']);
    }
    
    // get comments list
    
    public function getCommentsList($by = false)
    {
        $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                . $this->commentsTableTitle;
        
        if($by == false || $by == 'date')
        {
            $sql .= ' ORDER BY COMMENT_DATE DESC';
        }
        else if($by == 'name')
        {
            $sql .= ' ORDER BY USER_NAME ASC';
        }
        else if($by == 'email')
        {
            $sql .= ' ORDER BY USER_EMAIL ASC';
        }
        
        $sqlPrepare = $this->prepare($sql);
        
        if($sqlPrepare->execute())
        {
            return $sqlPrepare->FetchAll();
        }
        else
        {
            //$sqlPrepare->errorInfo();
            
            return false;
        }
    }
    
    // get comment by ID
    
    public function getCommentByID($commentID)
    {
        $sql = 'SELECT CONSOLIDATE_T.ID, CONSOLIDATE_T.USER_NAME, CONSOLIDATE_T.USER_EMAIL, CONSOLIDATE_T.USER_PHOTO, CONSOLIDATE_T.USER_ATTACHMENT_PHOTOS, CONSOLIDATE_T.USER_COMMENT_TEXT, CONSOLIDATE_T.REPLY_COMMENT_ID, CONSOLIDATE_T.MODIFIED_BY_ADMIN, CONSOLIDATE_T.COMMENT_STATUS, CONSOLIDATE_T.COMMENT_DATE FROM ( SELECT CT.ID, CT.USER_NAME, CT.USER_EMAIL, CT.USER_PHOTO, CT.USER_ATTACHMENT_PHOTOS, CT.USER_COMMENT_TEXT, NULL as REPLY_COMMENT_ID, CT.MODIFIED_BY_ADMIN, CT.COMMENT_STATUS, CT.COMMENT_DATE FROM ' . $this->commentsTableTitle . ' CT UNION SELECT RCT.ID, RCT.USER_NAME, RCT.USER_EMAIL, RCT.USER_PHOTO, RCT.USER_ATTACHMENT_PHOTOS, RCT.USER_COMMENT_TEXT, RCT.REPLY_COMMENT_ID, RCT.MODIFIED_BY_ADMIN, RCT.COMMENT_STATUS, RCT.COMMENT_DATE FROM ' . $this->replyCommentsTableTitle . ' RCT ) as CONSOLIDATE_T WHERE CONSOLIDATE_T.ID = :ID';
        
        $sqlPrepare = $this->prepare($sql);
        
        $prepareValues = array(
            ':ID' => $commentID
        );
        
        if($sqlPrepare->execute($prepareValues))
        {
            return $sqlPrepare->fetch();
        }
        else
        {
            return false;
        }
    }
    
    // set to reply comments list
    
    public function insertToReplyCommentsList(array $commentFields)
    {
        $sql = 'INSERT INTO ' . $this->replyCommentsTableTitle . ' (USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, COMMENT_DATE) '
                . 'VALUES (:USER_NAME, :USER_EMAIL, :USER_PHOTO, :USER_ATTACHMENT_PHOTOS, :USER_COMMENT_TEXT, :REPLY_COMMENT_ID, :COMMENT_DATE)';
        $sqlPrepare = $this->prepare($sql);
        
        $prepareValues = array(
            ':USER_NAME' => $commentFields['USER_NAME'],
            ':USER_EMAIL' => $commentFields['USER_EMAIL'],
            ':USER_PHOTO' => ( isset($commentFields['USER_PHOTO']) ? $commentFields['USER_PHOTO'] : '' ),
            ':USER_ATTACHMENT_PHOTOS' => ( isset($commentFields['USER_ATTACHMENT_PHOTOS']) ? $commentFields['USER_ATTACHMENT_PHOTOS'] : '' ),
            ':USER_COMMENT_TEXT' => $commentFields['USER_COMMENT_TEXT'],
            ':REPLY_COMMENT_ID' => ( isset($commentFields['REPLY_COMMENT_ID']) ? $commentFields['REPLY_COMMENT_ID'] : 0 ),
            ':COMMENT_DATE' => $commentFields['COMMENT_DATE']
        );
        
        $this->beginTransaction();
        
        if($sqlPrepare->execute($prepareValues))
        {
            $this->commit();
            
            return true;
        }
        else
        { 
            $this->rollBack();
            
            return false;
        }  
    }
    
    // set in to comments list
    
    public function insertToCommentsList(array $commentFields)
    {
        $sql = 'INSERT INTO ' . $this->commentsTableTitle . ' (USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, COMMENT_DATE) '
                . 'VALUES (:USER_NAME, :USER_EMAIL, :USER_PHOTO, :USER_ATTACHMENT_PHOTOS, :USER_COMMENT_TEXT, :COMMENT_DATE)';
        $sqlPrepare = $this->prepare($sql);
        
        $prepareValues = array(
            ':USER_NAME' => $commentFields['USER_NAME'],
            ':USER_EMAIL' => $commentFields['USER_EMAIL'],
            ':USER_PHOTO' => ( isset($commentFields['USER_PHOTO']) ? $commentFields['USER_PHOTO'] : '' ),
            ':USER_ATTACHMENT_PHOTOS' => ( isset($commentFields['USER_ATTACHMENT_PHOTOS']) ? $commentFields['USER_ATTACHMENT_PHOTOS'] : '' ),
            ':USER_COMMENT_TEXT' => $commentFields['USER_COMMENT_TEXT'],
            ':COMMENT_DATE' => $commentFields['COMMENT_DATE']
        );
        
        $this->beginTransaction();
        
        if($sqlPrepare->execute($prepareValues))
        {
            $this->commit();
            
            return true;
        }
        else
        { 
            $this->rollBack();
            
            return false;
        }  
    }
    
    // update comment by ID
    
    public function updateComment(array $commentFields)
    {
        if(isset($commentFields['COMMENT_STATUS']))
        {
            $sql = 'UPDATE ' . $this->commentsTableTitle . ' SET USER_NAME = :USER_NAME, USER_COMMENT_TEXT = :USER_COMMENT_TEXT, MODIFIED_BY_ADMIN = :MODIFIED_BY_ADMIN, '
                    . 'COMMENT_STATUS = :COMMENT_STATUS WHERE ID = :ID';
            $sqlPrepare = $this->prepare($sql);
            
            $prepareValues = array(
                ':ID' => $commentFields['ID'],
                ':USER_NAME' => $commentFields['USER_NAME'],
                ':USER_COMMENT_TEXT' => $commentFields['USER_COMMENT_TEXT'],
                ':MODIFIED_BY_ADMIN' => 1,
                ':COMMENT_STATUS' => $commentFields['COMMENT_STATUS']
            );
        }
        else
        {
            $sql = 'UPDATE ' . $this->commentsTableTitle . ' SET USER_NAME = :USER_NAME, USER_COMMENT_TEXT = :USER_COMMENT_TEXT, MODIFIED_BY_ADMIN = :MODIFIED_BY_ADMIN WHERE ID = :ID';
            $sqlPrepare = $this->prepare($sql);
            
            $prepareValues = array(
                ':ID' => $commentFields['ID'],
                ':USER_NAME' => $commentFields['USER_NAME'],
                ':USER_COMMENT_TEXT' => $commentFields['USER_COMMENT_TEXT'],
                ':MODIFIED_BY_ADMIN' => 1
            );
        }
        
        $this->beginTransaction();
        
        if($sqlPrepare->execute($prepareValues))
        {
            $this->commit();
            
            return true;
        }
        else
        { 
            $this->rollBack();
            
            return false;
        }  
    }
    
    // delete comments by ids
    
    public function deleteCommentsByIDs($commentIds)
    {
        $sql = 'DELETE FROM ' . $this->commentsTableTitle . ' WHERE ID = :ID';
        $sqlPrepare = $this->prepare($sql);
        
        foreach($commentIds as $id)
        {
            $this->beginTransaction();
            
            $prepareValues = array(
                ':ID' => $id
            );
            
            if($sqlPrepare->execute($prepareValues))
            {
                $this->commit();
            }
            else
            { 
                $this->rollBack();

                return false;
            }  
        }
        
        return true;
    }
    
    // get comments with pagination
    
    public function getAllCommentsWithPagination($offset, $elementsOnPage)
    {
        $sql = 'SELECT CONSOLIDATE_T.ID, CONSOLIDATE_T.USER_NAME, CONSOLIDATE_T.USER_EMAIL, CONSOLIDATE_T.USER_PHOTO, '
                . 'CONSOLIDATE_T.USER_ATTACHMENT_PHOTOS, CONSOLIDATE_T.USER_COMMENT_TEXT, CONSOLIDATE_T.REPLY_COMMENT_ID, '
                . 'CONSOLIDATE_T.MODIFIED_BY_ADMIN, CONSOLIDATE_T.COMMENT_STATUS, CONSOLIDATE_T.COMMENT_DATE FROM '
                . '( SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, NULL as REPLY_COMMENT_ID, '
                . 'MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM ' . $this->commentsTableTitle . ' UNION SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, '
                . 'USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                . $this->replyCommentsTableTitle . ' ) as CONSOLIDATE_T ORDER BY CONSOLIDATE_T.COMMENT_DATE DESC LIMIT ' . $offset . ', ' . $elementsOnPage;
        $sqlPrepare = $this->prepare($sql);
        
        $sqlCount = 'SELECT COUNT(*) as ALL_ELEMENTS_COUNT FROM ' . $this->commentsTableTitle . ' UNION SELECT COUNT(*) FROM ' .  $this->replyCommentsTableTitle;
        $sqlPrepareCount = $this->prepare($sqlCount);
        
        if($sqlPrepare->execute() && $sqlPrepareCount->execute())
        {
            $sqlResult = $sqlPrepare->FetchAll();
            $sqlCountResult = $sqlPrepareCount->FetchAll();

            return array(
                'ELEMENTS_LIST' => $sqlResult,
                'ALL_ELEMENTS_COUNT' => ( $sqlCountResult[0]['ALL_ELEMENTS_COUNT'] + $sqlCountResult[1]['ALL_ELEMENTS_COUNT'] )
            );
        }
        else
        {
            return false;
        }
    }
    
    public function getMainCommentsWithPagination($offset, $elementsOnPage, $sortParams = false, $moreParams = false)
    { 
        if($sortParams == false || $sortParams == 'date')
        {
            if($moreParams != false)
            {
                if(isset($moreParams['COMMENT_STATUS']))
                {
                    $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                        . $this->commentsTableTitle . ' WHERE COMMENT_STATUS = ' . $moreParams['COMMENT_STATUS'] . ' ORDER BY COMMENT_DATE DESC LIMIT ' . $offset . ', ' . $elementsOnPage;
                }
            }
        }
        else if($sortParams == 'name')
        {
            if($moreParams != false)
            {
                if(isset($moreParams['COMMENT_STATUS']))
                {
                    $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                        . $this->commentsTableTitle . ' WHERE COMMENT_STATUS = ' . $moreParams['COMMENT_STATUS'] . ' ORDER BY USER_NAME ASC LIMIT ' . $offset . ', ' . $elementsOnPage;
                }
            }
        }
        else if($sortParams == 'email')
        {
            if($moreParams != false)
            {
                if(isset($moreParams['COMMENT_STATUS']))
                {
                    $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                        . $this->commentsTableTitle . ' WHERE COMMENT_STATUS = ' . $moreParams['COMMENT_STATUS'] . ' ORDER BY USER_EMAIL ASC LIMIT ' . $offset . ', ' . $elementsOnPage;
                }
            }
        }
        
        $sqlPrepare = $this->prepare($sql);
        
        if($moreParams != false)
        {
            if(isset($moreParams['COMMENT_STATUS']))
            {
                $sqlCount = 'SELECT COUNT(*) as ALL_ELEMENTS_COUNT FROM ' . $this->commentsTableTitle . ' WHERE COMMENT_STATUS = ' . $moreParams['COMMENT_STATUS'];
                $sqlPrepareCount = $this->prepare($sqlCount);
            }
        }
        
        if($sqlPrepare->execute() && $sqlPrepareCount->execute())
        {
            $sqlResult = $sqlPrepare->FetchAll();
            $sqlCountResult = $sqlPrepareCount->FetchAll();

            return array(
                'ELEMENTS_LIST' => $sqlResult,
                'ALL_ELEMENTS_COUNT' => $sqlCountResult[0]['ALL_ELEMENTS_COUNT']
            );
        }
        else
        {
            return false;
        }
    }
    
    public function getReplyCommentsList(array $mainCommentsIDs)
    {
        $mainCommentsIDs = implode(',', $mainCommentsIDs);
        
        $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                . $this->replyCommentsTableTitle . ' WHERE REPLY_COMMENT_ID IN(' . $mainCommentsIDs . ') ORDER BY COMMENT_DATE DESC';
        
        $sqlPrepare = $this->prepare($sql);
        
        if($sqlPrepare->execute())
        {  
            return $sqlPrepare->FetchAll();
        }
        else
        {
            return false;
        }
    }
    
    // check admin data
    
    public function checkAdminData(array $fields)
    {
        $sql = 'SELECT ID, USER_LOGIN, USER_PASSWORD FROM ' . $this->adminDataTableTitle . ' WHERE USER_LOGIN = :USER_LOGIN AND USER_PASSWORD = :USER_PASSWORD';
        $sqlPrepare = $this->prepare($sql);
        
        $prepareValues = array(
            ':USER_LOGIN' => $fields['USER_LOGIN'],
            ':USER_PASSWORD' => $fields['USER_PASSWORD']
        );
        
        if($sqlPrepare->execute($prepareValues))
        {
            $result = $sqlPrepare->fetch();
            
            if($result)
            {
                return $result['USER_PASSWORD'];
            }
            else
            {
                return false;
            }
        }
        else
        { 
            return false;
        }
    }
}
?>