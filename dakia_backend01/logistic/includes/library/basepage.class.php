<?php

/**
 * Standard page template class
 *
 * Version 2 - Class name unchanged, choice between version determined by the include options.
 *
 */
class BasePage {

    // Array of posted values
    protected $form_vars;
    // Page heading, i.e. rendered in <h1> tags.
    private $heading = "";
    // Page title (in html head section)
    private $page_title = "";
    // The HTML meta description
    private $meta_description = CONFIG_META_DESCRIPTION;
    //The HTML meta keywords
    private $meta_keywords = CONFIG_META_KEYWORDS;
    // Identifies the html template to use
    private $template = "";
    // Template folder
    private static $folder = "../includes/templates/pages/";
    // Read post array flag
    private static $read_post_array = true;
    // changes CSS for Menu Link for Body Tag
    private static $menu_link = "";
    protected   $breadCrumb  =   array();
    protected $flashMsg;
    /**
     * Constructor - sets the page up
     * @return void
     */
    public function __construct($template) {
        $this->flashMsg = new FlashMessages();
        $this->template = $template;
        $this->sanitizeInputs();
//        Get current page name
        $pageName = basename($_SERVER['PHP_SELF']);
//        Check if page exsist in permissions array
//        if(Permissions::checkFilePermission($pageName)){
//             //Allowed page 
//        }else{
//            //Redirect to 401 page
//             util_redirect("401.php");
//        }
        if(!in_array($pageName , array('signup.php','account_signup.php', 'login.php' ,'verify_email_token.php' , 'multitracking.php',  'tracking.php', 'login-viva.php', 'login-ukmail.php','login-spar.php', 'login-rt.php','login-deutchepost.php','login-epostg.php','terms_conditions.php','legal_disclosure.php','change_dropofflocation.php','quotation_pay.php'))) {
            if(!isset($_POST['login_check']) && @$_POST['login_check'] != 1) {
                $UserSessionManager = SessionManager::getUser();
                $userAccountId = $UserSessionManager->getUserAccountId();
                if ($userAccountId < 1) {
                    echo "<script> window.location.href = 'login.php'; </script>";
                    die;
                }
            }
        }
        
    }

    /**
     * Allows page template folder to be set
     * - only set if default folder not being used.
     *
     * @param string $theTemplateFolder
     */
    public static function setTemplateFolder($theTemplateFolder) {
        self::$folder = $theTemplateFolder;
    }

    /**
     * By default post values are cleaned and read in to the form_vars array.
     * This enables this functionatlity to be turned on/off.
     *
     * @param bool $enabled_flag
     */
    public static function setPostArrayRead($enabled_flag) {
        self::$read_post_array = $enabled_flag;
    }

    /**
     * Controller and view initialise
     */
    protected function init() {
        
    }

    /**
     * Render the page
     * @return void
     */
    public function show() {
        // read form variables
        //var_dump(self::$read_post_array);
        //die;
        //if (self::$read_post_array) 
        //$this->form_vars = util_getPostArray();
        $this->sanitizeInputs();
        $this->form_vars = $_POST;
        // initialise view
        $this->init();

        $this->setLanguage();
        //
        try {
            // output HTML
            require_once(self::$folder . $this->template . ".php");
        } catch (Exception $e) {
            echo "Error displaying page. Error " . $e->getMessage();
            die;
        }
    }

    /**
     * Set title displayed within main page (within HTML).
     *
     * @param string $title
     */
    protected function setTitle($title) {
        $this->heading = $title;
    }

    /**
     * Page Title
     *
     * @return string
     */
    protected function getPageTitle() {
        // determine <head> section title
        $pageTitle = $this->page_title;
        if ($pageTitle == "") {
            //
            $pageTitle = CONFIG_HEAD_TITLE_PREFIX;
            if ($this->heading != "")
                $pageTitle .= " - " . $this->heading;
        }
        return $pageTitle;
    }

    /**
     * Methods to set meta details and page title
     *
     * @param string
     */
    protected function setPageTitle($title) {
        $this->page_title = $title;
    }

    protected function setMetaKeyWords($keywords) {
        $this->meta_keywords = $keywords;
    }

    protected function setMetaDescription($description) {
        $this->meta_description = $description;
    }

    protected function setMenuLink($menulink) {
        $this->menu_link = $menulink;
    }

    ////////////////////////////////////////////////////
    // Render methods - for overriding.
    ////////////////////////////////////////////////////

    protected function renderHead() {
        
    }

    protected function renderBody() {
        
    }

    protected function renderFooter() {
		DbAccess3::closeConnection();?>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © Logistic.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Design & Develop by Myth Solutions
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }

    //
    protected function renderAdditional() {
        
    }

    protected function renderLeftColumn() {
        
    }

    protected function renderRightColumn() {
        
    }
    
    protected function addPagelavelCss() {
        
    }
    protected function addPagelavelJs() {
        
    }

    /**
     * Render meta tags
     *
     */
    protected function renderMetaTags() {
        ?>
        <title><?php echo $this->getPageTitle(); ?></title>
        <?php $this->renderSearchMetaTags(); ?>
        <meta name="rating" content="general" />
        <meta name="author" content="IT TEAM" />
        <meta name="language" content="en" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="imagetoolbar" content="no" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <link rel="shortcut icon" href="/images/favicon.ico">
        <?php
    }

    /*     * *
     * Render meta tags used by search engines
     */

    protected function renderSearchMetaTags() {
        ?>
        <meta name="description" content="<?php echo $this->meta_description; ?>" />
        <meta name="keywords" content="<?php echo $this->meta_keywords; ?>" />
        <meta name="robots" content="all,index,follow" />
        <meta name="distribution" content="global" />
        <?php
    }

    /**
     * The title should normally be set using the "setTitle" method
     * within the init method.
     * This method may be overridden ONLY when there is a need to
     * vary the title layout from standard.
     */
    public function renderTitle() {
        if ($this->heading != "") {
            ?>
            <h1><?php echo $this->heading ?></h1>
            <?php
        }
    }

    public function setLanguage() {
        //mail("mkazim4u@gmail.com", "set language", $_REQUEST['lang']);
        if (isset($_REQUEST['lang']) && $_REQUEST['lang'] != '') {
            $_SESSION['lang'] = DbAccess3::escape($_REQUEST['lang']);
            Translation::PopulateKeywordsArray();
        }
    }

    public function renderMenuLink() {

        echo $this->menu_link;
    }

    public function sanitizeInputRecursive($input=[]){
        if(is_array($input)){
            foreach ($input as $k => $v) {
                if (is_array($v)) {
                    $input[$k] = $this->sanitizeInputRecursive($v);
                } else {
                    $v = @preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $v);
                    $v = @preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $v);
                    $input[$k] = $v;
                }
            }
        }else{
            $input = @preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $input);
            $input = @preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $input);
        }
        return $input;
    }

    public function sanitizeInputs() {
        if (!empty($_POST)) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->sanitizeInputRecursive($v);
                /*if (!is_array($v)) {
                    $v = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $v);
                    $v = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $v);
                    $_POST[$k] = $v; //htmlentities(strip_tags($v,'<p>'));
                } else {
                    foreach ($v as $k1 => $v1) {
                        $v1 = @preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $v1);
                        $v1 = @preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $v1);
                        $v[$k1] = $v1; //htmlentities(strip_tags($v,'<p>'));
                    }
                    $_POST[$k] = $v;
                }*/
            }
        }
        if (!empty($_GET)) {
            foreach ($_GET as $k => $v) {
                $_GET[$k] = $this->sanitizeInputRecursive($v);
                /*if (!is_array($v)) {
                    $v = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $v);
                    $v = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $v);
                    $_GET[$k] = $v; //htmlentities(strip_tags($v,'<p>'));
                } else {
                    foreach ($v as $k1 => $v1) {
                        $v1 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $v1);
                        $v1 = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $v1);
                        $v[$k1] = $v1; //htmlentities(strip_tags($v,'<p>'));
                    }
                    $_GET[$k] = $v;
                }*/
            }
        }
        if (!empty($_REQUEST)) {
            foreach ($_REQUEST as $k => $v) {
                $_REQUEST[$k] = $this->sanitizeInputRecursive($v);
            }
        }
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }
    
     public function renderBreadcrumb() {
        $breadCrumb    =   $this->breadCrumb;
        
        
        $output =   '';
       
        $breadCrumb['li-class']     =   (trim(@$breadCrumb['li-class'])!='')?@$breadCrumb['li-class']:'breadcrumb-item';
        $breadCrumb['a-class']      =   (trim(@$breadCrumb['a-class'])!='')?@$breadCrumb['a-class']:'';
        $breadCrumbData             =   (empty($breadCrumb['data']))?array( 'index.php' => 'home'):$breadCrumb['data'];
        if(count(@$breadCrumbData)>0)
        {
            $output         .=   '<ul class="breadcrumb m-0">';
            $countIndex     =   0;
            foreach($breadCrumbData as $index=>$value)
            {
                if(is_numeric($index))
                    $href   =   'javascript:;';
                else
                    $href   =   $index;
                $output     .=      '<li class="'.@$breadCrumb['li-class'].' '. (((count($breadCrumbData)-1) == $countIndex)?'active':''). '">';
                if(((count($breadCrumbData)-1) == $countIndex))
                {
                    $output     .=          ucfirst(strtolower($value));
                }
                else
                {
                    $output     .=      '   <a href="'.$href.'">'.ucfirst(strtolower($value)).'</a>';
                }
                $output     .=      '</li>';
                $countIndex++;
            }
            $output     .= '</ul>';
        }
        echo $output;
    }
    
       protected function auditLogs(){
           //get Audit Log
            if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_audit_log') {
                $output = "";
                $id     = $this->form_vars['id'];
                $table  = $this->form_vars['table'];
                $ajaxPage  = $this->form_vars['ajaxUrl'];
                echo AuditLogs::getAuditDetails($table,$id,$ajaxPage);
                exit;
            }

            // Get Log Details
            if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_log_details') {
                $cid    = $this->form_vars['cid'];
                $table  = $this->form_vars['table'];
                echo AuditLogs::getAuditItemDetails($table,$cid);
                exit;
            }
       }
    
}
?>
