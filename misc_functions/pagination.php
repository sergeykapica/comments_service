<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/model/DBObject.php');

class Pagination
{
    public function __construct($currentPageNumber, $elementsOnPage, $currentPage, $segmentOfPagesCount)
    {
        $this->currentPageNumber = $currentPageNumber - 1;
        $this->elementsOnPage = $elementsOnPage;
        $this->currentPage = $currentPage;
        $this->segmentOfPagesCount = $segmentOfPagesCount;
        $this->DBObject = new \DBObjectScope\DBObject;
    }
    
    public function getPaginationString($functionFromDB, $sortParams = false, $moreParams = false)
    {
        $offset = $this->currentPageNumber * $this->elementsOnPage;
        
        if($sortParams == false)
        {
            $elementsList = $this->DBObject->$functionFromDB($offset, $this->elementsOnPage, false, $moreParams);
        }
        else
        {
            $elementsList = $this->DBObject->$functionFromDB($offset, $this->elementsOnPage, $sortParams, $moreParams);
        }
        
        if($elementsList)
        {
            if(!empty($elementsList['ELEMENTS_LIST']))
            {
                $this->allElementsCount = $elementsList['ALL_ELEMENTS_COUNT'];
                $paginationString = ( $sortParams == false ? $this->generatePaginationString() : $this->generatePaginationString($sortParams) );
                
                return array(
                    'PAGINATION_ELEMENTS' => $elementsList['ELEMENTS_LIST'],
                    'PAGINATION_STRING' => $paginationString
                );
            }
        }
        
        return false;
    }
    
    public function generatePaginationString($sortBy = false)
    {
        $countPages = ceil($this->allElementsCount / $this->elementsOnPage);
        $navigationString = '<nav aria-label="..." class="pagination-wrapper"><ul class="pagination">';
        
        if($this->currentPageNumber == 0)
        {
            $navigationString .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Предыдущая</a></li>';
        }
        else
        {
            $navigationString .= '<li class="page-item"><a class="page-link" href="' . $this->currentPage . '?N=' . $this->currentPageNumber . ( $sortBy != false ? '&SORT_BY=' . $sortBy : '' ) . '">Предыдущая</a></li>';
        }
        
        // start offset
        
        if( ( $this->currentPageNumber + 1 ) > $this->segmentOfPagesCount)
        {
             $i = ( $this->currentPageNumber + 1 ) - ( $this->segmentOfPagesCount - 1 );
        }
        else
        {
             $i = 1;
        }
        
        // end offset
        
        if(( $this->currentPageNumber + 1 ) + $this->segmentOfPagesCount <= $countPages)
        {
            $countPages = ( $this->currentPageNumber + 1 ) + $this->segmentOfPagesCount;
        }
        
        while($i <= $countPages)
        {
            if(( $this->currentPageNumber + 1 ) == $i)
            {
                $navigationString .= ' <li class="page-item active" aria-current="page"><a class="page-link" href="#">' . $i . ' <span class="sr-only">(current)</span></a></li>';
            }
            else
            {
                $navigationString .= '<li class="page-item"><a class="page-link" href="' . $this->currentPage . '?N=' . $i . ( $sortBy != false ? '&SORT_BY=' . $sortBy : '' ) . '">' . $i . '</a></li>';
            }
            
            $i++;
        }
        
        if(( $this->currentPageNumber + 1 ) == $countPages)
        {
            $navigationString .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Следующая</a></li>';
        }
        else
        {
            $navigationString .= '<li class="page-item"><a class="page-link" href="' . $this->currentPage . '?N=' . ( $this->currentPageNumber + 2 ) . ( $sortBy != false ? '&SORT_BY=' . $sortBy : '' ) . '">Следующая</a></li>';
        }
        
        $navigationString .= '</ul></nav>';
        
        return $navigationString;
    }
}
?>