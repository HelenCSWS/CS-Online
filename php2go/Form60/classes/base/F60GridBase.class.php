<?php

/**
 * Perform the necessary imports
 */
//require_once(PHP2GO_ROOT . "modules/html2pdf/html2fpdf.php");
//import('Form60.base.F60LayoutBase');
import('php2go.data.Report');

class F60GridBase extends Report
{
    var $contents;
    var $pageLink;
    var $idName;
    var $fieldValue;
    var $id ="";
    var $_SearchID="";
    var $_SearchAlt="";
    var $_from="";
    var $_to="";
    var $pageid ="";
    var $searchQry="";
    var $is_print = false;
    var $is_start="";
    var $store_type="";
    var $user_id="";
    var $is_OOB ="0";
    var $adt_id="";
    var $estate_id="";

    function F60GridBase($xmlFile, $templateFile, &$Document)
    {
       Report::Report($xmlFile, $templateFile, &$Document);
       Report::setPageSize(25);
    }

	function setPrint($isPrint)
	{
		$this->is_print = $isPrint;
	}

    function setPageid($pageid)
    {
        $this->pageid =$pageid;
    }
    function getRows()
    {
        return PagedDataSet::getTotalRecordCount();
        //return PagedDataSet::getField("wine_id");
    }
    function setSearchPara($searchID,$searchKey)
    {
        $this->_SearchID =$searchID;
        $this->_SearchKey =$searchKey;
    }

	function setAdtId($adtId)
	{
		$this->adt_id = $adtId;
	}
	function setEstateId($estate_id)
	{
		$this->estate_id = $estate_id;
	}
    function setSearchstring($search)
    {
        $this->searchQry =$search;
    }
    function setStartWith($isstart)
    {

        $this->is_start =$isstart;
       //  print $is_start;
    }
   function setStoreType($type)
    {
        $this->store_type =$type;
        
    }
   function setUserid($userid)
    {
        $this->user_id =$userid;
    }
   function setOOB($is_OOB)
    {
        $this->is_OOB =$is_OOB;
    }

    function getid()
    {
      return $this->id;
    }
    
    //!-----------------------------------------------------------------
	// @function 	Report::getContent
	// @desc 		Constrói e retorna o conteúdo do relatório
	// @access 		public
	// @return		string	Conteúdo final do relatório
	//!-----------------------------------------------------------------
	function getContent() {
		if (!$this->_loaded)
			$this->build();
		if (PagedDataSet::getTotalRecordCount() > 0) {
			$this->_buildContent();
			return $this->Template->getContent();
		} else {
			if (!isset($this->emptyTemplate['file'])) {
				//$emptyMessages =PHP2Go::getLangVal('REPORT_EMPTY_VALUES');
				//$emptyMessages ="test";
				$EmptyTpl =& new Template(TEMPLATE_PATH . 'emptyGrid.tpl');
				$EmptyTpl->parse();
				//$EmptyTpl->assign($emptyMessages);
				$EmptyTpl->assign($this->styleSheet);
				//$EmptyTpl->assign('url',"rrr");// HttpRequest::basePath());
			} else {
				$EmptyTpl =& new Template($this->emptyTemplate['file']);
				$EmptyTpl->parse();
				if (!empty($this->emptyTemplate['vars']))
					$EmptyTpl->assign($this->emptyTemplate['vars']);
			}

			return $EmptyTpl->getContent();
		}
	}
	
   function _dataColumns($data=NULL, $aCell=NULL) {
		// verifica se deve aplicar estilo de altern?cia
		if (!empty($this->altStyle)) {
			$altStyle = current($this->altStyle);
			if (TypeUtils::isNull($aCell) || $aCell == $this->numCols) {
				if (!next($this->altStyle))
					reset($this->altStyle);
			}
		}
		// dados nulos, deve gerar uma c?ula vazia
		if (TypeUtils::isNull($data)) {
			$blockName = (isset($this->emptyBlock) ? $this->emptyBlock :
'loop_cell');
			$this->Template->createBlock($blockName);
			$this->Template->assign(DataSet::getFieldName(0), '&nbsp;');
			if (!empty($this->altStyle) &&
$this->Template->isVariableDefined($blockName . '.alt_style')) {
				$this->Template->assign('alt_style', $altStyle);
				$this->Template->assign('loop_line.alt_style', $altStyle);
			}
		} else {
			// executa o tratador de linha
			if (isset($this->lineHandler))
				$data = $this->lineHandler->invoke($data);
			// executa a formata?o da linha
			if (!empty($this->hlFormat))
				$data = $this->_highlightSearch($data);
			// relat?io com cabe?lhos
			if ($this->hasHeader) {
				$varsOk = FALSE;
				// verifica os tamanhos fornecidos e o n?ero de colunas
				$finalSize = (DataSet::getFieldCount() - count($this->groupDisplay)
- count($this->hidden));
				if (isset($this->colSizes) && sizeOf($this->colSizes) !=
$finalSize)

PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH',
array(sizeOf($this->colSizes), $finalSize,
sizeOf($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
				for ($i = 0, $c = 0; $i < DataSet::getFieldCount(); $i++) {
					$key = DataSet::getFieldName($i);
					// verifica se a coluna pertence ? colunas de agrupamento e ?escondidas
					if (!in_array($key, $this->groupDisplay) && !in_array($key,
$this->hidden)) {
						// define o tamanho da coluna, a partir do modo definido naclasse
						$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM &&
isset($this->colSizes) ? $this->colSizes[$c] . '%' :
($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ?
TypeUtils::parseInteger(100 / $finalSize) . '%' : ''));
						// verifica se as vari?eis obrigat?ias foram declaradas
						$this->Template->createBlock('loop_cell');
						if (!$varsOk) {
							if (!$this->Template->isVariableDefined('loop_cell.col_data'))

PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE',
array("col_data", "loop_cell")), E_USER_ERROR, __FILE__, __LINE__);
							else if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE &&
!$this->Template->isVariableDefined('loop_cell.col_wid'))

PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE',
array("col_wid", "loop_cell")), E_USER_ERROR, __FILE__, __LINE__);
							else
								$varsOk = TRUE;
						}
						// verifica se existe tratador para a coluna
						if (isset($this->columnHandler[$key]))
							$data[$key] = $this->columnHandler[$key]->invoke($data[$key]);
						// substitui ID, valor e tamanho na coluna
						$this->Template->assign('col_wid', $colWidth);
						//???????????????????
						//$this->Template->assign('col_data', $data[$key] . '&nbsp;');
						//$this->sLink =$_SERVER["REQUEST_URI"] . '?customer_id=' .$data['customer_id'] . '">'
  	                     $sLink =$this->pageLink . '&'.$this->idName.'=' .$data[$this->idName] . '">';
                       /*  if ($this->is_estate4wine)
                        {
	                   	   $sLink =$this->pageLink . '&'.estate_id.'=' .$data[$this->idName] . '">';
	                   	 }*/
                      
                        
                        
                        $sURL = '<a style="font-size:8pt;color:black;font-family:verdana" href="' . $sLink. $data[$key] . '&nbsp;</a>';
                        if($this->is_print)
	                        $sURL = '<span>' . $data[$key] . '&nbsp;</span>';
                      if (strlen($this->id==0))
                        {
                            $this->id=$data[$this->idName];
                           //print $this->id;
                        }
						$this->Template->assign('col_data', $sURL);
						if (!empty($this->altStyle) &&
							$this->Template->isVariableDefined('loop_cell.alt_style')) {
							$this->Template->assign('alt_style', $altStyle);
							$this->Template->assign('loop_line.alt_style', $altStyle);
						}
						$c++;
					}
				}
			// relat?io com c?ulas
			} else {
				$colWidth = TypeUtils::parseInteger(100 / $this->numCols) . '%';
				$this->Template->createBlock('loop_cell');
				$this->Template->assign('col_wid', $colWidth);
				if (!empty($this->altStyle) &&
$this->Template->isVariableDefined('loop_cell.alt_style'))
					$this->Template->assign('alt_style', $altStyle);

				// Substitui o valor de todas as colunas na c?ula
				for ($i=0; $i<DataSet::getFieldCount(); $i++) {
					$key = DataSet::getFieldName($i);
					// Verifica se existe tratador para a coluna
					if (isset($this->columnHandler[$key]))
						$data[$key] = $this->columnHandler[$key]->invoke($data[$key]);
					if (!isset($this->group) || !in_array($key, $this->groupDisplay))
						$this->Template->assign("$key", $data[$key]);
				}


			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_dataHeader
	// @desc 		Exibe o cabeçalho de dados com os nomes das colunas do relatório
	// @access 		private
	// @return		void
	// @note 		Esta função só é executada no modo em que os cabeçalhos
	// 				são exibidos no topo das páginas ou nos inícios de grupo
	//!-----------------------------------------------------------------
	function _dataHeader() {
		// verifica os tamanhos fornecidos e o número de colunas
		$finalSize = (DataSet::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
		if (isset($this->colSizes) && sizeOf($this->colSizes) != $finalSize)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeOf($this->colSizes), $finalSize, sizeOf($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
		$varsOk = FALSE;
		for ($i = 0, $c = 0; $i < DataSet::getFieldCount(); $i++) {

			$key = DataSet::getFieldName($i);
			$keyAlias = (array_key_exists($key, $this->columnAliases) ? $this->columnAliases[$key] : $key);
			// verifica se a coluna pertence às colunas de agrupamento ou às colunas escondidas
			if (!in_array($key, $this->groupDisplay) && !in_array($key, $this->hidden)) {
				// define o tamanho da coluna
				$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? TypeUtils::parseInteger(100 / $finalSize) . '%' : ''));
				$this->Template->createBlock('loop_header_cell');
				// verifica se as variáveis obrigatórias foram declaradas
				if (!$varsOk) {
					if (!$this->Template->isVariableDefined('loop_header_cell.col_name'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_name', 'loop_header_cell')), E_USER_ERROR, __FILE__, __LINE__);
					else if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE && !$this->Template->isVariableDefined('loop_header_cell.col_wid'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_header_cell')), E_USER_ERROR, __FILE__, __LINE__);
					else $varsOk = TRUE;
				}
				// substitui valor, tamanho e ordenação na coluna
				$this->Template->assign('col_wid', $colWidth);
				if (!$this->isPrintable && $this->_orderLinks === TRUE) {
					$this->Template->assign('col_id', $key);
				//	$this->Template->assign('col_name', HtmlUtils::anchor($this->_generatePageLink('userSearch', $key), $keyAlias, PHP2Go::getLangVal('REPORT_ORDER_TIP', $keyAlias), $this->styleSheet['header'], array(), '', "head$c"));
                  // print $this->_generatePageLink(PagedDataSet::getCurrentPage(), $key);
					$this->Template->assign('col_name', HtmlUtils::anchor($this->_generatePageLink(PagedDataSet::getCurrentPage(), $key), $keyAlias, PHP2Go::getLangVal('REPORT_ORDER_TIP', $keyAlias), $this->styleSheet['header'], array(), '', "head$c"));

					$this->Template->assign('col_order', (urldecode(HttpRequest::get('order')) == $key ? '&nbsp;' . HtmlUtils::image($this->_orderTypeIcon()) : '&nbsp;'));
				} else {
					$this->Template->assign('col_id', $key);
					$this->Template->assign("col_name", "<SPAN CLASS='{$this->styleSheet['header']}'>{$keyAlias}</SPAN>");
				}
				$c++;
			}
		}
	}





	function _generatePageLink($page, $order = '') {
		if (isset($this->_order) && $order == $this->_order)
			$ot = ($this->_orderType == 'a' ? 'd' : 'a');
		else
			$ot = $this->_orderType;

//print $this->adt_id;
       if ($this->pageid !="")
        $path =HttpRequest::basePath()."?page_name=F60SearchResult&search_id=".$this->_SearchID."&search_key=".$this->_SearchKey."&pageid=".$this->pageid."&is_start=".$this->is_start."&store_type=".$this->store_type."&user_id=".$this->user_id."&is_OOB=".$this->is_OOB."&adt_field=".$this->adt_id."&estate_id=".$this->estate_id;
      
        
        else
          $path =HttpRequest::basePath()."?page_name=F60SearchResult&search_id=".$this->_SearchID."&search_key=".$this->_SearchKey."&is_start=".$this->is_start."&store_type=".$this->store_type."&user_id=".$this->user_id."&is_OOB=".$this->is_OOB."&adt_field=".$this->adt_id."&estate_id=".$this->estate_id;
      
		//	print $path;
		return sprintf("%s&page=%s%s%s%s%s",
					$path, $page,
					($order != '' ? '&order=' . urlencode($order) : (isset($this->_order) ? '&order=' . $this->_order : '')),
					'&ordertype=' . $ot,
					($this->_SimpleSearch->searchSent ? $this->_SimpleSearch->getUrlString() : ''),
					(isset($this->extraVars) ? '&' . $this->extraVars : '')
		);
	}
	
	
}

?>
