<?php  // $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
/**
 * This page prints a particular instance of jeliot
 *
 * @author
 * @version $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
 * @package jeliot
 **/

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a', 0, PARAM_INT);  // jeliot ID

    if ($id) {
        if (! $cm = $DB->get_record("course_modules", array('id' => $id))) {
            error("Course Module ID was incorrect");
        }

        if (! $course = $DB->get_record("course", array('id' => $cm->course))) {
            error("Course is misconfigured");
        }

        if (! $jeliot = $DB->get_record("jeliot", array('id' => $cm->instance))) {
            error("Course module is incorrect");
        }

    } else {
        if (! $jeliot = $DB->get_record("jeliot", array('id' => $a))) {
            error("Course module is incorrect");
        }
        if (! $course = $DB->get_record("course", array('id' => $jeliot->course))) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("jeliot", $jeliot->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

	$params = array();
	$params['id'] = $id;
	$PAGE->set_url('/mod/jeliot/view.php', $params);

    require_login($course->id);

	$event = \mod_jeliot\event\course_module_viewed::create(array('objectid' => $jeliot->id, 'context' => context_module::instance($cm->id)));
	$event->trigger();

    //add_to_log($course->id, "jeliot", "view", "view.php?id=$cm->id", "$jeliot->id");

/// Print the page header
    $strjeliots = get_string("modulenameplural", "jeliot");
    $strjeliot  = get_string("modulename", "jeliot");

	$title = $course->shortname." : ".$jeliot->name;
	$PAGE->set_title($title);
	$PAGE->set_heading($course->fullname);

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    $linkCSS = "<link rel=\"stylesheet\" type=\"text/css\" href=\"jeliot.css\" />";
    //print_header("$course->shortname: $jeliot->name", "$course->fullname",
    //             "$navigation <a href=index.php?id=$course->id>$strjeliots</a> -> $jeliot->name",
    //              "", $linkCSS, true, update_module_button($cm->id, $course->id, $strjeliot),
    //              navmenu($course, $cm));
    echo $OUTPUT->header();
	echo $OUTPUT->heading($jeliot->name);

/// Print the main part of the page
?>  

    <div id="jeliot_description">
      <p>
        <?php echo $jeliot->intro; ?>
      </p>
    </div>
    <div id="jeliot_link">  
	<!--<?php echo $course->id."**".$jeliot->sourcefile;?>-->
	
   
    <?php 
        $code = jeliot_return_sourcefile($course, $jeliot);
        echo "<pre>".$code."</pre>";
        $baseurl = "http://localhost/java_visualize/#mode=display&curInstr=0&code=";
        $baseurl = $baseurl.urlencode($code);
        echo "<a title='Visualize Online!' target='_blank' href='".$baseurl."'><button>Visualize Online!</button></a>";
        
    ?>
	 <a title="Start Jeliot!" href="<?php echo jeliot_create_JNLP_link($course, $jeliot);?>">
         <!-- <img src="logo3d32.png" title="Start Jeliot 3" height="32" width="32" alt="Jeliot 3 logo"/> -->
         <br/><button> Visualize in Jeliot</button></a><br/>  


    </div>
<?php
/// Finish the page
    print_footer($course);
?>
