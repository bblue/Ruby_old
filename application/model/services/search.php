<?php
namespace Model\Services;

use App\ServiceAbstract;

final class Search extends ServiceAbstract
{
	public $aFilters = array();
	private $aFilterMap = array();
	private $aFulltextMatches = array();
	private $sFulltextSearch = '';

	private $_aResultData = array();
	private $_aSearchData = array();

	const DEFAULT_RESULTS_PER_PAGE = 10;

	public function __construct()
	{
		$this->_aResultData['search_time_start'] = microtime(true);
	}

	public function addFilter($sField, $value, $iFilterLink = 0, $sOperator = '=', $sFilterLinkOperator = 'AND')
	{
		$sTable = null;
		if(sizeof($parts = explode('.', $sField))==2) {
			list($sTable, $sField) = $parts;
		}
		if(!array_key_exists($sTable, $this->aFilters)) {
			$this->setupFilterArray($sTable);
		}
		$this->aFilters[$sTable][] = array(
			'field' 	=> $sField,
			'operator'	=> $sOperator,
			'value'		=> $value,
			'filterlink'=> $iFilterLink,
			'filteroperator'=>$sFilterLinkOperator,
			'db_field'	=> null,
		);
		end($this->aFilters[$sTable]);
		$id = key($this->aFilters[$sTable]);

		return $id;
	}
	private function setupFilterArray($sTable)
	{
		$this->aFilters[$sTable][] = array(
			'field' 	=> '',
			'operator'	=> '',
			'value'		=> '',
			'filterlink'=> false,
			'filteroperator'=>'',
			'db_field'	=> null,
		);
	}
	public function getFilters($key = null)
	{
		return array_key_exists($key, $this->aFilters) ? $this->aFilters[$key] : array();
	}

	public function getFulltextMatches($key = null)
	{
		return isset($this->aFulltextMatches[$key]) ? $this->aFulltextMatches[$key] : null;
	}

	public function getFulltextSearch()
	{
		return $this->sFulltextSearch;
	}

	public function getResult($key = 0)
	{
		if(isset($this->_aResultData['result'])) {
			$start = microtime(true);
			// Split result in pages
			$iPage = $this->getRequestedPage() ? : 1;
			if(!isset($this->_aResultData['result_pages'][$iPage - 1])) {
				$aResultArray = $this->_aResultData['result']->toArray();
				usort($aResultArray, array($this, 'make_cmp'));
				$this->_aResultData['result_pages'] = array_chunk($aResultArray, $this->getResultsPerPage(), false);
			}

			if(!isset($this->_aResultData['result_pages'][$iPage - 1])) {
				$iPage = 1;
			}

			if(!empty($this->_aResultData['result_pages'][$iPage - 1])) {
				$this->_aResultData['result']->clear();
				foreach($this->_aResultData['result_pages'][$iPage - 1] as $resultObject) {
					$this->_aResultData['result']->add(null, $resultObject);
				}
			}
			$this->_aResultData['search_time'] += microtime(true) - $start;

			return $this->_aResultData['result'];
		}
	}

	public function getSearchTime()
	{
		return (isset($this->_aResultData['search_time'])) ? $this->_aResultData['search_time'] : null;
	}

	public function addFulltextMatch($sField)
	{
		$sTable = null;
		if(sizeof($parts = explode('.', $sField))==2) {
			list($sTable, $sField) = $parts;
		}

		$this->aFulltextMatches[$sTable][] = $sField;
		return $this;
	}

	public function setFulltextSearch($sSearchString)
	{
		$this->sFulltextSearch = $sSearchString;
		return $this;
	}

	public function setResult($result, $key = 0)
	{
		$this->_aResultData['result'] = $result;
		$this->_aResultData['total_res_count'] = $result->count();
		$this->_aResultData['search_time'] = microtime(true) - $this->_aResultData['search_time_start'];
	}

	######### functions by _get ###########
	public function setResultsPerPage($iResultsPerPage = self::DEFAULT_RESULTS_PER_PAGE)
	{
		return $this->_aSearchData['results_per_page'] = $iResultsPerPage;
	}

	public function getResultsPerPage()
	{
		return isset($this->_aSearchData['results_per_page']) ? $this->_aSearchData['results_per_page'] : $this->setResultsPerPage();
	}

	public function setRequestedPage($iRequestedPage)
	{
		$this->_aSearchData['requested_page'] = $iRequestedPage;
	}
	private function getRequestedPage()
	{
		return isset($this->_aSearchData['requested_page']) ? $this->_aSearchData['requested_page'] : false;
	}

	public function setOffset($iOffset = null)
	{
		return $this->_aSearchData['offset'] = $iOffset;
	}
	public function getOffset()
	{
		return isset($this->_aSearchData['offset']) ? $this->_aSearchData['offset'] : $this->setOffset();
	}

	public function setLimit($iLimit = null)
	{
		return $this->_aSearchData['limit'] = $iLimit;
	}
	public function getLimit()
	{
		return isset($this->_aSearchData['limit']) ? $this->_aSearchData['limit'] : $this->setLimit();
	}

	public function setOrder($sOrder = null)
	{
		$sOrder = strtoupper($sOrder);
		$sOrder = ($sOrder  === 'DESC' || $sOrder === 'ASC') ? $sOrder : null;
		return $this->_aSearchData['order'] = $sOrder;
	}
	public function getOrder()
	{
		return isset($this->_aSearchData['order']) ? $this->_aSearchData['order'] : $this->setOrder();
	}

	public function setOrderBy($sOrderBy = null)
	{
		return $this->_aSearchData['order_by'] = $sOrderBy;
	}
	public function getOrderBy()
	{
		return isset($this->_aSearchData['order_by']) ? $this->_aSearchData['order_by'] : $this->setOrderBy();
	}

	######### statistics #############
	private function setTotalResultCount($key)
	{
		return $this->_aResultData['total_res_count'] = count($this->getResult());
	}
	public function getTotalResultCount($key = 0)
	{
		return (isset($this->_aResultData['total_res_count'])) ? $this->_aResultData['total_res_count'] : $this->setTotalResultCount($key);
	}

	public function getTotalPageCount($key = 0)
	{
		return (isset($this->_aResultData['total_page_count'])) ? $this->_aResultData['total_page_count'] : $this->setTotalPageCount($key);
	}
	private function setTotalPageCount($key)
	{
		return $this->_aResultData['total_page_count'] = (($iResCount = $this->getTotalResultCount($key)) > 0) ? ceil($iResCount / $this->getResultsPerPage()) : 0;
	}

	public function selectPage($key, $iRequestedPage)
	{
		$this->setCurrentPage($key);
		$this->setPrevPage($key);
		$this->setNextPage($key);
	}

	public function getCurrentPage($key = 0)
	{
		return (isset($this->_aResultData['current_page'])) ? $this->_aResultData['current_page'] : $this->setCurrentPage($key);
	}
	private function setCurrentPage($key)
	{
		$iRequestedPage = ($this->getRequestedPage()) ? : 1;

		return $this->_aResultData['current_page'] = ($iRequestedPage <= ($iLastPage = $this->getTotalPageCount())) ? $iRequestedPage : $iLastPage;
	}

	private function setPrevPage($key)
	{
		$iCurrentPage = $this->getCurrentPage($key);
		return $this->_aResultData['prev_page'] = ($iCurrentPage > 1) ? $iCurrentPage - 1 : false;
	}
	public function getPrevPage($key = 0)
	{
		return (isset($this->_aResultData['prev_page'])) ? $this->_aResultData['prev_page'] : $this->setPrevPage($key);
	}

	private function setNextPage($key)
	{
		$iCurrentPage = $this->getCurrentPage($key);
		return $this->_aResultData['next_page'] = ($iCurrentPage < $this->getTotalPageCount() && $iCurrentPage > 0) ? $iCurrentPage + 1 : false;
	}
	public function getNextPage($key = 0)
	{
		return (isset($this->_aResultData['next_page'])) ? $this->_aResultData['next_page'] : $this->setNextPage($key);
	}

	private function setResultsOnCurrentPageCount($key)
	{
		if(($iFrom = $this->getResultsOnCurrentPageCountFrom($key)) == 0) {
			return 0;
		} else {
			$iCount = $this->getResultsOnCurrentPageCountTo($key) - $iFrom;
			return $this->_aSearchData['visible_results'] = ($iCount == 0) ? 1 : $iCount +1;
		}
	}

	public function getResultsOnCurrentPageCount($key = 1)
	{
		return isset($this->_aSearchData['visible_results']) ? $this->_aSearchData['visible_results'] : $this->setResultsOnCurrentPageCount($key);
	}

	private function setResultsOnCurrentPageCountFrom($key)
	{
		$iCount = ($this->getCurrentPage($key) * $this->getResultsPerPage($key)) - $this->getResultsPerPage($key) + 1;
		return $this->_aSearchData['visible_results_from'] = ($iCount >= 0) ? $iCount : 0;
	}

	public function getResultsOnCurrentPageCountFrom($key = 1)
	{
		return isset($this->_aSearchData['visible_results_from']) ? $this->_aSearchData['visible_results_from'] : $this->setResultsOnCurrentPageCountFrom($key);
	}

	private function setResultsOnCurrentPageCountTo($key)
	{
		$iCount = $this->getCurrentPage($key) * $this->getResultsPerPage($key);
		return $this->_aSearchData['visible_results_to'] = ($iCount <= $this->getTotalResultCount($key)) ? $iCount : $this->getTotalResultCount($key);
	}

	public function getResultsOnCurrentPageCountTo($key = 1)
	{
		return isset($this->_aSearchData['visible_results_to']) ? $this->_aSearchData['visible_results_to'] : $this->setResultsOnCurrentPageCountTo($key);
	}

	############### Sorting methods #####################
	private function make_cmp($objectA, $objectB)
	{
		if(!$sSortBy = $this->getOrderBy())
		{
			return 0;
		}

		$varA = $objectA->$sSortBy;
		$varB = $objectB->$sSortBy;

		if($varB === null) {
			$diff = ($varA === null) ? 0 : -1;
		} else {
			$typeA = gettype($varA);
			switch($typeA)
			{
				case 'boolean': $diff = (($varA === $varB) ? 0 : (($varA === true) ? -1 : 1) ); break;
				case 'integer': case 'float': case 'double': $diff = $varA - $varB; break;
				case 'string': $diff = strcmp(strtolower($varA), strtolower($varB)); break;
				case 'NULL': $diff = 1; break;
				default: throw new exception($type . ' can not be handled by sort function'); break;
			}
		}

		if($diff != 0) {
			if($this->getOrder() == 'ASC' || !$this->getOrder()) {
				$diff = ($diff > 0) ? -$diff : abs($diff);
			}
		}

		return $diff;
	}

	private function LevenshteinDistance($s1, $s2) //source: http://php.net/manual/en/function.levenshtein.php
	{
		/*
		 * This function starts out with several checks in an attempt to save time.
		*   1.  The shorter string is always used as the "right-hand" string (as the size of the array is based on its length).
		*   2.  If the left string is empty, the length of the right is returned.
		*   3.  If the right string is empty, the length of the left is returned.
		*   4.  If the strings are equal, a zero-distance is returned.
		*   5.  If the left string is contained within the right string, the difference in length is returned.
		*   6.  If the right string is contained within the left string, the difference in length is returned.
		* If none of the above conditions were met, the Levenshtein algorithm is used.
		*/
		$sLeft = (strlen($s1) > strlen($s2)) ? $s1 : $s2;
		$sRight = (strlen($s1) > strlen($s2)) ? $s2 : $s1;
		$nLeftLength = strlen($sLeft);
		$nRightLength = strlen($sRight);
		if ($nLeftLength == 0)
			return $nRightLength;
		else if ($nRightLength == 0)
			return $nLeftLength;
		else if ($sLeft === $sRight)
			return 0;
		else if (($nLeftLength < $nRightLength) && (strpos($sRight, $sLeft) !== FALSE))
			return $nRightLength - $nLeftLength;
		else if (($nRightLength < $nLeftLength) && (strpos($sLeft, $sRight) !== FALSE))
			return $nLeftLength - $nRightLength;
		else {
			$nsDistance = range(1, $nRightLength + 1);
			for ($nLeftPos = 1; $nLeftPos <= $nLeftLength; ++$nLeftPos)
			{
				$cLeft = $sLeft[$nLeftPos - 1];
				$nDiagonal = $nLeftPos - 1;
				$nsDistance[0] = $nLeftPos;
				for ($nRightPos = 1; $nRightPos <= $nRightLength; ++$nRightPos)
				{
					$cRight = $sRight[$nRightPos - 1];
					$nCost = ($cRight == $cLeft) ? 0 : 1;
					$nNewDiagonal = $nsDistance[$nRightPos];
					$nsDistance[$nRightPos] =
					min($nsDistance[$nRightPos] + 1,
						$nsDistance[$nRightPos - 1] + 1,
						$nDiagonal + $nCost);
					$nDiagonal = $nNewDiagonal;
				}
			}
			return $nsDistance[$nRightLength];
		}
	}
}