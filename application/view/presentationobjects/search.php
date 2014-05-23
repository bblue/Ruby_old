<?php
namespace View\PresentationObjects;

use View\AbstractPresentationObject;
use Model\Services\Search as SearchService;

final class Search extends AbstractPresentationObject
{
	public function assignData(SearchService $search)
	{
		$this->assign_vars(array(
			'SEARCH_COUNT_RES_TOTAL'		=> $search->getTotalResultCount(),
			'SEARCH_COUNT_RES_CURRENT'		=> $search->getResultsOnCurrentPageCount(),
			'SEARCH_COUNT_RES_CURRENT_FROM'	=> $search->getResultsOnCurrentPageCountFrom(),
			'SEARCH_COUNT_RES_CURRENT_TO'	=> $search->getResultsOnCurrentPageCountTo(),
			'SEARCH_COUNT_PAGES_TOTAL'		=> $search->getTotalPageCount(),
			'SEARCH_PAGES_CURRENT_ID'		=> $search->getCurrentPage(),
			'SEARCH_PAGES_NEXT_ID'			=> $search->getNextPage(),
			'SEARCH_PAGES_PREV_ID'			=> $search->getPrevPage(),
			'SEARCH_QUERY_STRING'			=> $search->getFulltextSearch(),
			'SEARCH_TIME'					=> round($search->getSearchTime(), 5)
		));
	}
}