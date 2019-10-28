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
        $sql = 'SELECT ID, USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, MODIFIED_BY_ADMIN, COMMENT_STATUS, COMMENT_DATE FROM '
                . $this->commentsTableTitle . ' WHERE ID = :ID';
        
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
    
    // set in to comments list
    
    public function insertToCommentsList(array $commentFields)
    {
        $sql = 'INSERT INTO ' . $this->commentsTableTitle . ' (USER_NAME, USER_EMAIL, USER_PHOTO, USER_ATTACHMENT_PHOTOS, USER_COMMENT_TEXT, REPLY_COMMENT_ID, COMMENT_DATE) '
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