<?php

	// $Header: /www/cvsroot/php2go/examples/feedreader.example.php,v 1.2 2005/06/01 15:56:03 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2005/06/01 15:56:03 $
	// vim: set expandtab tabstop=4 shiftwidth=4:
	
	require_once('../p2gConfig.php');
	import('php2go.xml.feed.FeedReader');
	import('php2go.data.PagedDataSet');
	import('php2go.net.HttpRequest');
	import('php2go.util.HtmlUtils');
	
	println('<B>PHP2Go Examples</B> : php2go.xml.feed.FeedReader');
	println('<B>Also using</B> : php2go.data.DataSetArray, php2go.data.PagedDataSet<BR>');
	
	/**
	 * create an instance of the FeedReader class
	 */
	$fr =& new FeedReader();
	/**
	 * define base directory and lifetime of the cache engine
	 */
	$fr->setCacheProperties(PHP2GO_CACHE_PATH, 60*30);
	/** 
	 * fetch the feed from the remote URL
	 * cache will be used if exists and not stale
	 */
	$feed = $fr->fetch('http://freshmeat.net/backend/fm-releases-global.xml');
	/**
	 * test the value to prevent errors
	 */
	if ($feed) {
		println(sprintf("<B>%s</B><BR>Last Modified: %s<BR>Etag: %s<BR>", $feed->Channel->title, $feed->getLastModified('r'), $feed->etag));
		/**
		 * the return value of the fetch method is an instance of php2go.xml.feed.Feed;
		 * this object has a property called Channel (php2go.xml.feed.FeedChannel);
		 * inside this channel are stored the set of items (php2go.xml.feed.FeedItem);
		 * the properties of the item differ from one specification to another (different
		 * version of RSS or ATOM pattern
		 */
		$iterator = $feed->Channel->itemIterator();
		while ($iterator->hasNext()) {
			$item = $iterator->next();
			println(sprintf("<B>%s</B> - [%s]", $item->title, HtmlUtils::anchor($item->link, $item->link)));
		}
		println('<HR><A NAME=\'dataset\'></A>');
		/**
		 * the second example shows how to fill a dataset using the feed items;
		 * in this special case, we're using a PagedDataSet to browse the items
		 */
		println('<B>Example using a PagedDataSet to browse the items</B>');
		$DataSet =& PagedDataSet::getInstance('array');
		$DataSet->setPageSize(10);
		$DataSet->load($feed->Channel->getChildren());
		if ($DataSet->getRecordCount() > 0) {
			println(sprintf("Current page: %d", $DataSet->getCurrentPage()));
			println(sprintf("%s%s",
				($DataSet->atFirstPage() ? '' : HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $DataSet->getPreviousPage() . '#dataset', '<B>[ < Previous ]</B>') . "&nbsp"),
				($DataSet->atLastPage() ? '' : HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $DataSet->getNextPage() . '#dataset', '<B>[ Next > ]</B>'))
			));
			while (!$DataSet->eof()) {
				println(sprintf("<A HREF=\"%s\" TARGET=\"_blank\">%s</A>", $DataSet->getField('link'), $DataSet->getField('title')));
				$DataSet->moveNext();
			}
		} else {
			pritln('Empty result set');
		}
	}

?>