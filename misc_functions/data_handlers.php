<?
namespace DataHandlers;

class ODataHandlers
{
    private $hashSalt = 'SergeySitov';
    private $valueApplyHashIterationCount = 100;
    
	public function stringCleanFromXSS($str)
	{
		return htmlspecialchars(strip_tags(trim($str)));
	}
    
    public function setValueHash($value)
    {
        $hashValue = $value;
        
        for($i = 0; $i <= $this->valueApplyHashIterationCount; $i++)
        {
            $hashValue = md5($hashValue . $this->hashSalt);
        }
        
        return $hashValue;
    }
}
?>