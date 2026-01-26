<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'marketplaces.class',
    'marketplacesfilter.class',
    'marketplacedocumentationmapping.class',
    'marketplacedocumentationmappingfilter.class',
]);
class Page extends BasePage {
/*     * *
 * Controller logic
 */
public $user = null;
public $marketplace_id;
private $marketplace_documentation_data;
public $marketplaceObj;
protected function init()
{
    $mp_id = util_get("mp");
    $mpObj = new MarketPlacesFilter();
    $mpObj->addFieldFilter('        plugin_key', $mp_id);
    $mpObj = $mpObj->getList();
    if (!empty($mpObj)) {
        $this->marketplaceObj = $mpObj[0];
        $this->marketplace_id = $this->marketplaceObj->getId();
        $marketplaceDocumentationObj = new MarketPlaceDocumentationMappingFilter();
        $marketplaceDocumentationObj->addFilter('marketplace_id = ' . $this->marketplace_id);
        $marketplaceDocumentationObj->orderBy('step_order', 'ASC');
        $marketplaceDocumentationObj = $marketplaceDocumentationObj->getList();
        if (!empty($this->marketplaceObj) && !empty($marketplaceDocumentationObj)) {
            $this->marketplace_documentation_data['title'] = $this->marketplaceObj->getDocumentationTitle();
            $this->marketplace_documentation_data['cover_image'] = $this->marketplaceObj->getDocumentationCoverImage();
            if (!empty($marketplaceDocumentationObj)) {
                $i = 0;
                foreach ($marketplaceDocumentationObj as $marketplaceD) {
                    $this->marketplace_documentation_data['steps'][$i]['step_title'] = $marketplaceD->getStepTitle();
                    $this->marketplace_documentation_data['steps'][$i]['step_image'] = $marketplaceD->getStepImage();
                    $this->marketplace_documentation_data['steps'][$i]['step_description'] = $marketplaceD->getStepDescription();
                    $i++;
                }
            }
        }
    }
    $this->user = SessionManager::getUser();
    $this->breadCrumb['data'] = array(
        'index.php' => Translation::GetCaption("HOME"),
        "Marketplace Documentation"
    );
}
/**
 * Page-specific buttons
 */
protected function renderFooter()
{
    ?>
    <script type="text/javascript">
        /*!
       * jQuery FancyZoom Plugin
       * version: 1.0.1 (20-APR-2014)
       * @requires jQuery v1.6.2 or later
       *
       * Examples and documentation at: http://github.com/keegnotrub/jquery.fancyzoom/
       * Licensed under the MIT license:
       *   http://www.opensource.org/licenses/mit-license.php
       */
        !function (a) {
            a.extend(jQuery.easing, {
                easeInOutCubic: function (a, b, c, d, e) {
                    return (b /= e / 2) < 1 ? d / 2 * b * b * b + c : d / 2 * ((b -= 2) * b * b + 2) + c
                }
            }), a.fn.fancyZoom = function (b) {
                function e(b) {
                    b.childNodes.length > 0 && (b = b.childNodes[0]);
                    var c = a(b), d = c.offset().left, e = c.offset().top, f = c.width() || 50, g = c.height() || 12;
                    return {left: d, top: e, width: f, height: g}
                }

                function f() {
                    var b = a(window), c = b.width(), d = b.height(), e = b.scrollLeft(), f = b.scrollTop();
                    return {width: c, height: d, scrollX: e, scrollY: f}
                }

                function g(b) {
                    function r(a) {
                        g ? (p.css("backgroundPosition", "0px " + -50 * j + "px"), j = (j + 1) % 12) : (clearInterval(i), i = 0, j = 0, p.hide(), t(a))
                    }

                    function s(a) {
                        p.css({
                            left: l.width / 2 + l.scrollX + "px",
                            top: l.height / 2 + l.scrollY + "px",
                            backgroundPosition: "0px 0px",
                            display: "block"
                        }), j = 0, i = setInterval(function () {
                            r(a)
                        }, 100)
                    }

                    function t(b) {
                        if (d) return !1;
                        d = !0, n.attr("src", b.getAttribute("href"));
                        var e = h.width, f = h.height, g = e / f;
                        e > l.width - c.minBorder && (e = l.width - c.minBorder, f = e / g), f > l.height - c.minBorder && (f = l.height - c.minBorder, e = f * g);
                        var i = l.height / 2 - f / 2 + l.scrollY, j = l.width / 2 - e / 2 + l.scrollX;
                        o.hide(), m.hide().css({
                            left: k.left + "px",
                            top: k.top + "px",
                            width: k.width + "px",
                            height: k.height + "px",
                            opacity: "hide"
                        }), m.animate({
                            left: j + "px",
                            top: i + "px",
                            width: e + "px",
                            height: f + "px",
                            opacity: "show"
                        }, 200, "easeInOutCubic", function () {
                            o.fadeIn(), o.click(u), m.click(u), a(document).keyup(v), d = !1
                        })
                    }

                    function u() {
                        return d ? !1 : (d = !0, o.hide(), m.animate({
                            left: k.left + "px",
                            top: k.top + "px",
                            opacity: "hide",
                            width: k.width + "px",
                            height: k.height + "px"
                        }, 200, "easeInOutCubic", function () {
                            d = !1
                        }), m.unbind("click", u), o.unbind("click", u), a(document).unbind("keyup", v), void 0)
                    }

                    function v(a) {
                        27 == a.keyCode && u()
                    }

                    var k, l, m, n, o, p, c = b, d = !1, g = !1, h = new Image, i = 0, j = 0, q = this;
                    m = a("#zoom"), 0 === m.length && (m = a(document.createElement("div")), m.attr("id", "zoom"), a("body").append(m)), n = a("#zoom_img"), 0 === n.length && (n = a(document.createElement("img")), n.attr("id", "zoom_img"), m.append(n)), o = a("#zoom_close"), 0 === o.length && (o = a(document.createElement("div")), o.attr("id", "zoom_close"), m.append(o)), p = a("#zoom_spin"), 0 === p.length && (p = a(document.createElement("div")), p.attr("id", "zoom_spin"), a("body").append(p)), this.preload = function () {
                        var c = this.getAttribute("href");
                        h.src !== c && (g = !0, h = new Image, a(h).load(function () {
                            g = !1
                        }), h.src = c)
                    }, this.show = function (a) {
                        l = f(), k = e(this), q.preload.call(this, a), g ? 0 === i && s(this) : t(this), a.preventDefault()
                    }
                }

                var c = a.extend({minBorder: 90}, b), d = new g(c);
                this.each(function () {
                    var c = a(this);
                    c.mouseover(d.preload), c.click(d.show)
                })
            }
        }(jQuery);
        $(document).ready(function () {
            $("a").fancyZoom();
        });
    </script>
    <style>
        .page-breadcrumb {
            display: none
        }

        @media (min-width: 992px) {
            .page-content-wrapper .page-content {
                padding: 0px 0 0 20px;
            }
        }
    </style>
    <?php
}
/*     * *
 * Content View
 */
protected function renderBody() {
// transfer form variables into local values (form variables come from parent)
?>
<form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="portlet light">
        <div class="portlet-title" style="margin-bottom: 0">
            <div class="caption"><i class="fa fa-question-circle"></i>
                Help
            </div>
            <div class="actions">
                <input type="button" class="btn blue" value="Back" onclick="history.back(-1)"/>
                <!--                <button id="backLink" onclick="history.go(-2)" class="btn blue"><span></span><i class="fa fa-chevron-left"-->
                <!--                                                                          aria-hidden="true"></i> &nbsp;Back</button>-->
                <!--                <a href="javascript:;" class="btn btn-primary show_audit" title="audit"> WooCommerce</a>-->
            </div>
        </div>
        <?php if (!empty($this->marketplace_documentation_data)): ?>
            <div class="row mb-4" style="margin-left: -20px; margin-right: -20px">
                <?php if (!empty($this->marketplace_documentation_data['cover_image'])): ?>
                    <img src="../images/marketplace_documentation/<?php echo !empty($this->marketplace_documentation_data['cover_image']) ? $this->marketplace_documentation_data['cover_image'] : ''; ?>"
                         class="img-responsive"
                         alt="<?php echo !empty($this->marketplace_documentation_data['title']) ? $this->marketplace_documentation_data['title'] : 'Step by step guide to connect your ' . strtolower($this->marketplaceObj->getTitle()) . ' store to smarttrack'; ?>"
                         style="width:100%; height: 234px;">
                <?php endif; ?>

                <div class="help-sub-heading">
                    <h3>
                        <center><?php echo !empty($this->marketplace_documentation_data['title']) ? $this->marketplace_documentation_data['title'] : 'Step by step guide to connect your ' . strtolower($this->marketplaceObj->getTitle()) . ' store to smarttrack'; ?></center>
                    </h3>
                </div>

            </div>
            <div class="portlet-body">
                <div class="container1">
                    <!--
                     -->
                    <div class="row1">
                        <div class="col-md-4">
                            <!-- begin panel group -->
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <!-- panel 1 -->
                                <?php if (!empty($this->marketplace_documentation_data['steps'])): ?>
                                    <?php foreach ($this->marketplace_documentation_data['steps'] as $key => $steps): ?>
                                        <div class="panel panel-default">
                                            <!--wrap panel heading in span to trigger image change as well as collapse -->
                                            <span class="side-tab" data-target="#tab<?php echo $key + 1; ?>"
                                                  data-toggle="tab" role="tab"
                                                  aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="heading<?php echo $key + 1; ?>"
                                                 data-toggle="collapse"
                                                 data-parent="#accordion" href="#collapse<?php echo $key + 1; ?>"
                                                 aria-expanded="true"
                                                 aria-controls="collapse<?php echo $key + 1; ?>">
                                                <h4 class="panel-title"><?php echo !empty($steps['step_title']) ? $steps['step_title'] : ''; ?></h4>
                                            </div>
                                        </span>
                                            <div id="collapse<?php echo $key + 1; ?>"
                                                 class="panel-collapse collapse <?php echo($key == 0 ? 'in' : ''); ?>"
                                                 role="tabpanel"
                                                 aria-labelledby="heading<?php echo $key + 1; ?>">
                                                <div class="panel-body">
                                                    <?php echo !empty($steps['step_description']) ? $steps['step_description'] : ''; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div> <!-- / panel-group -->
                        </div> <!-- /col-md-4 -->
                        <div class="col-md-8">
                            <!-- begin macbook pro mockup -->
                            <div class="md-macbook-pro md-glare">
                                <div class="md-lid">
                                    <div class="md-camera"></div>
                                    <div class="md-screen">
                                        <!-- content goes here -->
                                        <div class="tab-featured-image">
                                            <div class="tab-content">
                                                <?php if (!empty($this->marketplace_documentation_data['steps'])): ?>
                                                    <?php foreach ($this->marketplace_documentation_data['steps'] as $key => $steps): ?>
                                                        <div class="tab-pane <?php echo($key == 0 ? 'in active' : ''); ?>"
                                                             id="tab<?php echo $key + 1; ?>">
                                                            <a href="images/marketplace_documentation/<?php echo !empty($steps['step_image']) ? $steps['step_image'] : ''; ?>"><img
                                                                        width="550px" height="400px"
                                                                        src="images/marketplace_documentation/<?php echo !empty($steps['step_image']) ? $steps['step_image'] : ''; ?>"/></a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="md-base"></div>
                            </div> <!-- end macbook pro mockup -->
                        </div> <!-- / .col-md-8 -->
                    </div>
                    <!--/ .row -->
                </div> <!-- end sidetab container -->
                <br clear="all">
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-sm-12">
                    <h4>Documentation Coming Soon.</h4>
                </div>
            </div>
        <?php endif; ?>
        <?php
        }
        /**
         * Override to show the menu
         *
         */
        public function renderMenu()
        {
            $menu = new Adminmenu(Adminmenu::CUSTOMERS);
            $menu->render();
        }
        }
        // class
        /* ------------------------------------------------------------------------------ */
        // create and render page
        $page = new Page(CONFIG_TEMPLATE_ADMIN);
        $page->show();
        ?>
