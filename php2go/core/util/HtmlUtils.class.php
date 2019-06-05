<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2005 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/util/HtmlUtils.class.php,v 1.26 2005/08/09 17:51:22 mpont Exp $
// $Date: 2005/08/09 17:51:22 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HtmlUtils
// @desc		Classe que cont�m um conjunto de fun��es utilit�rias
//				para a constru��o de tags ou por��es de c�digo HTML,
//				bem como para a execu��o de algumas a��es utilizando 
//				JavaScript
// @package		php2go.util
// @extends		PHP2Go
// @uses		HttpRequest
// @author		Marcos Pont
// @version		$Revision: 1.26 $
//!-----------------------------------------------------------------
class HtmlUtils extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::&getInstance
	// @desc		Retorna uma inst�ncia �nica da classe
	// @access		public
	// @return		HtmlUtils object	Inst�ncia da classe HtmlUtils
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance =& new HtmlUtils;
		}
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::anchor
	// @desc		Monta o c�digo de uma �ncora HTML 'A', a partir
	// 				dos par�metros b�sicos
	// @access		public
	// @param		url string			URL ou fun��o JavaScript para o par�metro HREF do �ncora
	// @param		text string			Texto interno ao �ncora
	// @param		stBarText string	"" Texto para a barra de status no evento onMouseOver
	// @param		css string			"" Estilo CSS para o texto interno ao �ncora
	// @param		extraScript array	"array()" Vetor associativo evento=>a��o para tratamento de eventos JavaScript
	// @param		target string		"" Alvo para a �ncora
	// @param		name string			"" Nome para a �ncora
	// @param		id string			"" Identifica��o de objeto para a �ncora
	// @param		rel string			"" Rela��o do documento atual com o documento indicado no par�metro 'url'
	// @return		string C�digo formatado para a �ncora
	// @static	
	//!-----------------------------------------------------------------
	function anchor($url, $text, $statusBarText='', $cssClass='', $jsEvents=array(), $target='', $name='', $id='', $rel='') {
		if (empty($url)) 
			$url = "javascript:void(0);";
		$scriptStr = '';
		if (!empty($jsEvents) && $statusBarText != "") {
			$jsEvents['onMouseOver'] = (isset($jsEvents['onMouseOver']) ? $jsEvents['onMouseOver'] . "window.status='$statusBarText';return true;" : "window.status='$statusBarText';return true;");
			$jsEvents['onMouseOut'] = (isset($jsEvents['onMouseOut']) ? $jsEvents['onMouseOut'] . "window.status='';return true;" : "window.status='';return true;");
		} else if ($statusBarText) {
			$scriptStr .= "onMouseOver=\"window.status='$statusBarText';return true;\" onMouseOut=\"window.status='';return true;\"";
		}
		foreach ($jsEvents as $event => $action)
			$scriptStr .= " $event=\"" . ereg_replace("\"", "'", $action) . "\"";
		return sprintf("<A HREF=\"%s\"%s%s%s%s%s%s%s>%s</A>", htmlentities($url),
			(!empty($name) ? " NAME=\"{$name}\"" : ""),
			(!empty($id) ? " ID=\"{$id}\"" : ""),
			(!empty($rel) ? " REL=\"{$rel}\"" : ""),
			(!empty($target) ? " TARGET=\"{$target}\"" : ""),
			(!empty($cssClass) ? " CLASS=\"{$cssClass}\"" : ""),
			(!empty($statusBarText) ? " TITLE=\"{$statusBarText}\"" : ""),
			(!empty($scriptStr) ? " {$scriptStr}" : ""), 
			$text);
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::mailtoAnchor
	// @desc		Constr�i uma �ncora do tipo mailto:, com a possibilidade
	//				de ofuscar o c�digo gerado para proteger o endere�o de e-mail
	// @access		public
	// @param		email string			Endere�o de e-mail
	// @param		text string				"" Texto para o �ncora. Se n�o for fornecido, o texto do �ncora ser� o endere�o de e-mail
	// @param		statusBarText string	"" Texto para a barra de status
	// @param		cssClass string			"" Estilo CSS para o �ncora
	// @param		id string				"" ID do �ncora
	// @param		obfuscate bool			"TRUE" Ofuscar o c�digo do �ncora
	// @return		string C�digo HTML gerado
	// @static	
	//!-----------------------------------------------------------------
	function mailtoAnchor($email, $text='', $statusBarText='', $cssClass='', $id='', $obfuscate=TRUE) {
		$scriptStr = (!empty($statusBarText) ? HtmlUtils::statusBar($statusBarText, TRUE) : '');
		$anchor = sprintf("<A HREF=\"mailto:%s\"%s%s%s>%s</A>", $email,
			(!empty($id) ? " ID=\"{$id}\"" : ""),
			(!empty($cssClass) ? " CLASS=\"{$cssClass}\"" : ""),
			$scriptStr, 
			(empty($text) ? $email : $text)
		);
		if ($obfuscate) {
			$s = chunk_split(bin2hex($anchor), 2, '%');
			$s = '%' . substr($s, 0, strlen($s)-1);
			$s = chunk_split($s, 54, "'+'");
			$s = substr($s, 0, strlen($s)-3);
			$result = "<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript\">document.write(unescape('$s'));</SCRIPT>";
			return $result;			
		} else {
			return $anchor;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::image
	// @desc		Constr�i uma tag IMG para uma imagem
	// @access		public
	// @param		src string		Caminho completo para a imagem
	// @param		alt string		"" Texto alt para a imagem
	// @param		wid int			"0" Largura da imagem
	// @param		hei int			"0" Altura da imagem
	// @param		hspace int		"-1" Espa�amento horizontal da imagem
	// @param		vspace int		"-1" Espa�amento vertical da imagem
	// @param		align string	"" Alinhamento da imagem
	// @param		id string		"" ID para o objeto criado
	// @param		swpImage string	"" Caminho completo para a imagem de swap a ser utilizada
	// @param		cssClass string	"" Estilo CSS para a imagem
	// @return		string C�digo da tag IMG da imagem
	// @static	
	//!-----------------------------------------------------------------
	function image($src, $alt='', $wid=0, $hei=0, $hspace=-1, $vspace=-1, $align='', $id='', $swpImage='', $cssClass='') {
		// ID padr�o
		if (empty($id))
			$id = PHP2Go::generateUniqueId('htmlimage');
		return sprintf ("<IMG ID=\"%s\" SRC=\"%s\" ALT=\"%s\" BORDER=\"0\"%s%s%s%s%s%s%s>",
			$id, htmlentities($src), $alt,
			($wid > 0 ? " WIDTH=\"{$wid}\"" : ""),
			($hei > 0 ? " HEIGHT=\"{$hei}\"" : ""),
			($hspace >= 0 ? " HSPACE=\"{$hspace}\"" : ""),
			($vspace >= 0 ? " VSPACE=\"{$vspace}\"" : ""),
			(!empty($align) ? " ALIGN=\"{$align}\"" : ""),
			(!empty($cssClass) ? " CLASS=\"{$cssClass}\"" : ""),
			(!empty($swpImage) ? " onMouseOver=\"this.src='$swpImage'\" onMouseOut=\"this.src='$src'\"" : "")
		);	
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::button
	// @desc		Constr�i uma tag INPUT para um bot�o
	// @access		public
	// @param		type string		"SUBMIT" Tipo do bot�o: button, submit ou reset
	// @param		name string		"" Nome do INPUT do bot�o
	// @param		value string	"" Valor a ser exibido no bot�o
	// @param		script string	"" Eventos JavaScript a serem tratados
	// @param		alt string		"" Texto 'alt' para o bot�o
	// @param		css string		"" Estilo CSS para o bot�o. Ser� ignorado se o browser n�o suportar CSS em INPUTs
	// @return		string C�digo da tag INPUT para o bot�o
	// @static	
	//!-----------------------------------------------------------------
	function button($type='SUBMIT', $name='', $value='', $script='', $alt='', $css='') {
		$type = strtolower($type);
		if ($type != 'button' && $type != 'submit' && $type != 'reset')
			$type = 'button';
		// nome padr�o
		if (empty($name))
			$name = PHP2Go::generateUniqueId('htmlbutton');
		// valor padr�o
		$Lang =& LanguageBase::getInstance();
		$defaultValue = $Lang->getLanguageValue('DEFAULT_BTN_VALUE');
		// compatibilidade do browser para uso de CSS
		if (!empty($css)) {
			$Agent =& UserAgent::getInstance();
			if (!$Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
				$css = '';
		}			
		return sprintf ("<INPUT TYPE=\"%s\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\"%s%s%s>",
			$type, $name, $name,
			(!empty($value) ? $value : $defaultValue),
			(!empty($script) ? " {$script}" : ""),
			(!empty($alt) ? " ALT=\"{$alt}\"" : ""),
			(!empty($css) ? " CLASS=\"{$css}\"" : ""));
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::window
	// @desc		Constr�i a chamada para a fun��o JavaScript createWindow, que cria uma nova janela
	// @access		public
	// @param		url string			URL da nova janela a ser aberta
	// @param		windowType int		Tipo da janela. Para maiores informa��es, consulte a documenta��o do arquivo window.js
	// @param		windowWidth int		"640" Largura da janela
	// @param		windowHeight int	"480" Altura da janela
	// @param		windowX int			"0" Coordenada X da janela
	// @param		windowY int			"0" Coordenada Y da janela
	// @param		windowTitle string	"" T�tulo da janela
	// @param		windowReturn bool	"FALSE" Indica se a fun��o de cria��o da janela deve retornar o objeto Window
	// @return		string Chamada da fun��o
	// @note		Esta fun��o � �til em conjunto com HtmlUtils::anchor, na constru��o de links para abertura de popups
	// @static
	//!-----------------------------------------------------------------
	function window($url, $windowType, $windowWidth=640, $windowHeight=480, $windowX=0, $windowY=0, $windowTitle='', $windowReturn=FALSE) {
		if ($windowTitle == '')
			$windowTitle = PHP2Go::generateUniqueId('window');
		$ret = (TypeUtils::isTrue($windowReturn) ? 'true' : 'false');
		return "createWindow('$url', $windowWidth, $windowHeight, $windowX, $windowY, '$windowTitle', $windowType, null, $ret)";
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::scrollableArea
	// @desc		Constr�i uma DIV com rolagem horizontal e/ou vertical se
	//				o conte�do exceder o tamanho definido
	// @access		public
	// @param		content string		Conte�do
	// @param		width int			Largura do container
	// @param		height int			Altura do container
	// @param		overflow string		"auto" Valor para o atributo overflow da defini��o de estilos do container
	// @param		cssClass string		"" Estilo CSS para o container
	// @param		id string			"" ID para o container
	// @return		string C�digo HTML resultante
	// @static
	//!-----------------------------------------------------------------
	function scrollableArea($content, $width, $height, $overflow='auto', $cssClass='', $id='') {
		if (empty($id))
			$id = PHP2Go::generateUniqueId('scrollarea');
		$style = "width:{$width}px;height:{$height}px;overflow:{$overflow}";
		$cssClass = (!empty($cssClass) ? " CLASS=\"{$cssClass}\"" : '');
		return "<DIV ID=\"{$id}\" STYLE=\"{$style}\"{$cssClass}>{$content}</DIV>";
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::itemList
	// @desc		Constr�i uma lista de itens em HTML utilizando as tags OL ou UL
	// @param		values array		Array de valores
	// @param		ordered bool		"FALSE" Lista ordenada ou n�o ordenada
	// @param		listAttr string		"" String de atributos para a lista
	// @param		itemAttr string		"" String de atributos para cada um dos itens 
	// @access		public
	// @return		string C�digo HTML da lista
	// @static	
	//!-----------------------------------------------------------------
	function itemList($values, $ordered=FALSE, $listAttr='', $itemAttr='') {
		$array = (array)$values;
		if (empty($array))
			return '';		
		$tag = ($ordered ? 'OL' : 'UL');
		if (!empty($listAttr))
			$listAttr = ' ' . ltrim($listAttr);
		if (!empty($itemAttr))
			$itemAttr = '  ' . ltrim($itemAttr);
		$buf = "<{$tag}{$listAttr}>";
		foreach ($array as $entry) {
			if (is_array($entry))
				$buf .= HtmlUtils::itemList($entry, $ordered, $listAttr, $itemAttr);
			else
				$buf .= "<LI{$itemAttr}>{$entry}</LI>";
		}
		$buf .= "</{$tag}>";
		return $buf;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::definitionList
	// @desc		Monta, a partir de um array associativo, uma lista de termos e
	//				defini��es, onde as chaves s�o os termos e os valores as defini��es,
	//				utilizando as tags DL, DT e DD
	// @param		values array		Array de valores
	// @param		listAttr string		"" String de atributos para a lista
	// @param		termAttr string		"" String de atributos para os termos
	// @param		defAttr string		"" String de atributos para as defini��es
	// @access		public
	// @return		string C�digo HTML da lista
	// @static	
	//!-----------------------------------------------------------------
	function definitionList($values, $listAttr='', $termAttr='', $defAttr='') {
		$array = (array)$values;
		if (empty($array))
			return '';		
		if (!empty($listAttr))
			$listAttr = ' ' . ltrim($listAttr);
		if (!empty($termAttr))
			$termAttr = ' ' . ltrim($termAttr);
		if (!empty($defAttr))
			$defAttr = ' ' . ltrim($defAttr);
		$buf = "<DL{$listAttr}>";
		foreach ($array as $key => $value) {
			$buf .= "<DT{$termAttr}>{$key}";
			if (is_array($value))
				$buf .= HtmlUtils::definitionList($value, $listAttr, $termAttr, $defAttr);
			else
				$buf .= "<DD{$defAttr}>{$value}";
		}
		$buf .= "</DL>";
		return $buf;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::table
	// @desc		M�todo utilit�rio de constru��o de uma tabela a partir de um array bidimensional
	// @param		table array					Tabela - array bidimensional
	// @param		headers bool				"TRUE" Exibir cabe�alhos a partir das chaves da primeira entrada do array
	// @param		tableAttr string			"" Atributos da tabela
	// @param		cellAttr string				"" Atributos de c�lula
	// @param		alternateCellAttr string	"" Atributos para c�lula �mpar
	// @param		headerAttr string			"" Atributos para os cabe�alhos	
	// @note		Se o par�metro $alternateCellAttr for fornecido, ser� utilizada altern�ncia de atributos a cada linha, para todas as c�lulas
	// @access		public
	// @return		string C�digo HTML da tabela
	// @static	
	//!-----------------------------------------------------------------
	function table($table, $headers=TRUE, $tableAttr='', $cellAttr='', $alternateCellAttr='', $headerAttr='') {
		$table = (array)$table;
		if (empty($table))
			return '';
		if (!empty($tableAttr))
			$tableAttr = ' ' . ltrim($tableAttr);
		if (!empty($headerAttr))
			$headerAttr = ' ' . ltrim($headerAttr);
		if (!empty($cellAttr))
			$cellAttr = ' ' . ltrim($cellAttr);
		if (!empty($alternateCellAttr))
			$alternateCellAttr = ' ' . ltrim($alternateCellAttr);
		$buf = "<TABLE{$tableAttr}>\n";
		// inclui cabe�alhos, se solicitado
		if ($headers) {			
			list(, $row) = each($table);
			$row = array_keys((array)$row);
			$buf .= "<TR>";
			foreach ($row as $cell)
				$buf .= "<TH{$headerAttr}>{$cell}</TD>";
			$buf .= "</TR>";
		}
		// itera��o nas linhas da tabela
		$count = 1;
		foreach ($table as $entry) {
			$attr = (!empty($alternateCellAttr) && ($count%2) == 0 ? $alternateCellAttr : $cellAttr);
			$buf .= "<TR>\n";
			if (!TypeUtils::isArray($entry)) {
				$buf .= "<TD{$attr}>{$entry}</TD>";
			} else {
				foreach ($entry as $cellValue)
					$buf .= "<TD{$attr}>{$cellValue}</TD>";				
			}
			$buf .= "</TR>\n";
			$count++;
		}
		$buf .= "</TABLE>";
		return $buf;
	}
	
	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::noBreakSpace
	// @desc 		Imprime uma seq��ncia de espa�os em branco (&nbsp;)
	// @access		public
	// @param 		n int			"1" N�mero de espa�os em branco a exibir
	// @return 		string Seq��ncia de caracteres '&nbsp;'
	// @static	
	//!-----------------------------------------------------------------
	function noBreakSpace($n=1) {
		return str_repeat('&nbsp;', $n);
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::tagRepeat
	// @desc		Imprime uma mesma tag $n vezes
	// @access		public
	// @param		tag string		Nome da tag
	// @param		content string	Conte�do para a tag
	// @param		n int			"1" N�mero de repeti��es
	// @return		string C�digo HTML gerado
	// @note		Este m�todo � �til para tags como BIG, SMALL, BR, BLOCKQUOTE, etc...
	// @static	
	//!-----------------------------------------------------------------
	function tagRepeat($tag, $content, $n=1) {
		$n = max(1, $n);
		$tag = strtoupper($tag);
		return str_repeat("<{$tag}>", $n) . $content . str_repeat("</{$tag}>", $n);
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::newLineToBr
	// @desc		Transforma quebras de linha em tags <BR> HTML, otimizando
	//				a funcionalidade j� oferecida pela fun��o nl2br()
	// @access		public
	// @param		str string		String original
	// @return		string String com as quebras de linha transformadas
	// @static	
	//!-----------------------------------------------------------------
	function newLineToBr($str) {
		return str_replace("\n", "<br />\n", $str);
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::parseLinks
	// @desc		Parseia por��es clic�veis (links) dentro de uma string,
	//				gerando c�digo HTML para os �ncoras encontrados
	// @access		public
	// @param		str string		String original
	// @return		string String com os links encontrados transformados em �ncoras
	// @static	
	//!-----------------------------------------------------------------
	function parseLinks($str) {
		$instance = HtmlUtils::getInstance();
        $str = preg_replace_callback('=  (http://|https://|ftp://|mailto:|news:)(\S+)(\*\s|\=\s|&quot;|&lt;|&gt;|<|>|\(|\)|\s|$)=Usmix', array(&$instance, 'buildAnchor'), $str);
        return $str;		
	}
	

    //!-----------------------------------------------------------------
	// @function	HtmlUtils::buildAnchor
	// @desc		Constr�i um �ncora para uma das express�es interpretadas
	//				em HtmlUtils::parseLinks
	// @access		public
	// @param		aMatches array	Uma das ocorr�ncias retornadas no m�todo HtmlUtils::parseLinks
	// @return		string �ncora correspondente
	// @static	
	//!-----------------------------------------------------------------
	function buildAnchor($aMatches) {
        $sHref = $aMatches[1] . $aMatches[2];
		return $this->anchor($sHref, $sHref, $sHref, '', array(), '_blank') . $aMatches[3];
    }
	
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::flashMovie
	// @desc		Monta o c�digo de exibi��o de um movie SWF, a
	// 				partir dos par�metros b�sicos
	// @access		public
	// @param		src string		URL do filme SWF
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para o movie
	// @param		arrPars array	"array()" Array associativo de par�metros
	// @return		string C�digo da tag EMBED/OBJECT do movie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @static	
	//!-----------------------------------------------------------------
	function flashMovie($src, $wid = 0, $hei = 0, $arrPars = array()) {
		$src = htmlentities($src);
		$srcP = $src;
		if (TypeUtils::isArray($arrPars) && !empty($arrPars)) {
			$srcP .= "?";
			foreach($arrPars as $key => $value)
				$srcP .= $key . "=" . $value . "&";
			$srcP = substr($srcP, 0, -1);
		}
		$srcP = htmlentities($srcP);
		return sprintf ("<!-- IE -->
						  <OBJECT CLASSID=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" CODEBASE=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"%s%s ALIGN=\"top\">
						  <PARAM NAME=movie VALUE=\"%s\">
						  <PARAM NAME=\"QUALITY\" VALUE=\"high\">
						  <!-- NN -->
						  <EMBED SRC=\"%s\" QUALITY=\"high\" PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" TYPE=\"application/x-shockwave-flash\"%s%s ALIGN=\"top\" SCALE=\"exactfit\">
						  </EMBED>
						  </OBJECT>",
			($wid > 0) ? " WIDTH=\"" . $wid . "\"" : "",
			($hei > 0) ? " HEIGHT=\"" . $hei . "\"" : "",
			$srcP, $src,
			($wid > 0) ? " WIDTH=\"" . $wid . "\"" : "",
			($hei > 0) ? " HEIGHT=\"" . $hei . "\"" : "");
	}	
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::realPlayerMovie
	// @desc		Monta o c�digo HTML para exibi��o de um filme
	// 				nos formatos do Real Player
	// @access		public
	// @param		src string		URL do movie
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para a janela de exibi��o
	// @param		flags array	Vetor associativo de par�metros
	// @return		string C�digo da tag EMBED do movie	
	// @note		Os par�metros aceitos s�o CLIP_INFO, CLIP_STATUS,
	// 				CONTROLS, AUTO_START e LOOP. S�o considerados verdadeiros
	// 				se fornecidos com valor != 0
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @static	
	//!-----------------------------------------------------------------
	function realPlayerMovie($src, $wid = 0, $hei = 0, $flags) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeOf($srcVals)-1];
		if ($extension == 'ram' || $extension == 'ra' || $extension == 'rm' || $extension == 'rpm' || $extension == 'smil') {
			$src = htmlentities($src);
			$movieCode = sprintf("<EMBED NAME=\"realVideo\" SRC=\"%s\" TYPE=\"audio/x-pn-realaudio\" PLUGINSPAGE=\"http://www.real.com/player\"
										%s%sHSPACE=\"0\" VSPACE=\"0\" BORDER=\"0\" NOJAVA=\"True\" CONTROLS=\"ImageWindow\" CONSOLE=\"_master\" AUTOSTART=\"%d\" LOOP=\"%s\">",
				$src, ($wid > 0 ? " WIDTH=\"" . $wid . "\"" : ""), ($hei > 0 ? " HEIGHT=\"" . $hei . "\"" : ""), ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			if ($flags['CONTROLS']) {
				$movieCode .= sprintf("<BR><EMBED NAME=\"realVideo\" SRC=\"%s\" TYPE=\"audio/x-pn-realaudio\" PLUGINSPAGE=\"http://www.real.com/player\"
											  %s%sHSPACE=\"0\" VSPACE=\"0\" BORDER=\"0\" NOJAVA=\"True\" CONTROLS=\"ControlPanel\" CONSOLE=\"rVideo\" AUTOSTART=\"%d\" LOOP=\"%s\">",
					$src, ($wid > 0 ? " WIDTH=\"" . $wid . "\"" : ""), " HEIGHT=\"35\"", ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			}
			if ($flags['CLIP_STATUS']) {
				$movieCode .= sprintf("<BR><EMBED NAME=\"realVideo\" SRC=\"%s\" TYPE=\"audio/x-pn-realaudio\" PLUGINSPAGE=\"http://www.real.com/player\"
											  %s%sHSPACE=\"0\" VSPACE=\"0\" BORDER=\"0\" NOJAVA=\"True\" CONTROLS=\"StatusBar\" CONSOLE=\"rVideo\">",
					$src, ($wid > 0 ? " WIDTH=\"" . $wid . "\"" : ""), " HEIGHT=\"30\"");
			}
			if ($flags['CLIP_INFO']) {
				$movieCode .= sprintf("<BR><EMBED NAME=\"realVideo\" SRC=\"%s\" TYPE=\"audio/x-pn-realaudio\" PLUGINSPAGE=\"http://www.real.com/player\"
											  %s%sHSPACE=\"0\" VSPACE=\"0\" BORDER=\"0\" NOJAVA=\"True\" CONTROLS=\"TACCtrl\" CONSOLE=\"rVideo\">",
					$src, ($wid > 0 ? " WIDTH=\"" . $wid . "\"" : ""), " HEIGHT=\"32\"");
			}
			return $movieCode;
		} else
			return "";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::mediaPlayerMovie
	// @desc		Monta o c�digo HTML para exibi��o de um filme
	// 				nos formatos do Windows Media Player
	// @access		public
	// @param		src string	URL do movie
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para a janela de exibi��o
	// @param		flags array		"array()" Vetor de par�metros
	// @return		string C�digo da tag EMBED/OBJECT do movie	
	// @note		Os par�metros aceitos s�o CLIP_INFO, CLIP_STATUS,
	// 				CONTROLS, AUTO_START e AUTO_SIZE. S�o considerados
	// 				verdadeiros se fornecidos com valor != 0
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @static	
	//!-----------------------------------------------------------------
	function mediaPlayerMovie($src, $wid = 0, $hei = 0, $flags = array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeOf($srcVals)-1];
		if ($extension == 'asf' || $extension == 'asx' || $extension == 'wmv' || $extension == 'wma') {
			$src = htmlentities($src);
			return sprintf ("<!-- IE -->
							   <OBJECT ID=\"MPlay1\" CLASSID=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" CODEBASE=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715\" STANDBY=\"Loading Microsoft� Windows� Media Player components...\" TYPE=\"application/x-oleobject\"%s%s>
							   <PARAM NAME=\"FileName\" VALUE=\"%s\">
							   <PARAM NAME=\"ShowDisplay\" VALUE=\"%s\">
							   <PARAM NAME=\"ShowStatusBar\" VALUE=\"%s\">
							   <PARAM NAME=\"StatusBar\" VALUE=\"True\">
							   <PARAM NAME=\"AnimationAtStart\" VALUE=\"True\">
							   <PARAM NAME=\"ShowAudioControls\" VALUE=\"%s\">
							   <PARAM NAME=\"ShowPositionControls\" VALUE=\"%s\">
							   <PARAM NAME=\"ShowControls\" VALUE=\"%s\">
							   <PARAM NAME=\"AutoSize\" VALUE=\"%s\">
							   <PARAM NAME=\"AutoStart\" VALUE=\"%d\">
							   <PARAM NAME=\"AutoRewind\" VALUE=\"TRUE\">
							   <!-- NN -->
							   <EMBED%s%s FILENAME=\"%s\" SRC=\"%s\" PLUGINSPAGE=\"http://www.microsoft.com/Windows/MediaPlayer/\" NAME=\"MPlay1\" TYPE=\"video/x-mplayer2\" SHOWDISPLAY=\"%s\" SHOWSTATUSBAR=\"%s\" STATUSBAR=\"True\" AUTOREWIND=\"1\" ANIMATIONATSTART=\"True\" SHOWAUDIOCONTROLS=\"%s\" SHOWPOSITIONCONTROLS=\"%s\" SHOWCONTROLS=\"%s\" AUTOSIZE=\"%s\" AUTOSTART=\"%d\">
							   </EMBED>
							   </OBJECT>",
				($wid > 0) ? " WIDTH=\"" . $wid . "\"" : "",
				($hei > 0) ? " HEIGHT=\"" . $hei . "\"" : "", $src,
				($flags['CLIP_INFO'] ? "TRUE" : "FALSE"),
				($flags['CLIP_STATUS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "TRUE" : "FALSE"),
				($flags['AUTO_START'] ? 1 : 0),
				($wid > 0) ? " WIDTH='" . $wid . "'" : "",
				($hei > 0) ? " HEIGHT='" . $hei . "'" : "", $src, $src,
				($flags['CLIP_INFO'] ? "1" : "0"),
				($flags['CLIP_STATUS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['AUTO_SIZE'] ? "1" : "0"),
				($flags['AUTO_START'] ? 1 : 0)
				);
		} else
			return '';
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::quickTimeMovie
	// @desc		Monta o c�digo HTML para a exibi��o de um movie
	// 				atrav�s do plug-in Quick Time
	// @access		public
	// @param 		src string	URL ou caminho no servidor do movie
	// @param 		wid int			"0" Largura para o movie
	// @param 		hei int			"0" Altura para o movie
	// @param 		flags array		"array()" Atributos de configura��o da exibi��o do movie
	// @return		string C�digo da tag EMBED do movie
	// @note 		Os atributos aceitos s�o AUTO_START, CACHE, CONTROLS, LOOP e AUTO_SIZE
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @static	
	//!-----------------------------------------------------------------
	function quickTimeMovie($src, $wid = 0, $hei = 0, $flags = array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeOf($srcVals)-1];
		if ($extension == 'mov' || $extension == 'qt') {
			$src = htmlentities($src);
			return sprintf("<EMBED NAME=\"Quick Time Video\" SRC=\"%s\" TYPE=\"video/quicktime\" PLUGINSPAGE=\"http://www.apple.com/quicktime/download/indext.html\"
							   %s%s AUTOSTART=\"%s\" KIOSKMODE=\"TRUE\" CACHE=\"%s\" CONTROLLER=\"%s\" LOOP=\"%s\" MOVIENAME=\"quickTime\" SCALE=\"%s\">
								</EMBED>", $src,
				($wid > 0 ? " WIDTH=\"" . $wid . "\"" : ""),
				($hei > 0 ? " HEIGHT=\"" . $hei . "\"" : ""),
				($flags['AUTO_START'] ? "TRUE" : "FALSE"),
				($flags['CACHE'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['LOOP'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "1" : "TOFIT")
				);
		} else {
			return "";
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::statusBar
	// @desc 		Imprime um texto na barra de status no evento
	// 				onMouseOver de um objeto do documento
	// @access		public
	// @param 		str string			Texto para a barra de status e o hint
	// @param 		return bool			"TRUE" TRUE para retornar a string, FALSE para imprimi-la
	// @return		mixed	Retorna a string montada se $return == TRUE. Do contr�rio, retorna TRUE
	// @static	
	//!-----------------------------------------------------------------
	function statusBar($str, $return = TRUE) {
		$mText = "TITLE=\"$str\" onMouseOver=\"window.status='$str';return true;\" onMouseOut=\"window.status='';return true;\"";
		if ($return) {
			return $mText;
		} else {
			print $mText;
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::overPopup
	// @desc 		Gera uma 'popup' de texto associada ao evento
	// 				onMouseOver do elemento atual
	// @access		public
	// @param  		&_Document Document object	Objeto Document onde a popup ser� gerada
	// @param 		caption string		Texto para a popup
	// @param 		argumentList string	"" Lista de argumentos para a gera��o da popup
	// @return		string String da chamada dos eventos onMouseOver e onMouseOut
	// @note 		Esta fun��o utiliza a biblioteca overLIB, desenvolvida por Erik Bosrup.
	// 				Para maiores informa��es sobre como construir o par�metro $argumentList,
	// 				consulte a documenta��o do projeto em http://www.bosrup.com/web/overlib
	// @static	
	//!-----------------------------------------------------------------
	function overPopup(&$_Document, $caption, $argumentList = '') {
		static $divInserted;
		if (!isset($divInserted)) {
			$_Document->appendBodyContent("<DIV ID=\"overDiv\" STYLE=\"position:absolute; visibility:hidden; z-index:1000;\"></DIV>");
			$divInserted = TRUE;
		}
		$_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/overlib/overlib.js");
		return "onMouseOver='return overlib(\"" . $caption . "\"" . ($argumentList != '' ? ',' . $argumentList : '') . ");' onMouseOut='return nd();'";
	}	

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::alert
	// @desc		Imprime um 'alert' JavaScript
	// @access		public
	// @param		msg string			Mensagem para o alert
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function alert($msg) {
		echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">alert(\"", $msg, "\");</SCRIPT>";
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::confirm
	// @desc		Imprime um di�logo 'confirm' JavaScript
	// @access		public
	// @param		msg string			Mensagem para a caixa de di�logo
	// @param		trueAction string	"" A��o para retorno TRUE do usu�rio
	// @param		falseAction string	"" A��o para retorno FALSE do usu�rio
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function confirm($msg, $trueAction = '', $falseAction = '') {
		$confirm = "";
		if ($trueAction != "") {
			$confirm .= "<SCRIPT TYPE=\"text/javascript\">\n";
			$confirm .= "if (confirm(\"$msg\")) {\n";
			$confirm .= $trueAction . "\n";
			$confirm .= "}\n";
			if ($falseAction != "") {
				$confirm .= "else {";
				$confirm .= $falseAction . "\n";
				$confirm .= "}";
			}
			$confirm .= "</SCRIPT>\n";
		} elseif ($falseAction != "") {
			$confirm .= "<SCRIPT TYPE=\"text/javascript\">\n";
			$confirm .= "if (!confirm(\"$msg\")) {\n";
			$confirm .= $falseAction . "\n";
			$confirm .= "}\n";
			$confirm .= "</SCRIPT>\n";
		}
		echo $confirm;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::closeWindow
	// @desc		Fecha a janela atual do browser utilizando JavaScript
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function closeWindow() {
		echo "<SCRIPT LANGUAGE=\"Javascript\" TYPE=\"text/javascript\">if (parent) parent.close(); else window.close();</SCRIPT>\n";
		exit;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::redirect
	// @desc		Redireciona para uma URL usando JavaScript
	// @access		public
	// @param		url string		Url para redirecionamento	
	// @param		object string	"document" Objeto base para o redirecionamento
	// @return		void
	// @see			HtmlUtils::replace
	// @see			HttpResponse::redirect
	// @static	
	//!-----------------------------------------------------------------
	function redirect($url, $object = "document") {
		if ($object[strlen($object)-1] != '.') $object .= ".";
		echo "<SCRIPT LANGUAGE=\"Javascript\" TYPE=\"text/javascript\">", $object, "location.href = \"", $url, "\"</SCRIPT>\n";
		exit;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::replace
	// @desc		Substitui a entrada atual do hist�rico por outra URL
	// @access		public	
	// @param		url string		Url a ser carregada
	// @return		void
	// @see			HtmlUtils::redirect
	// @static	
	//!-----------------------------------------------------------------
	function replace($url) {
		echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">location.replace(\"", $url, "\");</SCRIPT>\n";
		exit;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::refresh
	// @desc		Imprime a tag META de redirecionamento
	// @access		public	
	// @param		url string		Url para redirecionamento
	// @param		time int		"1" Nro. de segundos de espera para o redirecionamento
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function refresh($url, $time = 1) {
		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"", $time, "; URL=", htmlentities($url), "\">";
	}	
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::goBackN
	// @desc		Volta 'n' posi��es no hist�rico do browser
	// @param		n int			"1" N�mero de posi��es para retornar
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function goBackN($n = 1) {
		$n = ($n > 0) ? TypeUtils::parseString($n) : "1";
		echo "<SCRIPT LANGUAGE=\"Javascript\" TYPE=\"text/javascript\">history.go(-", $n, ")</SCRIPT>\n";
		exit;
	}
	
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::focus
	// @desc		Gera um script JavaScript para requisitar foco
	// 				em um campo de um formul�rio
	// @param		form string		Nome do formul�rio
	// @param		field string	Nome do campo
	// @param		object string	"" Objeto base para a fun��o
	// @param		return bool		"FALSE" Indica que o c�digo JavaScript deve ser retornado e n�o impresso
	// @return		mixed	Retorna o c�digo JavaScript se $return == TRUE. Do contr�rio, retorna TRUE
	// @static
	//!-----------------------------------------------------------------
	function focus($form, $field, $object = "", $return=FALSE) {
		if ($object != '')
			$object .= '.';
		$strScript = 
			"\n<SCRIPT TYPE=\"text/javascript\">\n" . 
			"var obj = eval(\"" . $object . "document.forms['" . $form . "']\");\n" . 
			"if (obj) requestFocus(obj, \"$field\");\n" . "</SCRIPT>\n";
		if ($return) {
			return $strScript;
		} else {
			echo $strScript;
			return TRUE;
		}
	}
}
?>