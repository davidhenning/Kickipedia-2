<?php

/**
 * Class View
 *
 * Basic view functions
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit;

class View extends Base {

    /**
     * Name of the App built with MongoAppKit (used template path)
     * @var string
     */
    
    protected $_sAppName = '';
 
    /**
     * Name of the template to render
     * @var string
     */

    protected $_sTemplateName = null;

    /**
     * Template data for rendering
     * @var array
     */

    protected $_aTemplateData = array();

    /**
     * Id of the view related document
     * @var string
     */

    protected $_sId = null;

    /**
     * Documents per page
     * @var integer
     */

    protected $_iDocumentLimit = 100;

    /**
     * Skipped documents
     * @var integer
     */

    protected $_iSkippedDocuments = 0;

    /**
     * Current page
     * @var integer
     */

    protected $_iCurrentPage = 1;

    /**
     * Base path for generated urls
     * @var string
     */

    protected $_sBaseUrl = '';

    /**
     * Additional path for generated urls
     * @var string
     */

    protected $_sPaginationAdditionalUrl = '';

    /**
     * Additional get parameters for generated urls
     * @var array
     */

    protected $_aAdditionalUrlParameters = array();

    /**
     * Total count of documents for pagination
     * @var integer
     */

    protected $_iTotalDocuments = 0;

    /**
     * Pagination array
     * @var array
     */

    protected $_aPagination = null;

    /**
     * Current output format
     * @var string
     */

    protected $_sOutputFormat = 'html';

    /**
     * Allowed output format
     * @var string
     */

    protected $_aAllowedOutputFormats = array('html', 'json', 'xml');

    /**
     * Sets id if given
     *
     * @param string $sId
     */

    public function __construct($sId = null) {
        if($sId !== null) {
            $this->setId($sId);
        }
    }

    /**
     * Returns id
     *
     * @return string
     */

    public function getId() {
        return $this->_sId;
    }

    /**
     * Sets id
     *
     * @param string $sId
     */

    public function setId($sId) {
        $this->_sId = $sId;
    }

    /**
     * Sets output method
     *
     * @param string $sOutputFormat
     */

    public function setOutputFormat($sOutputFormat) {
        if(!in_array($sOutputFormat, $this->_aAllowedOutputFormats)) {
            throw new \InvalidArgumentException('Specified output format is unkown');
        }

        $this->_sOutputFormat = $sOutputFormat;
    }

    /**
     * Sets count of documents per page
     *
     * @param integer $iDocumentLimit
     */

    public function setDocumentLimit($iDocumentLimit) {
        $this->_iDocumentLimit = $iDocumentLimit;
    }

    /**
     * Set count of skipped documents
     *
     * @param integer $iSkippedDocuments
     */

    public function setSkippedDocuments($iSkippedDocuments) {
        $this->_iSkippedDocuments = $iSkippedDocuments;
    }

    /**
     * Get current page number
     *
     * @return integer
     */

    public function getCurrentPage() {
        return $this->_iSkippedDocuments / $this->_iDocumentLimit + 1;
    }

    /**
     * Adds an get parameter to generated urls
     *
     * @param string $sName
     * @param string $sValue
     */

    public function addAdditionalUrlParameter($sName, $sValue) {
        $this->_aAdditionalUrlParameters[$sName] = $sValue;
    }

    /**
     * Create and returns array with all pagination data
     *
     * @return array
     */

    public function getPagination() {
        if($this->_aPagination === null) {    
            // compute total pages
            $iPages = ceil($this->_iTotalDocuments / $this->_iDocumentLimit);
            
            if($iPages > 1) {
                // init array of the pagination
                $aPages = array(
                    'pages' => array(),
                    'currentPage' => $this->getCurrentPage(),
                    'documentsPerPage' => $this->_iDocumentLimit,
                    'totalPages' => $iPages
                );

                // set URL to previous page and first page
                if($this->getCurrentPage() > 1) {
                    $aPages['prevPageUrl'] = $this->_createPageUrl($this->getCurrentPage() - 1, $this->_iDocumentLimit);
                    $aPages['firstPageUrl'] = $this->_createPageUrl(1, $this->_iDocumentLimit);
                }

                // set URL to next page and last page
                if($this->getCurrentPage() < $iPages) {
                    $aPages['nextPageUrl'] = $this->_createPageUrl($this->getCurrentPage() + 1 ,$this->_iDocumentLimit);
                    $aPages['lastPageUrl'] = $this->_createPageUrl($iPages, $this->_iDocumentLimit);
                }

                if($iPages > 0) {
                    
                    // set pages with number, url and active state
                    for($i = 1; $i <= $iPages; $i++) {
                        $aPage = array(
                            'nr' => $i,
                            'url' => $this->_createPageUrl($i, $this->_iDocumentLimit)
                        );

                        if($i === $this->getCurrentPage()) {
                            $aPage['active'] = true;
                        }

                        $aPages['pages'][] = $aPage;
                    }
                }

                $this->_aPagination = $aPages;
            }
        }

        return $this->_aPagination;
    }

    /**
     * Get url with given parameters and permanently added parameters
     *
     * @param array $aParams
     * @param string $sBaseUrl
     * @return string
     */

    protected function _createUrl($aParams, $sBaseUrl = null) {
        $sBaseUrl = (!empty($sBaseUrl)) ? $sBaseUrl : $this->_sBaseUrl;
        $sUrl = "{$sBaseUrl}.{$this->_sOutputFormat}";
        $aParams = array_merge($this->_aAdditionalUrlParameters, $aParams);

        if(!empty($aParams)) {
            $sUrl .= '?'.http_build_query($aParams);
        }

        return $sUrl;
    }

    /**
     * Get url for given page number and limit
     *
     * @param integer $iPage
     * @param integer $iLimit
     * @return string
     */

    protected function _createPageUrl($iPage, $iLimit) {
        $aParams = array(
            'skip' => (($iPage - 1) * $iLimit), 
            'limit' => $iLimit
        );

        return $this->_createUrl($aParams);
    }

    /**
     * Loads Twig and starts page rendering
     */

    public function render() {
        if($this->_sOutputFormat == 'html') {
            $this->_renderTwig();
        } elseif($this->_sOutputFormat == 'json') {
            header('Content-type: application/json');
            $this->_renderJSON();
        } elseif($this->_sOutputFormat == 'xml') {
            header('Content-type: text/xml');
            $this->_renderXML();
        } else {
            $this->_renderTwig();
        }
    }

    protected function _renderTwig() {
        // load Twig
        $loader = new \Twig_Loader_Filesystem(getBasePath() ."/{$this->_sAppName}/Templates");
        $twig = new \Twig_Environment($loader, array(
          'cache' => getBasePath() .'/tmp',
          'auto_reload' => $this->getConfig()->getProperty('TemplateDebugMode')
        ));

        // render given template with given data
        echo $twig->render($this->_sTemplateName, $this->_aTemplateData);
    }

    protected function _renderJSON() {
        echo json_encode($this->_aTemplateData);
    }

    protected function _renderXML() {
        $oDocument = new \SimpleXMLElement('<kickipedia></kickipedia>');
        $oHeader = $oDocument->addChild('header');
        $oStatus = $oHeader->addChild('status', 200);

        echo $oDocument->asXML();
    }
}