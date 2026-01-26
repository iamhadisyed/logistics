<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    // consignment filter
    private $notes_list;
    private $notes_filter;
    private $errormsg;
    // table description
    private $table_msg;
    private $num_valid; // number of valid consignments.
    private $column_name;
    private $sortby;
    private $flag;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // user must be CLIENT
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        $user = SessionManager::getUser();

        if (isset($_POST['action']) && trim($_POST['action']) == "SAVE_NOTES") {

            $id = $_POST["id"];
            $notes_description = $_POST["notes_description"];

            if (isset($notes_description) && $notes_description != "") {
                if ($id != '') {
                    $CSNotes = new CSNotes($id);
                } else {
                    $CSNotes = new CSNotes();
                }
                $CSNotes->setNotes($notes_description);
                $CSNotes->setDateCreated(time());
                $CSNotes->setCreatedBy($user->getUserAccount());
                $CSNotes->save();
                echo "Notes Added Successfully";
                die;
            } else {
                echo "Please enter Notes.";
                die;
            }
        }
        if (isset($_POST['action']) && trim($_POST['action']) == "EDIT_NOTES_DATA") {

            $CSNotes = new CSNotes($_POST["id"]);


            if (sizeof($CSNotes) > 0) {
                $responceArray = array();
                $responceArray['DESCRIPTION'] = $CSNotes->getNotes();
                echo json_encode($responceArray);
                die;
            }
        }



        if (isset($this->form_vars["form_action"]) && trim($this->form_vars["form_action"]) == "search") {
            echo $search = $this->form_vars["searchnotes"];
            $this->notes_filter = new CSNotesFilter();
            $this->notes_filter->addFieldLikeFilter("notes", $search);
            $this->notes_list = $this->notes_filter->getList();
        } else {

            $this->notes_filter = new CSNotesFilter();
            $this->notes_list = $this->notes_filter->getList();
        }

        // common initialisation for ths page
        $this->setTitle("Notes");

        // keep session copies of class variables
        $_SESSION["notes_filter"] = $this->notes_filter;
        $_SESSION["client_table_msg"] = $this->table_msg;

        // Check message
        if ($this->table_msg == "")
            $this->table_msg = "Notes Listed";
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <script src="../assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>   
        <script type="text/javascript">

            $(document).ready(function () {


                $("#btnSearch").click(function () {
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });



            });

            function AddNewNotes()
            {
                document.getElementById("adminForm").reset();
            }

            function editNotes(id)
            {
                document.getElementById("notes_id").value = id;
                $.post(
                        "notes_list.php",
                        {action: 'EDIT_NOTES_DATA', id: id},
                        function (data)
                        {
                            if ($.type(data) === 'object') {
                                document.getElementById("notes_description").value = data.DESCRIPTION;
                            }
                        }, "json");
            }

            function saveNotes()
            {
                var id = document.getElementById("notes_id").value;
                var notes_description = document.getElementById("notes_description").value;

                $.post(
                        "notes_list.php",
                        {action: 'SAVE_NOTES', id: id, notes_description: notes_description},
                        function (data)
                        {
                            alert(data)
                            $('#myModalNotes').modal('hide')
                            window.location.href = 'notes_list.php';

                        });
            }







        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>

        <style>
            .row{
                margin-bottom:15px;
            }
        </style>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                    NOTES
                </div>
                <div class="tools"> 
                    <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> 
                </div>
            </div>
            <div class="portlet-body">


        <?php ErrorList::getItem()->render(); ?>
                <div class="row">
                    <div class="col-md-4">
                        <input type="button" class="btn btn-primary" value="ADD NOTES" title="Add Notes" name='btnaddNotes' onclick="AddNewNotes()" data-toggle="modal" data-target="#myModalNotes"/>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control"  value="<? echo $this->form_vars["searchnotes"]; ?>" id="searchnotes" title="Search" placeholder="SEARCH NOTES" name="searchnotes" />
                    </div>
                    <div class="col-md-4">
                        <input type="button" class="btn btn-primary" value="SEARCH" title="Search" name="btnSearch" id = "btnSearch" />
                    </div>
                </div>
                <!-- List of Bulletins -->

                <div class="row">
                    <div class="col-md-9">
                    </div>
                    <div class="col-sm-3">
                        <h4><? echo count($this->notes_list); ?> <?php echo "Notes List"; ?></h4>
                    </div>


                    <div class="table-scrollable">

                        <table id='consignment_list' class="table table-striped table-bordered table-advance table-hover">


                            <!-- table head -->
                            <thead>
                                <tr>

                                    <th scope="col" class="red-back">
                                        Date Created
                                    </th>
                                    <th scope="col" class="red-back">
                                        Notes
                                    </th> 
                                    <th scope="col" class="red-back">
                                        Created By
                                    </th> 
                                    <th scope="col" class="red-back">
                                        Action
                                    </th> 
                                </tr>
                            </thead>



                            <!-- table body -->

                            <tbody>
                                <?
                                if(count($this->notes_list) > 0)
                                {
                                foreach ($this->notes_list as $notes)
                                {    
                                $id= $notes->getId();  

                                ?>
                                <tr>
                                    <td><?php echo date("Y-m-d", ($notes->getDateCreated())); ?> </td>
                                    <td><?php echo $notes->getNotes(); ?> </td>
                                    <td><?php echo $notes->getCreatedBy(); ?> </td>

                                    <td> 
                                        <button type="button" class="btn btn-primary" id="edit_notes" onclick="editNotes(<? echo $id; ?>);" data-toggle="modal" data-target="#myModalNotes">Edit Notes</button>

                                    </td>
                                </tr>
        <?php
    }

} else {
    ?>
                            <tr>
                                <td colspan="4">No Records Found</td>
                            </tr>
                            <?
                            }
                            ?>
                        <input type="hidden" id = "notes_id" name="notes_id" value="<?php echo $notes_id; ?>" />
                        </tbody>

                    </table>
                </div>  

                <!-- ADD NEW BULLETIN POP UP --> 

                <div class="modal fade" id="myModalNotes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">ADD NEW NOTES</h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?
                                        if($this->errormsg != "") 
                                        {
                                        ?>
                                        <div class="alert alert-success hidden" id="error_msg"></div>
                                        <?
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label font-green-soft"><strong>Note</strong></label>
                                            <textarea required data-provide="markdown" class="form-control"  rows="7" cols="150" name="notes_description" placeholder="<?php echo "Notes"; ?>" id="notes_description" rel="tooltip" data-original-title="<?php echo "Notes" ?>" data-placement="bottom"><? echo $this->form_vars["notes_description"]; ?></textarea>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" id = "add_notes" class="btn btn-default" onclick="saveNotes();">Save</button>
                                <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </div>
                </div>        
                <br clear="all"/>


            </div>  <!-- table_container -->
        </div>
    </div>

    <input type="hidden" name="form_action" id="form_action" value="" /> 
    <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
