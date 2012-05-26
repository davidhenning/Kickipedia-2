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

    protected $_iPerPage = 50;

    /**
     * Current page
     * @var integer
     */

    protected $_iCurrentPage = 1;

    /**
     * Base path for generated urls
     * @var string
     */

    protected $_sPaginationBaseUrl = '';

    /**
     * Additional path for generated urls
     * @var string
     */

    protected $_sPaginationAdditionalUrl = '';

    /**
     * Additional get parameters for generated urls
     * @var array
     */

    protected $_aPaginationAdditionalParameters = array();

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
     * Output method
     * @var string
     */

    protected $_sOutputMethod = 'template';


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
     * @param string $sId
     */

    public function setOutputMethod($sOutputMethod) {
        $this->_sOutputMethod = $sOutputMethod;
        $this->addAdditionalUrlParameter('output', $sOutputMethod);
    }

    /**
     * Sets count of documents per page
     *
     * @param integer $iPerPage
     */

    public function setPerPage($iPerPage) {
        $this->_iPerPage = $iPerPage;
    }

    /**
     * Sets current page number
     *
     * @param integer $iPage
     */

    public function setCurrentPage($iPage) {
        $this->_iCurrentPage = $iPage;
    }

    /**
     * Adds an get parameter to generated urls
     *
     * @param string $sName
     * @param string $sValue
     */

    public function addAdditionalUrlParameter($sName, $sValue) {
        $this->_aPaginationAdditionalParameters[$sName] = $sValue;
    }

    /**
     * Create and returns array with all pagination data
     *
     * @return array
     */

    public function getPagination() {
        if($this->_aPagination === null) {    
            // compute total pages
            $iPages = ceil($this->_iTotalDocuments / $this->_iPerPage);
            
            if($iPages > 1) {
                // init array of the pagination
                $aPages = array(
                    'pages' => array(),
                    'currentPage' => $this->_iCurrentPage,
                    'documentsPerPage' => $this->_iPerPage,
                    'totalPages' => $iPages
                );

                // set URL to previous page and first page
                if($this->_iCurrentPage > 1) {
                    $aPages['prevPageUrl'] = $this->_createPageUrl($this->_iCurrentPage - 1);
                    $aPages['firstPageUrl'] = $this->_createPageUrl(1);
                }

                // set URL to next page and last page
                if($this->_iCurrentPage < $iPages) {
                    $aPages['nextPageUrl'] = $this->_createPageUrl($this->_iCurrentPage + 1);
                    $aPages['lastPageUrl'] = $this->_createPageUrl($iPages);
                }

                if($iPages > 0) {
                    
                    // set pages with number, url and active state
                    for($i = 1; $i <= $iPages; $i++) {
                        $aPage = array(
                            'nr' => $i,
                            'url' => $this->_createPageUrl($i)
                        );

                        if($i === $this->_iCurrentPage) {
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
     * Creates and returns a page url for given page number
     *
     * @param integer $iPage
     * @return string
     */

    protected function _createPageUrl($iPage) {
        $sUrl = "{$this->_sPaginationBaseUrl}/";
        
        if(!empty($this->_sPaginationAdditionalUrl)) {
            $sUrl .= "{$this->_sPaginationAdditionalUrl}/";
        }

        $sUrl .= $iPage;

        if(!empty($this->_aPaginationAdditionalParameters)) {
            $sUrl .= '?'.http_build_query($this->_aPaginationAdditionalParameters);
        }

        return $sUrl;
    }

    /**
     * Loads Twig and starts page rendering
     */

    public function render() {
        if($this->_sOutputMethod == 'template') {
            $this->_renderTwig();
        } elseif($this->_sOutputMethod == 'json') {
            header('Content-type: application/json');
            $this->_renderJSON();
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
}